@extends('layouts.app')

@section('content')
<div class="min-h-screen">

    <div class="max-w-3xl mx-auto">
        {{-- Tombol Kembali --}}
        <div class="mb-4">
            <a href="{{ route('profile.show') }}"
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
        </div>
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
                    Edit Profil
                </h1>

                <p class="text-sm mt-1
                          text-azwara-medium dark:text-azwara-light">
                    Perbarui informasi akun Anda
                </p>
            </div>

            <form method="POST" enctype="multipart/form-data"
                  action="{{ route('profile.update') }}"
                  class="space-y-5">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="form-label-required
                                  block text-sm font-medium
                                  text-azwara-darkest dark:text-azwara-light">
                        Nama Lengkap
                    </label>

                    <input type="text" name="name"
                           value="{{ auth()->user()->name }}"
                           class="input-primary">
                </div>

                {{-- Phone --}}
                <div>
                    <label class="form-label-required
                                  block text-sm font-medium
                                  text-azwara-darkest dark:text-azwara-light">
                        No. HP
                    </label>

                    <input type="text" name="phone"
                           value="{{ auth()->user()->phone }}"
                           class="input-primary">
                </div>

                {{-- PROVINCE --}}
                <div>
                    <label class="block text-sm font-medium
                                text-azwara-darkest dark:text-azwara-light">
                        Provinsi
                        <span class="text-xs text-gray-500">(opsional)</span>
                    </label>

                    <select id="province_id" name="province_id" class="select2 input-primary">
                        <option value="">-- pilih provinsi --</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov->id }}"
                                {{ auth()->user()->province_id == $prov->id ? 'selected' : '' }}>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- REGENCY --}}
                <div>
                    <label class="block text-sm font-medium
                                text-azwara-darkest dark:text-azwara-light">
                        Kabupaten / Kota
                        <span class="text-xs text-gray-500">(opsional)</span>
                    </label>

                    <select id="regency_id" name="regency_id" class="select2 input-primary">
                        <option value="">Pilih provinsi terlebih dahulu</option>
                    </select>
                </div>

                {{-- Avatar --}}
                <div>
                    <label
                        class="block text-sm font-medium
                               text-azwara-darkest dark:text-azwara-light">
                        Foto Profil
                    </label>

                    <input type="file" name="avatar" class="input-file">
                </div>

                {{-- Action --}}
                <div class="pt-4">
                    <button type="submit"
                            class="btn-primary w-full">
                        Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {

    const provinceSelect = document.getElementById("province_id");
    const regencySelect = document.getElementById("regency_id");
    const userRegency = "{{ auth()->user()->regency_id }}";

    function loadRegencies() {
        const provinceId = provinceSelect.value;

        regencySelect.innerHTML = `<option value="">Loading...</option>`;

        if (!provinceId) {
            regencySelect.innerHTML = `<option value="">Pilih provinsi terlebih dahulu</option>`;
            return;
        }

        fetch(`/get-regencies/${provinceId}`)
            .then(res => res.json())
            .then(data => {
                regencySelect.innerHTML = `<option value="">-- pilih kabupaten/kota --</option>`;

                data.forEach(reg => {
                    regencySelect.innerHTML += `
                        <option value="${reg.id}" ${reg.id == userRegency ? 'selected' : ''}>
                            ${reg.name}
                        </option>`;
                });
            });
    }

    provinceSelect.addEventListener("change", loadRegencies);

    // Jika user sudah punya province, otomatis load regency
    if ("{{ auth()->user()->province_id }}") {
        loadRegencies();
    }
});
</script>
@endpush
