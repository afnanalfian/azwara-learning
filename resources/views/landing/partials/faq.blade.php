<section id="faq" class="py-20">
    <div class="max-w-3xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">
            Pertanyaan Umum
        </h2>

        <div class="space-y-4">

            <div x-data="{ open: false }" class="bg-white rounded-lg shadow p-5">
                <button @click="open = !open"
                        class="w-full flex justify-between font-semibold">
                    Apakah kelas bisa diakses ulang?
                    <span x-text="open ? '-' : '+'"></span>
                </button>
                <p x-show="open" x-transition class="mt-3 text-gray-600 text-sm">
                    Ya, semua video dan materi bisa diakses selama masa aktif.
                </p>
            </div>

            <div x-data="{ open: false }" class="bg-white rounded-lg shadow p-5">
                <button @click="open = !open"
                        class="w-full flex justify-between font-semibold">
                    Metode pembayarannya apa saja?
                    <span x-text="open ? '-' : '+'"></span>
                </button>
                <p x-show="open" x-transition class="mt-3 text-gray-600 text-sm">
                    Saat ini tersedia QRIS dan pembayaran manual.
                </p>
            </div>

        </div>
    </div>
</section>
