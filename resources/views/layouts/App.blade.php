<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Matches') | LFMO</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 font-sans text-base text-slate-900 dark:bg-neutral-950 dark:text-slate-100">
<header class="sticky top-0 z-20 bg-emerald-900 text-white dark:bg-emerald-950">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -right-9 -top-10 size-32 rounded-full border-2 border-white/20"></div>
        <div class="absolute inset-y-0 right-8 border-l-2 border-white/20"></div>
    </div>

    <div class="relative mx-auto flex max-w-2xl items-center justify-between px-4 pt-2.5">
        <a href="{{ route('matches.index') }}" class="font-display text-4xl font-bold tracking-wide">LFMO</a>

        <details class="relative mr-11">
            <summary aria-label="Notifications" class="cursor-pointer list-none text-xl [&::-webkit-details-marker]:hidden flex items-center">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if(count($notes))
                    <span class="align-top rounded-full bg-yellow-400 px-1.5 text-xs font-bold text-yellow-950 ml-1">{{ count($notes) }}</span>
                @endif
            </summary>
            <div class="absolute right-0 top-9 z-30 w-72 rounded-2xl border border-slate-200 bg-white p-4 text-slate-900 shadow-lg dark:border-neutral-800 dark:bg-neutral-900 dark:text-slate-100">
                <h3 class="font-display text-xl font-semibold">Notifications</h3>
                @forelse($notes as $note)
                    <p class="mt-2 text-sm">{{ $note }}</p>
                @empty
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">No notifications.</p>
                @endforelse
            </div>
        </details>
    </div>

    @php
        $tabs = ['matches.index' => 'Matches', 'matches.create' => 'Create', 'pitches.index' => 'Pitches', 'profile' => 'Profile'];
    @endphp
    <nav class="relative mx-auto flex max-w-2xl gap-1 px-4">
        @foreach($tabs as $route => $label)
            @php $active = request()->routeIs($route) || ($route === 'matches.index' && request()->routeIs('matches.show')); @endphp
            <a href="{{ route($route) }}" @class([
                'border-b-[3px] px-3 py-2.5 text-[15px] font-semibold',
                'border-white text-white' => $active,
                'border-transparent text-white/70 hover:text-white' => ! $active,
            ])>{{ $label }}</a>
        @endforeach
    </nav>
</header>

<main class="mx-auto max-w-2xl px-4 pb-12">
    @if(session('flash'))
        <p role="status" class="mt-4 rounded-lg bg-emerald-100 px-3.5 py-2.5 font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ session('flash') }}</p>
    @endif

    @if($errors->any())
        <div class="my-3 rounded-2xl border border-slate-200 bg-white p-4 text-red-700 dark:border-neutral-800 dark:bg-neutral-900 dark:text-red-400">
            @foreach($errors->all() as $error)
                <p class="my-0.5">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
