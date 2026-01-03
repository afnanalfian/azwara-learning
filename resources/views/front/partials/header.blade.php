<header
    id="site-header"
    class="fixed top-0 inset-x-0 z-50 transition-all duration-300 bg-transparent"
>
    {{-- Wrapper dengan background penuh --}}
    <div class="w-full bg-transparent transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20"> {{-- Tinggi diperbesar --}}

                {{-- Logo dengan lingkaran --}}
                <a href="#home" class="flex items-center gap-3 group">
                    <div class="relative">
                        <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-white shadow-md">
                            <img
                                src="{{ asset('img/logo.png') }}"
                                alt="Azwara Learning"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        {{-- Efek hover ring --}}
                        <div class="absolute inset-0 rounded-full border-2 border-transparent group-hover:border-primary transition-all duration-300"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-2xl text-azwara-darkest leading-tight">
                            Azwara<span class="text-primary">Learning</span>
                        </span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#home" class="nav-link text-lg font-medium">Home</a>
                    <a href="#about" class="nav-link text-lg font-medium">About</a>
                    <a href="#courses" class="nav-link text-lg font-medium">Course</a>
                    <a href="#teachers" class="nav-link text-lg font-medium">Teachers</a>
                    {{-- <a href="#testimonials" class="nav-link text-lg font-medium">Testimonial</a> --}}
                    {{-- <a href="#tutorial" class="nav-link text-lg font-medium">
                        Tutorial
                    </a> --}}
                </nav>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a
                            href="{{ route('dashboard.redirect') }}"
                            class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-azwara-darker transition duration-300 shadow-md hover:shadow-lg"
                        >
                            Dashboard
                        </a>
                    @endauth

                    @guest
                        <a
                            href="{{ route('login') }}"
                            class="px-5 py-2.5 rounded-lg border-2 border-primary text-primary font-medium hover:bg-primary hover:text-white transition duration-300"
                        >
                            Login
                        </a>
                        <a
                            href="{{ route('register') }}"
                            class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-azwara-darker transition duration-300 shadow-md hover:shadow-lg"
                        >
                            Register
                        </a>
                    @endguest
                </div>

                {{-- Mobile Toggle --}}
                <button
                    id="mobile-menu-btn"
                    class="md:hidden text-azwara-darkest p-2 rounded-lg hover:bg-azwara-lighter transition duration-300"
                    aria-label="Toggle menu"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div
        id="mobile-menu"
        class="md:hidden hidden bg-white shadow-xl"
    >
        <div class="px-4 py-6 space-y-1">
            {{-- Menu items dengan padding yang lebih baik --}}
            <a href="#home" class="block py-3 px-4 rounded-lg hover:bg-azwara-lightest text-lg font-medium">Home</a>
            <a href="#about" class="block py-3 px-4 rounded-lg hover:bg-azwara-lightest text-lg font-medium">About</a>
            <a href="#courses" class="block py-3 px-4 rounded-lg hover:bg-azwara-lightest text-lg font-medium">Course</a>
            <a href="#teachers" class="block py-3 px-4 rounded-lg hover:bg-azwara-lightest text-lg font-medium">Teachers</a>
            {{-- <a href="#testimonials" class="block py-3 px-4 rounded-lg hover:bg-azwara-lightest text-lg font-medium">Testimonial</a> --}}
            {{-- <a href="#tutorial" class="block py-3 px-4 rounded-lg hover:bg-azwara-lightest text-lg font-medium">Tutorial</a> --}}

            {{-- Auth Buttons di Mobile --}}
            <div class="pt-4 mt-4 border-t border-azwara-lighter">
                @auth
                    <a
                        href="{{ route('dashboard.redirect') }}"
                        class="block text-center py-3 px-4 rounded-lg bg-primary text-white font-medium hover:bg-azwara-darker transition duration-300 mb-2"
                    >
                        Dashboard
                    </a>
                @endauth

                @guest
                    <a
                        href="{{ route('login') }}"
                        class="block py-3 px-4 rounded-lg border-2 border-primary text-primary text-center font-medium hover:bg-primary hover:text-white transition duration-300 mb-3"
                    >
                        Login
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="block text-center py-3 px-4 rounded-lg bg-primary text-white font-medium hover:bg-azwara-darker transition duration-300"
                    >
                        Register
                    </a>
                @endguest
            </div>
        </div>
    </div>
</header>

