{{-- resources/views/front/sections/about.blade.php --}}
<section id="about" class="scroll-mt-20 py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center lg:items-start gap-12 lg:gap-16">
            {{-- Image Section --}}
            <div class="w-full lg:w-1/2">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    {{-- Main Image --}}
                    <img
                        src="{{ asset('front/img/about-1.png') }}"
                        alt="Tentang Azwara Learning"
                        class="w-full h-auto object-cover transform hover:scale-105 transition-transform duration-700"
                    >
                </div>
            </div>

            {{-- Content Section --}}
            <div class="w-full lg:w-1/2">
                <div class="max-w-2xl">
                    {{-- Section Label --}}
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-6">
                        Tentang Kami
                    </span>

                    {{-- Title --}}
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-azwara-darkest mb-6 leading-tight">
                        Memberikan Pendidikan Terbaik untuk
                        <span class="text-primary">Masa Depan Cerah</span>
                    </h2>

                    {{-- Description --}}
                    <div class="space-y-4 mb-8">
                        <p class="text-secondary text-md leading-relaxed">
                            Azwara Learning adalah platform bimbingan belajar profesional yang berkomitmen untuk memberikan pendidikan berkualitas tinggi dengan metode pengajaran yang inovatif dan efektif.
                        </p>
                        {{-- <p class="text-secondary text-md leading-relaxed">
                            Kami percaya bahwa setiap siswa memiliki potensi unik. Dengan pendekatan personalized learning, kami membantu siswa menemukan cara belajar yang paling sesuai dengan kebutuhan mereka.
                        </p> --}}
                    </div>

                    {{-- Features List --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10">
                        {{-- <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-azwara-darkest mb-1">Tentor Berpengalaman</h4>
                                <p class="text-secondary text-sm">Tentor profesional dengan sertifikasi dan pengalaman mengajar.</p>
                            </div>
                        </div> --}}

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-azwara-darkest mb-1">Materi Terupdate</h4>
                                <p class="text-secondary text-sm">Kurikulum sesuai dengan perkembangan terbaru pendidikan.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-azwara-darkest mb-1">Kelas Interaktif</h4>
                                <p class="text-secondary text-sm">Sesi belajar dua arah dengan diskusi aktif dan interaktif.</p>
                            </div>
                        </div>

                        {{-- <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-azwara-darkest mb-1">Fleksibel Waktu</h4>
                                <p class="text-secondary text-sm">Jadwal belajar yang dapat disesuaikan dengan kebutuhan siswa.</p>
                            </div>
                        </div> --}}
                    </div>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a
                            href="https://wa.me/6285141339645?text=Halo%20saya%20tertarik%20dengan%kelas%atau%tryout%di%Azwara%Learning"
                            target="_blank"
                            class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-primary text-white font-semibold rounded-lg hover:bg-azwara-medium transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl shadow-lg group"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.893-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                            </svg>
                            <span class="text-lg">Konsultasi via WhatsApp</span>
                            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>

                        {{-- <a
                            href="#courses"
                            class="inline-flex items-center justify-center gap-3 px-8 py-4 border-2 border-primary text-primary font-semibold rounded-lg hover:bg-primary hover:text-white transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="text-lg">Lihat Kelas Kami</span>
                        </a> --}}
                    </div>

                    {{-- Additional Info
                    <div class="mt-12 pt-8 border-t border-azwara-lighter">
                        <div class="flex flex-wrap gap-8">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-primary mb-2">1000+</p>
                                <p class="text-secondary">Siswa Aktif</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-primary mb-2">50+</p>
                                <p class="text-secondary">Tentor Profesional</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-primary mb-2">95%</p>
                                <p class="text-secondary">Kepuasan Siswa</p>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</section>
