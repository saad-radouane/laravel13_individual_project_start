@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="mt-5 flex items-center gap-3.5">
        <div class="grid size-14 shrink-0 place-items-center rounded-full bg-emerald-700 font-display text-3xl font-bold text-white dark:bg-emerald-400 dark:text-emerald-950">Y</div>
        <div>
            <h2 class="font-display text-3xl font-semibold">Your profile</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Midfielder, right foot</p>
        </div>
    </div>

    <div class="mt-3.5 grid grid-cols-3 gap-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-3 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <b class="block font-display text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $skill }}</b>
            <span class="text-[13px] text-slate-500 dark:text-slate-400">Intermediate level</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <b class="block font-display text-3xl font-bold text-emerald-700 dark:text-emerald-400">92%</b>
            <span class="text-[13px] text-slate-500 dark:text-slate-400">Reliability</span>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-3 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <b class="block font-display text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $played }}</b>
            <span class="text-[13px] text-slate-500 dark:text-slate-400">Matches played</span>
        </div>
    </div>

    <div class="my-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
        <h3 class="font-display text-[21px] font-semibold leading-tight">Match history</h3>
        <ul class="mt-2">
            @foreach([['Wednesday five-a-side, 14 Oct', '4-3'], ['Saturday 7v7, 10 Oct', '2-2'], ['Thursday five-a-side, 8 Oct', '1-3']] as [$game, $score])
                <li class="flex items-center justify-between border-t border-slate-200 py-2 dark:border-neutral-800">
                    <span>{{ $game }}</span>
                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ $score }}</span>
                </li>
            @endforeach
        </ul>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">After each match your teammates rate you, so your skill level becomes more accurate.</p>
    </div>
@endsection
