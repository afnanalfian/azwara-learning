@extends('layouts.app')

@section('content')

{{-- Tombol Kembali --}}
<a href="{{ route('tentor.index') }}"
    class="inline-flex items-center gap-2
            text-sm font-medium
            text-azwara-darkest dark:text-azwara-lighter
            hover:text-primary
            transition">

    {{-- Panah kiri --}}
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
<div class="px-4 py-8 max-w-2xl mx-auto">

    <h1 class="text-2xl font-bold mb-6 text-azwara-darkest dark:text-azwara-lighter">
        Tambah Tentor
    </h1>

    <div class="bg-white dark:bg-azwara-darker border border-gray-200 dark:border-azwara-darkest
                rounded-3xl shadow-xl p-6 sm:p-8">

        <form method="POST" action="{{ route('tentor.store') }}" class="space-y-6">
            @csrf

            {{-- User --}}
            <div>
                <label class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                    Pilih User
                </label>
                <select name="user_id"
                    class="w-full mt-1 p-3 rounded-xl border
                           bg-white dark:bg-azwara-darkest border-gray-300 dark:border-gray-700
                           text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary/40">
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Courses --}}
            <div>
                <label class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                    Course yang diajar
                </label>

                <select name="course_id[]" id="course-select" multiple
                    class="w-full mt-1 p-3 rounded-xl border
                           bg-white dark:bg-azwara-darkest border-gray-300 dark:border-gray-700
                           text-gray-800 dark:text-gray-200">
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Bio --}}
            <div>
                <label class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                    Bio Tentor
                </label>
                <textarea name="bio" rows="4"
                    class="w-full mt-1 p-3 rounded-xl border
                           bg-white dark:bg-azwara-darkest border-gray-300 dark:border-gray-700
                           text-gray-800 dark:text-gray-200
                           focus:ring-2 focus:ring-primary/40 focus:border-primary">{{ trim(old('bio')) }}</textarea>
            </div>

            {{-- Submit --}}
            <button
                class="px-5 py-3 bg-primary text-white rounded-xl font-medium
                       hover:bg-primary/90 transition w-full sm:w-auto">
                Simpan
            </button>

        </form>
    </div>

</div>

@endsection


{{-- TOMSELECT --}}
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<style>
    .ts-control {
        border-radius: 0.75rem !important;
        padding: 10px !important;
        min-height: 48px;
    }

    .ts-control input {
        color: inherit !important;
    }

    .ts-dropdown {
        border-radius: 0.75rem !important;
    }

    /* Dark mode styling */
    html.dark .ts-control,
    html.dark .ts-dropdown {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html.dark .ts-dropdown .option {
        color: #e2e8f0 !important;
    }

    html.dark .ts-dropdown .option.active {
        background-color: #1e293b !important;
    }
</style>

<script>
    new TomSelect("#course-select", {
        plugins: ['remove_button'],
        placeholder: "Pilih course (opsional)",
    });
</script>
@endpush
