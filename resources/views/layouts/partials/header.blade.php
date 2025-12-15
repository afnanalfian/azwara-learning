<header class="w-full bg-azwara-lighter dark:bg-azwara-darker shadow-sm px-6 py-4 flex justify-between items-center">

    {{-- Hamburger button (desktop & mobile) --}}
    <button
        onclick="toggleSidebar()"
        class="block md:hidden text-azwara-darkest dark:text-azwara-lighter">
        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 6h20M4 12h20M4 18h20"/>
        </svg>
    </button>

    {{-- <h1 class="text-lg font-semibold text-azwara-darkest dark:text-azwara-lighter">
    </h1> --}}

    {{-- Logo --}}
    <a href="{{ route('dashboard.redirect') }}"
    class="text-xl font-bold text-azwara-darker dark:text-white tracking-wide">
        Azwara<span class="text-primary">Learning</span>
    </a>

    <div class="flex items-center gap-6">

        {{-- Theme Toggle Icon --}}
        <button onclick="toggleTheme()"
                class="text-azwara-darkest dark:text-azwara-lighter hover:scale-110 transition">

            <!-- Moon Icon (tampil saat terang) -->
            <svg class="block dark:hidden" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z"/>
            </svg>

            <!-- Sun Icon (tampil saat gelap) -->
            <svg class="hidden dark:block" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <path d="M12 1v2m0 18v2m11-11h-2M3 12H1m16.95 7.95-1.41-1.41M6.46 6.46 5.05 5.05m12.9 0-1.41 1.41M6.46 17.54 5.05 18.95"/>
            </svg>
        </button>

        {{-- Bell Icon --}}
        <button class="text-azwara-darkest dark:text-azwara-lighter">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22c1.1 0 2-.9 2-2H10c0 1.1.9 2 2 2Zm6-6v-5a6 6 0 1 0-12 0v5l-2 2v1h16v-1l-2-2Z"/>
            </svg>
        </button>

        {{-- User Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <!-- Trigger -->
            <button @click="open = !open" class="flex items-center gap-3">
                <span class="hidden md:block text-azwara-darkest dark:text-azwara-lighter font-medium">
                    {{ auth()->user()->name }}
                </span>
            <img src="{{ auth()->user()->avatar_url }}"
                class="w-10 h-10 rounded-full border border-azwara-medium object-cover" />
            </button>

            <!-- Dropdown Panel -->
            <div x-show="open"
                @click.outside="open = false"
                x-transition
                x-cloak
                class="absolute right-0 mt-2 w-40 bg-white dark:bg-azwara-darker shadow-md rounded-lg p-2 z-50">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-azwara-darkest dark:text-azwara-lighter hover:bg-azwara-lighter/50 dark:hover:bg-azwara-medium/20 rounded">
                    Profil
                </a>
                <a href="{{ route('home') }}" class="block px-4 py-2 text-azwara-darkest dark:text-azwara-lighter hover:bg-azwara-lighter/50 dark:hover:bg-azwara-medium/20 rounded">
                    Home
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-100 dark:hover:bg-red-900/30 rounded">
                        Logout
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
