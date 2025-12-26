<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Azwara Learning</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script>
        (function () {
            const storedTheme = localStorage.getItem('theme');

            if (storedTheme === 'dark' ||
                (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body
  class="min-h-screen overflow-hidden flex flex-col
         bg-gradient-to-br from-azwara-lighter via-white to-azwara-light/30
         dark:bg-brand-gradient bg-fixed
         text-azwara-darker dark:text-azwara-lighter
         transition-colors">


    {{-- Navbar --}}
    <nav class="w-full
                bg-azwara-lightest dark:bg-azwara-darkest/80
                backdrop-blur
                border-b border-azwara-light/30
                shadow-sm py-4
                sticky top-0 z-50">
        <div class="container mx-auto px-4 flex items-center justify-between">

            {{-- Logo --}}
            <a href="{{ route('home') }}"
            class="text-xl font-bold text-azwara-darker dark:text-white tracking-wide">
                Azwara<span class="text-primary">Learning</span>
            </a>

            <div class="flex items-center gap-4">

            {{-- Theme Toggle Icon --}}
            <button
                onclick="
                    const html = document.documentElement;
                    const isDark = html.classList.toggle('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                "
                class="text-azwara-darkest dark:text-azwara-lighter hover:scale-110 transition">

                <!-- Moon (light mode) -->
                <svg class="block dark:hidden" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3a7 7 0 0 0 9.79 9.79Z"/>
                </svg>

                <!-- Sun (dark mode) -->
                <svg class="hidden dark:block" width="24" height="24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="5"/>
                    <path d="M12 1v2m0 18v2m11-11h-2M3 12H1
                            m16.95 7.95-1.41-1.41
                            M6.46 6.46 5.05 5.05
                            m12.9 0-1.41 1.41
                            M6.46 17.54 5.05 18.95"/>
                </svg>
            </button>

                <a href="{{ route('login') }}"
                class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary transition">
                    Login
                </a>

                <a href="{{ route('register') }}"
                class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-semibold shadow
                        hover:shadow-lg hover:scale-105 transition">
                    Register
                </a>
            </div>
        </div>
    </nav>

    {{-- Page content --}}
    <main class="flex-1 overflow-y-auto">
        @yield('content')
        {{-- Footer --}}
        <footer
        class="py-6 text-center text-sm
                text-gray-600 dark:text-gray-400
                border-t border-azwara-light/30
                backdrop-blur-sm">
        © {{ date('Y') }} Azwara Learning
        </footer>
    </main>
    @include('layouts.partials.toast')
    @stack('scripts')
</body>
</html>
