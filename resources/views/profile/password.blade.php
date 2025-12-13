@extends('layouts.app')

@section('content')
<div class="min-h-screen">

    <div class="max-w-xl mx-auto">

        {{-- Tombol Kembali --}}
        <div class="mb-4">
            <a href="{{ route('profile.show') }}"
               class="inline-flex items-center gap-2
                      text-sm font-medium
                      text-azwara-darkest dark:text-azwara-lighter
                      hover:text-primary
                      transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4"
                     fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7" />
                </svg>

                Kembali
            </a>
        </div>

        {{-- Card --}}
        <div
            class="bg-white dark:bg-azwara-darker
                   border border-gray-200 dark:border-azwara-darkest
                   rounded-3xl
                   shadow-xl dark:shadow-black/30
                   p-6 sm:p-8 transition-colors duration-300">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold
                           text-azwara-darkest dark:text-azwara-lighter">
                    Ganti Password
                </h1>

                <p class="text-sm mt-1
                          text-azwara-medium dark:text-azwara-light">
                    Perbarui kata sandi akun Anda
                </p>
            </div>

            <form method="POST"
                  action="{{ route('profile.password.update') }}"
                  class="space-y-5">
                @csrf

                {{-- Password Saat Ini --}}
                <div>
                    <label class="form-label-required
                                  block text-sm font-medium
                                  text-azwara-darkest dark:text-azwara-light">
                        Password Saat Ini
                    </label>

                    <input type="password"
                           name="current_password"
                           class="input-primary"
                           autocomplete="current-password">
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="form-label-required
                                  block text-sm font-medium
                                  text-azwara-darkest dark:text-azwara-light">
                        Password Baru
                    </label>

                    <input type="password"
                           name="password"
                           class="input-primary"
                           autocomplete="new-password">
                </div>

                {{-- Konfirmasi --}}
                <div>
                    <label class="form-label-required
                                  block text-sm font-medium
                                  text-azwara-darkest dark:text-azwara-light">
                        Konfirmasi Password
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           class="input-primary"
                           autocomplete="new-password">
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button type="submit"
                            class="btn-primary w-full">
                        Update Password
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
