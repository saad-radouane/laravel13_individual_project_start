<ul class="mt-2">
    @foreach($list as $p)
        <li class="flex items-center justify-between border-t border-slate-200 py-2 dark:border-neutral-800">
            <span>{{ $p['n'] }}</span>
            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[13px] font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ $p['s'] }}/10</span>
        </li>
    @endforeach
</ul>
