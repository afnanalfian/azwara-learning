@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-azwara-darker dark:text-white">
            Pengaturan Pembayaran
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            QRIS dan instruksi pembayaran manual
        </p>
    </div>

    <form method="POST"
          action="{{ route('payment.settings.update') }}"
          enctype="multipart/form-data"
          class="space-y-6 p-6 rounded-2xl border
                 dark:border-azwara-darker
                 bg-azwara-lightest dark:bg-azwara-darkest">

        @csrf

        {{-- QRIS IMAGE DISPLAY --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                QRIS
            </label>

            @if($settings['qris_image'] ?? null)
                <img src="{{ asset('storage/' . $settings['qris_image']) }}"
                     class="mt-3 max-w-xs rounded-xl border">
            @endif

            <input type="file" name="qris_image"
                accept="image/*"
                class="mt-3 block w-full text-sm
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-xl file:border-0
                        file:bg-primary file:text-white
                        hover:file:bg-azwara-medium
                        dark:file:bg-gray-700 dark:file:text-gray-200
                        dark:hover:file:bg-gray-600">

        </div>

        {{-- PAYMENT INSTRUCTION --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Instruksi Pembayaran
            </label>

            <textarea name="payment_instruction" rows="4"
                      class="mt-1 w-full rounded-xl border-gray-300 dark:text-white bg-azwara-lightest
                             dark:bg-azwara-darkest dark:border-azwara-darker
                             focus:ring-primary focus:border-primary">{{ old('payment_instruction', $settings['payment_instruction'] ?? '') }}</textarea>
        </div>

        {{-- PAYMENT METHOD --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Metode Pembayaran Aktif
            </label>

            <select name="active_payment_method"
                    class="w-full rounded-xl bg-azwara-lightest border-gray-300 dark:text-white
                           dark:bg-azwara-darkest dark:border-azwara-darker
                           focus:ring-primary focus:border-primary">

                <option value="manual_qris"
                    @selected(($settings['active_payment_method'] ?? '') === 'manual_qris')>
                    Manual QRIS
                </option>

                <option value="midtrans"
                    @selected(($settings['active_payment_method'] ?? '') === 'midtrans')>
                    Midtrans (Online)
                </option>

            </select>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl
                           bg-primary hover:bg-azwara-medium
                           text-white font-semibold transition">
                Simpan Pengaturan
            </button>
        </div>

    </form>

</div>
@endsection
