<aside class="hidden md:flex md:w-60 shrink-0 flex-col h-screen">
    <div class="h-14 flex items-center px-5 shrink-0">
        <x-application-logo />
    </div>
    @include('layouts.sidebar-nav')
</aside>

<div x-show="mobileMenuOpen" x-cloak
     class="fixed inset-0 z-40 bg-black/40 md:hidden"
     @click="mobileMenuOpen = false"
     x-transition.opacity.duration.150ms
     aria-hidden="true"></div>

<div x-show="mobileMenuOpen" x-cloak
     class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-frame flex flex-col md:hidden shadow-2xl"
     x-transition:enter="transition ease-out duration-200 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-150 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">
    <div class="h-14 flex items-center justify-between px-5 shrink-0">
        <x-application-logo />
        <button type="button" @click="mobileMenuOpen = false"
                class="flex items-center justify-center w-9 h-9 rounded-md text-muted hover:bg-black/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
                aria-label="Tutup menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    @include('layouts.sidebar-nav')
</div>
