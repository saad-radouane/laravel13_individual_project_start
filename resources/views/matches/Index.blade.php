@extends('layouts.app')

@section('title', 'Open matches')

@section('content')
    <h2 class="mb-1 mt-5 font-display text-3xl font-semibold">Open matches</h2>
    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">Filter by area, date and level, then join with one click.</p>

    <form method="GET" action="{{ route('matches.index') }}" class="mt-3.5 grid grid-cols-3 gap-2">
        @foreach([['zone', 'Area', $zones], ['date', 'Date', $dates], ['level', 'Level', $levels]] as [$key, $label, $options])
            <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">{{ $label }}
                <select name="{{ $key }}" onchange="this.form.submit()" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
                    <option value="">All</option>
                    @foreach($options as $option)
                        <option @selected(($f[$key] ?? '') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </label>
        @endforeach
    </form>

    @forelse($matches as $m)
        @include('partials.match-card', ['m' => $m])
    @empty
        <p class="my-4 text-sm text-slate-500 dark:text-slate-400">No matches with these filters. Change the area, date or level, or create your own match.</p>
    @endforelse
@endsection
