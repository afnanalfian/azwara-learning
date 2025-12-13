@extends('layouts.app')

@section('content')

<div class="min-h-screen">

    <div class="max-w-2xl mx-auto">

        {{-- Back --}}
        <div class="mb-5">
            <a href="{{ route('tentor.show', $teacher->id) }}"
               class="inline-flex items-center gap-2 text-sm font-medium
                      text-azwara-darker dark:text-azwara-lighter hover:text-primary">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="bg-white dark:bg-azwara-darker/80 border border-gray-200 dark:border-azwara-darkest/50
                    rounded-2xl shadow-lg p-5 sm:p-6 backdrop-blur">

            {{-- Header --}}
            <div class="flex items-center gap-4">
                <img src="{{ $teacher->user->avatar_url }}"
                     class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover shadow"/>

                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-azwara-darkest dark:text-azwara-lighter">
                        Edit Tentor
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-gray-300 truncate">
                        {{ $teacher->user->name }}
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $teacher->user->email }} · {{ $teacher->user->phone }}
                    </p>
                </div>
            </div>

            <div class="my-5 border-t border-gray-200 dark:border-gray-700"></div>

            {{-- Form --}}
            <form method="POST" action="{{ route('tentor.update', $teacher->id) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Bio --}}
                <div>
                    <label class="font-semibold text-sm dark:text-azwara-lighter">Bio Tentor</label>
                    <textarea name="bio" rows="4"
                        class="w-full mt-1 p-3 rounded-xl border bg-gray-50 dark:bg-azwara-darkest/60
                            text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700
                            focus:ring-2 focus:ring-primary/40 focus:border-primary">
                            {{ trim(old('bio', $teacher->bio)) }}
                    </textarea>
                </div>

                {{-- Courses --}}
                <div>
                    <label class="font-semibold text-sm  dark:text-azwara-lighter">Mengajar Course</label>

                    <select name="course_id[]" multiple id="course-select"
                        class="w-full mt-1 p-2 rounded-xl border
                            bg-gray-50 dark:bg-azwara-darkest/60
                            text-gray-800 dark:text-gray-200
                            border-gray-300 dark:border-gray-700">
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}"
                                {{ in_array($c->id, $teacher->courses->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Save --}}
                <div class="pt-3">
                    <button class="w-full sm:w-auto px-5 py-2.5 bg-primary text-white rounded-xl
                                   font-medium text-sm hover:bg-primary/90 transition">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- TOMSELECT --}}
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<script>
    new TomSelect('#course-select', {
        plugins: ['remove_button'],
        placeholder: "Pilih course",
    });
</script>
@endpush
@push('styles')
<style>
    /* ----- TOM SELECT LIGHT MODE ----- */
    .ts-control {
        background-color: #f9fafb !important; /* gray-50 */
        border-radius: 0.75rem !important; /* rounded-xl */
        border-color: #d1d5db !important; /* gray-300 */
        padding: 0.5rem 0.75rem !important;
        min-height: 42px !important;
        color: #111827 !important; /* gray-900 */
    }

    .ts-dropdown {
        background-color: white !important;
        border-color: #d1d5db !important;
        border-radius: 0.75rem !important;
    }

    .ts-dropdown .ts-option {
        padding: 8px 12px;
        border-radius: 6px;
    }

    .ts-dropdown .ts-option:hover {
        background-color: #eff6ff !important; /* blue-50 */
    }

    /* tags (selected item) */
    .ts-control .item {
        background-color: #5483B3 !important;
        color: white !important;
        border-radius: 0.5rem !important;
        padding: 2px 8px !important;
    }

    /* remove button on tag */
    .ts-control .item .remove {
        color: white !important;
        margin-left: 4px;
    }

    /* ----- DARK MODE ----- */
    html.dark .ts-control {
        background-color: rgba(2, 16, 36, 0.5) !important; /* dark glassy */
        border-color: #1e293b !important; /* slate-800 */
        color: #e5e7eb !important; /* gray-200 */
    }

    html.dark .ts-dropdown {
        background-color: #021024 !important;
        border-color: #1e293b !important;
    }

    html.dark .ts-dropdown .ts-option {
        color: #e5e7eb !important;
    }

    html.dark .ts-dropdown .ts-option:hover {
        background-color: rgba(84, 131, 179, 0.25) !important; /* primary 25% */
    }

    html.dark .ts-control .item {
        background-color: #5483B3 !important;
        color: white !important;
    }
    /* ----- FIX: SEARCH INPUT IN DARK MODE ----- */
    html.dark .ts-dropdown .ts-input input,
    html.dark .ts-control input {
        color: #e5e7eb !important;        /* text-gray-200 */
    }

    html.dark .ts-dropdown .ts-input input::placeholder,
    html.dark .ts-control input::placeholder {
        color: #9ca3af !important;        /* gray-400 */
    }

    /* Light mode placeholder (biar rapi juga) */
    .ts-dropdown .ts-input input::placeholder,
    .ts-control input::placeholder {
        color: #6b7280 !important;        /* gray-500 */
    }
</style>
@endpush
@endsection
