<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Bimbel Online')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-azwara-lightest text-azwara-darkest">

<header class="bg-azwara-darkest text-white">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <span class="text-xl font-bold">AZWARA BIMBEL</span>
        <nav class="space-x-6 text-sm">
            <a href="#courses" class="hover:text-azwara-lighter">Kelas</a>
            <a href="#faq" class="hover:text-azwara-lighter">FAQ</a>
            <a href="{{ route('tutorial') }}" class="hover:text-azwara-lighter">Tutorial</a>
        </nav>
    </div>
</header>

<main>
    @yield('content')
</main>

<footer class="bg-azwara-darker text-white mt-20">
    <div class="max-w-7xl mx-auto px-6 py-10 text-center text-sm">
        © {{ date('Y') }} Azwara Bimbel. All rights reserved.
    </div>
</footer>

</body>
</html>
