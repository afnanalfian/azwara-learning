{{-- ================= DASHBOARD ================= --}}
<a href="{{ route('dashboard.redirect') }}"
   class="menu-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 9.75L12 4.5l9 5.25V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/>
    </svg>
    Dashboard
</a>

{{-- ================= COURSE ================= --}}
<a href="{{ route('course.index') }}"
   class="menu-item {{ request()->routeIs('course.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 5a2 2 0 012-2h11a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M7 5v16"/>
    </svg>
    Course
</a>
{{-- ================= SCHEDULE ================= --}}
<a href="{{ route('schedule.index') }}"
   class="menu-item {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <!-- Icon kalender -->
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0
                 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    Schedule
</a>

{{-- ================= TENTOR ================= --}}
<a href="{{ route('tentor.index') }}"
   class="menu-item {{ request()->routeIs('tentor.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M17 20h5v-2a4 4 0 00-4-4h-1m-6 6H4v-2a4 4 0 014-4h1m0-4a4 4 0 118 0 4 4 0 01-8 0z"/>
    </svg>
    Tentor
</a>
{{-- ================= EXAMS ================= --}}
@php
    $evaluasiActive = request()->routeIs(
        'tryouts.*',
        'quizzes.*',
        'leaderboard.*'
    );
@endphp

<div x-data="{ open: {{ $evaluasiActive ? 'true' : 'false' }} }" class="relative">

    <button
        @click="open = !open"
        class="menu-item w-full flex justify-between items-center
               {{ $evaluasiActive ? 'active' : '' }}"
    >
        <div class="flex items-center gap-2">
            <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6
                         a2 2 0 012-2h3.5a2 2 0 004 0H17
                         a2 2 0 012 2v12a2 2 0 01-2 2z"/>
            </svg>
            Exams
        </div>

        <svg :class="open ? 'rotate-180' : ''"
             class="w-4 h-4 transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="ml-6 mt-2 space-y-1 border-l
               border-azwara-medium/30
               dark:border-azwara-light/20 pl-4"
    >
        <a href="{{ route('tryouts.index') }}"
           class="menu-subitem {{ request()->routeIs('tryouts.*') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Tryout
        </a>

        <a href="{{ route('quizzes.index') }}"
           class="menu-subitem {{ request()->routeIs('quizzes.*') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Daily Quiz
        </a>

        <a href="{{ route('leaderboard.index') }}"
           class="menu-subitem {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Leaderboard
        </a>
    </div>
</div>


{{-- ================= PEMBELIAN (DROPDOWN SISWA) ================= --}}
@php
    $purchaseActive = request()->routeIs(
        'browse.*',
        'cart.*',
        'checkout.*',
        'my.orders.*'
    );
@endphp

<div x-data="{ open: {{ $purchaseActive ? 'true' : 'false' }} }" class="relative">

    <button @click="open = !open"
            class="menu-item w-full flex justify-between items-center
                   {{ $purchaseActive ? 'active' : '' }}">
        <div class="flex items-center gap-2">
            <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8h13.2L17 13H7z"/>
            </svg>
            Pembelian
        </div>

        <svg :class="open ? 'rotate-180' : ''"
             class="w-4 h-4 transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open"
         @click.outside="open = false"
         x-transition
         class="ml-6 mt-2 space-y-1 border-l
                border-azwara-medium/30
                dark:border-azwara-light/20 pl-4">

        <a href="{{ route('browse.index') }}"
           class="menu-subitem {{ request()->routeIs('browse.*') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Beli Course
        </a>

        <a href="{{ route('cart.show') }}"
           class="menu-subitem {{ request()->routeIs('cart.*') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Keranjang
        </a>

        <a href="{{ route('my.orders.index') }}"
           class="menu-subitem {{ request()->routeIs('my.orders.*') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Riwayat Pembelian
        </a>
    </div>
</div>

{{-- ================= SIMPLE GAMES MENU ================= --}}
<div x-data="{ open: {{ request()->routeIs('game.*') ? 'true' : 'false' }} }" class="relative">

    <button
        @click="open = !open"
        class="menu-item w-full flex justify-between items-center
               {{ request()->routeIs('game.*') ? 'active' : '' }}"
    >
        <div class="flex items-center gap-2">
            {{-- Game Console Icon --}}
            <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 12a.75.75 0 01.75-.75h1.5V9.75a.75.75 0 011.5 0v1.5h1.5a.75.75 0 010 1.5h-1.5v1.5a.75.75 0 01-1.5 0v-1.5H7.5a.75.75 0 01-.75-.75zm7.5 0a.75.75 0 01.75-.75h.008a.75.75 0 010 1.5h-.008a.75.75 0 01-.75-.75zm3 0a.75.75 0 01.75-.75h.008a.75.75 0 010 1.5h-.008a.75.75 0 01-.75-.75zM4.5 6.75h15a2.25 2.25 0 012.25 2.25v6a2.25 2.25 0 01-2.25 2.25h-15A2.25 2.25 0 012.25 15V9a2.25 2.25 0 012.25-2.25z" />
            </svg>
            Simple Games
        </div>

        <svg :class="open ? 'rotate-180' : ''"
             class="w-4 h-4 transition-transform duration-200"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="ml-6 mt-2 space-y-1 border-l
               border-azwara-medium/30
               dark:border-azwara-light/20 pl-4"
    >
        {{-- Math Quiz Game --}}
        <a href="{{ route('game.math') }}"
           class="menu-subitem {{ request()->routeIs('game.math') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Math Quiz
        </a>

        {{-- Snake Game --}}
        <a href="{{ route('game.snake') }}"
           class="menu-subitem {{ request()->routeIs('game.snake') ? 'active' : '' }}">
            <span class="w-1 h-1 rounded-full bg-current"></span>
            Snake Game
        </a>
    </div>
</div>
