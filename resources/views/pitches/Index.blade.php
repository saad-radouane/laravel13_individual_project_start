@extends('layouts.app')

@section('title', 'Pitches')

@section('content')
    <h2 class="mb-1 mt-5 font-display text-3xl font-semibold">Pitches and availability</h2>
    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">Organisation view: publish slots, prices and rules. Tap a slot to organise a match there.</p>

    @foreach($pitches as $p)
        <article class="my-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center justify-between gap-3">
                <h3 class="font-display text-[21px] font-semibold leading-tight">{{ $p['name'] }}</h3>
                <b>€{{ $p['price'] }}/hour</b>
            </div>
            <p class="my-1 text-sm text-slate-500 dark:text-slate-400">{{ $p['zone'] }} area, {{ $p['format'] }}</p>
            <p>{{ $p['rules'] }}</p>
            <div class="my-2 flex flex-wrap gap-1.5">
                @forelse($p['slots'] as $slot)
                    <a href="{{ route('matches.create', ['slot' => $p['id'] . '|' . $slot]) }}" class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:hover:bg-emerald-900">{{ $slot }}</a>
                @empty
                    <span class="text-sm text-slate-500 dark:text-slate-400">No free slots</span>
                @endforelse
            </div>
        </article>
    @endforeach

    <form method="POST" action="{{ route('pitches.store') }}" class="my-3 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
        @csrf
        <h3 class="font-display text-[21px] font-semibold leading-tight">Add a pitch</h3>

        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Name
            <input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
        </label>
        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Area
            <input name="zone" value="{{ old('zone') }}" placeholder="Centre" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
        </label>
        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Price per hour in euros
            <input name="price" type="number" min="0" value="{{ old('price', 60) }}" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
        </label>
        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Format
            <select name="format" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
                @foreach(['5v5', '7v7', '11v11'] as $f)
                    <option @selected(old('format') === $f)>{{ $f }}</option>
                @endforeach
            </select>
        </label>
        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Rules
            <input name="rules" value="{{ old('rules') }}" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
        </label>
        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">First free slot
            <input name="slot" value="{{ old('slot') }}" placeholder="Sat 31 Oct 18:00" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
        </label>

        <button class="rounded-lg bg-emerald-700 px-4 py-2.5 font-semibold text-white hover:bg-emerald-800 dark:bg-emerald-400 dark:text-emerald-950 dark:hover:bg-emerald-300">Publish pitch</button>
    </form>
@endsection
