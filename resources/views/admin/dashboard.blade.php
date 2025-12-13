@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="min-h-screen p-8 transition-colors duration-300">
    <h1 class="text-xl font-bold text-azwara-darkest dark:text-azwara-lighter">
        Dashboard Admin
    </h1>

    <p class="mt-4 text-azwara-medium dark:text-azwara-light">
        Selamat datang di panel admin Azwara Learning.
    </p>

    <!-- Tombol toggle dark/light -->
    <button
        onclick="document.documentElement.classList.toggle('dark')"
        class="mt-6 px-4 py-2 rounded bg-azwara-medium text-white dark:bg-azwara-light dark:text-azwara-darkest transition-colors duration-300"
    >
        Toggle Dark Mode
    </button>
</div>
@endsection
