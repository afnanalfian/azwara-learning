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

        {{-- Danger Card --}}
        <div
            class="bg-white dark:bg-azwara-darker
                   border border-red-200 dark:border-red-500/30
                   rounded-3xl
                   shadow-xl dark:shadow-black/40
                   p-6 sm:p-8 transition-colors duration-300">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-red-600">
                    Hapus Akun
                </h1>

                <p class="mt-2 text-sm
                          text-gray-600 dark:text-gray-300">
                    Tindakan ini bersifat <strong>serius</strong>.
                    Setelah akun dihapus, semua sesi pembelian akan hangus.
                    Namun akun masih dapat diaktifkan kembali dalam waktu
                    <strong>10 hari</strong> dengan login ulang.
                </p>
            </div>

            <form method="POST"
                action="{{ route('profile.destroy') }}"
                x-data="{ other: false }"
                class="space-y-6 sweet-confirm"
                data-message="Yakin ingin menghapus akun? Akun masih bisa diaktifkan kembali dalam 10 hari.">
                @csrf

                {{-- Reason --}}
                <div>
                    <label class="form-label-required block text-sm font-medium
                                text-azwara-darkest dark:text-azwara-light">
                        Alasan Penghapusan
                    </label>

                    <select name="reason_option"
                            x-on:change="other = ($event.target.value === 'Lainnya')"
                            class="input-primary">
                        <option value="">-- pilih alasan --</option>
                        <option value="Terlalu mahal">Terlalu mahal</option>
                        <option value="Tidak sesuai kebutuhan">Tidak sesuai kebutuhan</option>
                        <option value="Jarang digunakan">Jarang digunakan</option>
                        <option value="Pindah platform lain">Pindah platform lain</option>
                        <option value="Lainnya">Lainnya...</option>
                    </select>

                    {{-- Custom reason --}}
                    <input type="text"
                        x-show="other"
                        x-transition
                        name="reason_custom"
                        placeholder="Tuliskan alasan Anda..."
                        class="input-primary mt-3">
                </div>

                {{-- Warning Box --}}
                <div class="rounded-xl bg-red-50 dark:bg-red-500/10
                            border border-red-200 dark:border-red-500/30 p-4 text-sm
                            text-red-600 dark:text-red-400">
                    ⚠️ Pastikan Anda benar-benar yakin sebelum melanjutkan.
                </div>

                {{-- Action --}}
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-red-600 hover:bg-red-700
                            text-white font-semibold transition">
                    Hapus Akun Permanen
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
