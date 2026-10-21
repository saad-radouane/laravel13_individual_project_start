@extends('layouts.app')

@section('title', 'Create a match')

@section('content')
    <h2 class="mb-1 mt-5 font-display text-3xl font-semibold">Create a match</h2>
    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">Pick a pitch with a free slot and choose whether the match is open to everyone or private.</p>

    <form method="POST" action="{{ route('matches.store') }}" class="my-3 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
        @csrf

        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Title
            <input name="title" value="{{ old('title', 'New match') }}" maxlength="60" required class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
        </label>

        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Free slot
            <select name="slot" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
                @foreach($pitches as $p)
                    <optgroup label="{{ $p['name'] }}, €{{ $p['price'] }}/hour">
                        @foreach($p['slots'] as $slot)
                            <option value="{{ $p['id'] }}|{{ $slot }}" @selected(old('slot', request('slot')) === $p['id'] . '|' . $slot)>{{ $slot }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </label>

        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Format
            <select name="format" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
                @foreach(['5v5', '7v7', '11v11'] as $f)
                    <option @selected(old('format') === $f)>{{ $f }}</option>
                @endforeach
            </select>
        </label>

        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Type
            <select name="type" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
                <option value="open" @selected(old('type') === 'open')>Open to everyone</option>
                <option value="private" @selected(old('type') === 'private')>Private</option>
            </select>
        </label>

        <label class="grid gap-1 text-[13px] text-slate-500 dark:text-slate-400">Required level
            <select name="level" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-base text-slate-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-slate-100">
                @foreach($levels as $level)
                    <option @selected(old('level', 'Intermediate') === $level)>{{ $level }}</option>
                @endforeach
            </select>
        </label>

        <button class="rounded-lg bg-emerald-700 px-4 py-2.5 font-semibold text-white hover:bg-emerald-800 dark:bg-emerald-400 dark:text-emerald-950 dark:hover:bg-emerald-300">Create match</button>
    </form>
@endsection
