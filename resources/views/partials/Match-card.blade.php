<article class="my-3 rounded-2xl border border-slate-200 bg-white p-4 dark:border-neutral-800 dark:bg-neutral-900">
    <div class="flex items-center justify-between gap-3">
        <h3 class="font-display text-[21px] font-semibold leading-tight">{{ $m['title'] }}</h3>
        @if($m['type'] === 'private')
            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">Private</span>
        @endif
    </div>

    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">{{ $m['date'] }} at {{ $m['time'] }}</p>
    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">{{ $m['pitch_data']['name'] }}, {{ $m['pitch_data']['zone'] }} area</p>

    <div class="my-2 flex flex-wrap gap-1.5">
        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ $m['format'] }}</span>
        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ $m['level'] }}</span>
        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">€{{ $m['pp'] }} each</span>
    </div>

    <div class="mt-2 h-2 overflow-hidden rounded-full bg-emerald-100 dark:bg-emerald-950">
        <div class="h-full bg-emerald-600 dark:bg-emerald-400" style="width: {{ count($m['players']) / $m['max'] * 100 }}%"></div>
    </div>
    <p class="my-1 text-sm text-slate-500 dark:text-slate-400">{{ count($m['players']) }} of {{ $m['max'] }} confirmed{{ $m['wait'] ? ', ' . count($m['wait']) . ' waiting' : '' }}</p>

    @if($m['risk'])
        <p class="my-2 flex items-center gap-2 text-sm font-semibold">
            <span class="h-[15px] w-[11px] shrink-0 rounded-sm bg-yellow-400"></span>
            At risk: more players needed
        </p>
    @endif

    <div class="mt-3 flex items-center justify-between gap-3">
        @if($detail ?? false)
            <span></span>
        @else
            <a href="{{ route('matches.show', $m['id']) }}" class="inline-block rounded-lg border border-slate-300 px-4 py-2.5 font-semibold text-emerald-700 dark:border-neutral-700 dark:text-emerald-400">Details</a>
        @endif

        @if($m['joined'] || $m['waiting'])
            <form method="POST" action="{{ route('matches.leave', $m['id']) }}">
                @csrf
                <button class="rounded-lg border border-slate-400 px-4 py-2.5 font-semibold dark:border-slate-500">{{ $m['joined'] ? 'Leave match' : 'Leave waiting list (#' . $m['position'] . ')' }}</button>
            </form>
        @else
            <form method="POST" action="{{ route('matches.join', $m['id']) }}">
                @csrf
                <button class="rounded-lg bg-emerald-700 px-4 py-2.5 font-semibold text-white hover:bg-emerald-800 dark:bg-emerald-400 dark:text-emerald-950 dark:hover:bg-emerald-300">{{ count($m['players']) >= $m['max'] ? 'Join waiting list' : 'Join' }}</button>
            </form>
        @endif
    </div>
</article>
