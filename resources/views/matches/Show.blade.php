@extends('layouts.app')

@section('title', $m['title'])

@section('content')
    <p class="mt-4">
        <a href="{{ route('matches.index') }}" class="inline-block rounded-lg border border-slate-300 px-4 py-2.5 font-semibold text-emerald-700 dark:border-neutral-700 dark:text-emerald-400">Back</a>
    </p>

    @include('partials.match-card', ['m' => $m, 'detail' => true])

    <div class="my-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
        <h3 class="font-display text-[21px] font-semibold leading-tight">Pitch rules</h3>
        <p class="my-1 text-sm text-slate-500 dark:text-slate-400">{{ $m['pitch_data']['rules'] }}</p>
    </div>

    <div class="my-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
        <h3 class="font-display text-[21px] font-semibold leading-tight">Confirmed players</h3>
        @include('partials.players', ['list' => $m['players']])
    </div>

    @if($m['wait'])
        <div class="my-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <h3 class="font-display text-[21px] font-semibold leading-tight">Waiting list</h3>
            @include('partials.players', ['list' => $m['wait']])
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">The first player on the list is promoted automatically if someone drops out.</p>
        </div>
    @endif

    <div class="flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('matches.balance', $m['id']) }}">
            @csrf
            <button @disabled(count($m['players']) < 2) class="rounded-lg bg-emerald-700 px-4 py-2.5 font-semibold text-white hover:bg-emerald-800 disabled:opacity-40 dark:bg-emerald-400 dark:text-emerald-950 dark:hover:bg-emerald-300">Balance teams</button>
        </form>
        <form method="POST" action="{{ route('matches.drop', $m['id']) }}">
            @csrf
            <button class="rounded-lg border border-slate-300 px-4 py-2.5 font-semibold text-emerald-700 dark:border-neutral-700 dark:text-emerald-400">Simulate a dropout</button>
        </form>
    </div>

    @if($m['teams'])
        <h2 class="mb-2 mt-6 font-display text-3xl font-semibold">Teams</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach($m['teams'] as $i => $team)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="font-display text-[21px] font-semibold leading-tight">Team {{ ['A', 'B'][$i] }}</h3>
                    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">Total strength {{ array_sum(array_column($team, 's')) }}</p>
                    @include('partials.players', ['list' => $team])
                </div>
            @endforeach
        </div>
    @endif
@endsection
