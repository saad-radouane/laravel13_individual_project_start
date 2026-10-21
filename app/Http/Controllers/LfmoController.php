<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class LfmoController extends Controller
{
    private const LEVELS = ['Beginner', 'Intermediate', 'Advanced'];
    private const MAX = ['5v5' => 10, '7v7' => 14, '11v11' => 22];

    public function index(Request $r)
    {
        $d = $this->data();
        $f = $r->only(['zone', 'date', 'level']);
        $matches = collect($d['matches'])
            ->filter(fn ($m) => ($m['type'] === 'open' || $m['mine'])
                && (empty($f['zone']) || $d['pitches'][$m['pitch']]['zone'] === $f['zone'])
                && (empty($f['date']) || $m['date'] === $f['date'])
                && (empty($f['level']) || $m['level'] === $f['level']))
            ->map(fn ($m) => $this->vm($m, $d));

        return $this->page('matches.index', $d, [
            'matches' => $matches,
            'f' => $f,
            'zones' => collect($d['pitches'])->pluck('zone')->unique()->values(),
            'dates' => collect($d['matches'])->pluck('date')->unique()->values(),
            'levels' => self::LEVELS,
        ]);
    }

    public function show(int $id)
    {
        $d = $this->data();
        abort_unless(isset($d['matches'][$id]), 404);

        return $this->page('matches.show', $d, ['m' => $this->vm($d['matches'][$id], $d)]);
    }

    public function create()
    {
        $d = $this->data();

        return $this->page('matches.create', $d, ['pitches' => $d['pitches'], 'levels' => self::LEVELS]);
    }

    public function store(Request $r)
    {
        $v = $r->validate([
            'title' => 'required|max:60',
            'slot' => 'required|string',
            'format' => 'required|in:5v5,7v7,11v11',
            'type' => 'required|in:open,private',
            'level' => 'required|in:' . implode(',', self::LEVELS),
        ]);
        $d = $this->data();
        [$pid, $slot] = array_pad(explode('|', $v['slot'], 2), 2, '');
        abort_unless(isset($d['pitches'][$pid]) && in_array($slot, $d['pitches'][$pid]['slots']), 422);

        $parts = explode(' ', $slot);
        $id = max(array_keys($d['matches'])) + 1;
        $d['matches'][$id] = [
            'id' => $id, 'title' => $v['title'], 'pitch' => (int) $pid,
            'date' => implode(' ', array_slice($parts, 0, 3)), 'time' => $parts[3],
            'format' => $v['format'], 'max' => self::MAX[$v['format']], 'level' => $v['level'],
            'type' => $v['type'], 'mine' => true, 'players' => [$this->me()], 'wait' => [], 'teams' => null,
        ];
        $d['pitches'][$pid]['slots'] = array_values(array_diff($d['pitches'][$pid]['slots'], [$slot]));
        session(['lfmo' => $d]);

        return redirect()->route('matches.show', $id)
            ->with('flash', $v['type'] === 'private' ? 'Private match created. Share the link with your friends.' : 'Match created.');
    }

    public function join(int $id)
    {
        return $this->mutate($id, function (array &$m) {
            if (count($m['players']) < $m['max']) {
                $m['players'][] = $this->me();

                return 'You joined the match.';
            }
            $m['wait'][] = $this->me();

            return 'You are on the waiting list, position ' . count($m['wait']) . '.';
        });
    }

    public function leave(int $id)
    {
        return $this->mutate($id, function (array &$m, array &$d) {
            $m['players'] = array_values(array_filter($m['players'], fn ($p) => ! $p['me']));
            $m['wait'] = array_values(array_filter($m['wait'], fn ($p) => ! $p['me']));
            $this->promote($m, $d);

            return 'You left the match.';
        });
    }

    public function drop(int $id)
    {
        return $this->mutate($id, function (array &$m, array &$d) {
            $others = array_keys(array_filter($m['players'], fn ($p) => ! $p['me']));
            if (! $others) {
                return 'No other players to remove.';
            }
            unset($m['players'][$others[array_rand($others)]]);
            $m['players'] = array_values($m['players']);
            $this->promote($m, $d);

            return 'A player dropped out.';
        });
    }

    public function balance(int $id)
    {
        return $this->mutate($id, function (array &$m) {
            $pl = $m['players'];
            usort($pl, fn ($x, $y) => $y['s'] <=> $x['s']);
            $a = $b = [];
            $cap = (int) ceil(count($pl) / 2);
            foreach ($pl as $p) {
                $sa = array_sum(array_column($a, 's'));
                $sb = array_sum(array_column($b, 's'));
                if (count($a) < $cap && ($sa <= $sb || count($b) >= $cap)) {
                    $a[] = $p;
                } else {
                    $b[] = $p;
                }
            }
            $m['teams'] = [$a, $b];

            return 'Teams balanced by skill.';
        });
    }

    public function pitches()
    {
        $d = $this->data();

        return $this->page('pitches.index', $d, ['pitches' => $d['pitches']]);
    }

    public function storePitch(Request $r)
    {
        $v = $r->validate([
            'name' => 'required|max:60',
            'zone' => 'required|max:30',
            'price' => 'required|numeric|min:0',
            'format' => 'required|in:5v5,7v7,11v11',
            'rules' => 'nullable|max:200',
            'slot' => ['required', 'regex:/^[A-Za-z]{3} \d{1,2} [A-Za-z]{3} \d{2}:\d{2}$/'],
        ], ['slot.regex' => 'Use a slot like "Sat 31 Oct 18:00".']);
        $d = $this->data();
        $id = max(array_keys($d['pitches'])) + 1;
        $d['pitches'][$id] = [
            'id' => $id, 'name' => $v['name'], 'zone' => $v['zone'], 'price' => (int) $v['price'],
            'format' => $v['format'], 'rules' => $v['rules'] ?: 'No special rules.', 'slots' => [$v['slot']],
        ];
        session(['lfmo' => $d]);

        return back()->with('flash', 'Pitch published.');
    }

    public function profile()
    {
        $d = $this->data();
        $played = 12 + collect($d['matches'])->filter(fn ($m) => $this->vm($m, $d)['joined'])->count();

        return $this->page('profile.show', $d, ['played' => $played, 'skill' => 6]);
    }

    private function me(): array
    {
        return ['n' => 'You', 's' => 6, 'me' => true];
    }

    private function page(string $view, array $d, array $data = [])
    {
        return view($view, $data + ['notes' => $this->notes($d)]);
    }

    private function mutate(int $id, callable $fn)
    {
        $d = $this->data();
        abort_unless(isset($d['matches'][$id]), 404);
        $m = &$d['matches'][$id];
        $m['teams'] = null;
        $flash = $fn($m, $d);
        unset($m);
        session(['lfmo' => $d]);

        return back()->with('flash', $flash);
    }

    private function promote(array &$m, array &$d): void
    {
        if ($m['wait'] && count($m['players']) < $m['max']) {
            $q = array_shift($m['wait']);
            $m['players'][] = $q;
            array_unshift($d['notes'], 'From the waiting list: ' . ($q['me'] ? 'you are' : $q['n'] . ' is') . " now confirmed in {$m['title']}.");
        }
    }

    private function vm(array $m, array $d): array
    {
        $p = $d['pitches'][$m['pitch']];
        $pos = array_search(true, array_column($m['wait'], 'me'), true);
        $m['pitch_data'] = $p;
        $m['joined'] = in_array(true, array_column($m['players'], 'me'), true);
        $m['waiting'] = $pos !== false;
        $m['position'] = $pos === false ? null : $pos + 1;
        $m['risk'] = count($m['players']) < $m['max'] * 0.6;
        $m['pp'] = (int) ceil($p['price'] / $m['max']);

        return $m;
    }

    private function notes(array $d): array
    {
        $n = $d['notes'];
        foreach ($d['matches'] as $m) {
            $vm = $this->vm($m, $d);
            if ($vm['joined']) {
                $n[] = "Reminder: {$m['title']}, {$m['date']} at {$m['time']}.";
            }
            if ($vm['risk'] && ($vm['joined'] || $vm['waiting'])) {
                $n[] = "{$m['title']} may be cancelled (" . count($m['players']) . "/{$m['max']} players).";
            }
        }

        return $n;
    }

    private function data(): array
    {
        if (! session()->has('lfmo')) {
            session(['lfmo' => $this->seed()]);
        }

        return session('lfmo');
    }

    private function seed(): array
    {
        $names = ['Luca', 'Marco', 'Giulia', 'Andrea', 'Sara', 'Matteo', 'Paolo', 'Elena', 'Davide', 'Chiara', 'Simone', 'Fabio', 'Nico', 'Ivan', 'Teo', 'Rob', 'Dario', 'Anna'];
        $skills = [8, 4, 6, 9, 5, 7, 3, 6, 8, 5, 7, 4, 6, 9, 5, 7, 3, 6];
        $pl = fn ($a, $b) => array_map(fn ($i) => ['n' => $names[$i], 's' => $skills[$i], 'me' => false], range($a, $b - 1));
        $m = fn ($id, $title, $pitch, $date, $time, $fmt, $level, $players, $wait = []) => [
            'id' => $id, 'title' => $title, 'pitch' => $pitch, 'date' => $date, 'time' => $time,
            'format' => $fmt, 'max' => self::MAX[$fmt], 'level' => $level, 'type' => 'open',
            'mine' => false, 'players' => $players, 'wait' => $wait, 'teams' => null,
        ];

        return [
            'notes' => [],
            'pitches' => [
                1 => ['id' => 1, 'name' => 'Central Pitch', 'zone' => 'Centre', 'price' => 60, 'format' => '5v5', 'rules' => 'Indoor or turf shoes only, no metal studs.', 'slots' => ['Thu 22 Oct 20:00', 'Fri 23 Oct 21:00', 'Sat 24 Oct 18:00']],
                2 => ['id' => 2, 'name' => 'North Arena', 'zone' => 'North', 'price' => 90, 'format' => '7v7', 'rules' => 'Showers included, ball provided.', 'slots' => ['Sat 24 Oct 10:00', 'Sun 25 Oct 11:00']],
                3 => ['id' => 3, 'name' => 'South Hall', 'zone' => 'South', 'price' => 70, 'format' => '5v5', 'rules' => 'Indoor shoes only.', 'slots' => ['Wed 28 Oct 20:30', 'Thu 29 Oct 19:00']],
            ],
            'matches' => [
                1 => $m(1, 'Thursday five-a-side', 1, 'Thu 22 Oct', '20:00', '5v5', 'Intermediate', $pl(0, 10), $pl(10, 12)),
                2 => $m(2, 'Saturday morning 7v7', 2, 'Sat 24 Oct', '10:00', '7v7', 'Advanced', $pl(0, 8)),
                3 => $m(3, 'Friendly game', 3, 'Wed 28 Oct', '20:30', '5v5', 'Beginner', $pl(4, 10)),
                4 => $m(4, 'Friday night', 1, 'Fri 23 Oct', '21:00', '5v5', 'Intermediate', [...$pl(5, 9), $this->me()]),
            ],
        ];
    }
}
