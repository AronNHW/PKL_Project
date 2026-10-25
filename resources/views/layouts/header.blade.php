<header class="h-14 flex items-center justify-between px-4 md:px-6 shrink-0">
    <div class="flex items-center gap-3 min-w-0">
        <button type="button" @click="mobileMenuOpen = true"
                class="md:hidden flex items-center justify-center w-10 h-10 -ml-2 rounded-md text-ink hover:bg-black/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
                aria-label="Buka menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-lg font-semibold text-ink truncate">{{ $title ?? 'Ringkasan' }}</h1>
    </div>

    <div class="flex items-center gap-4 text-[13px] text-muted shrink-0">
        <span class="hidden sm:inline">
            @if(auth()->user()->lokasi)
                {{ auth()->user()->lokasi->nama_lokasi }}
            @else
                Semua lokasi
            @endif
        </span>
        <span class="hidden lg:inline">{{ now()->locale('id')->translatedFormat('l, j F Y') }}</span>
        <a href="{{ route('profile.edit') }}" class="hover:text-ink transition-colors">Profil</a>
    </div>
</header>
