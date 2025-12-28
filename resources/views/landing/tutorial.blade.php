@extends('layouts.landing')

@section('title', 'Tutorial')

@section('content')
<section class="py-20">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-3xl font-bold mb-10">
            Tutorial Penggunaan
        </h1>

        <div class="space-y-10">

            <div>
                <h2 class="text-xl font-semibold mb-3 text-azwara-secondary">
                    Cara Pembelian
                </h2>
                <ol class="list-decimal ml-6 text-gray-700 space-y-2">
                    <li>Daftar atau login akun</li>
                    <li>Pilih kelas yang diinginkan</li>
                    <li>Klik daftar / beli</li>
                    <li>Lakukan pembayaran</li>
                    <li>Tunggu verifikasi</li>
                </ol>
            </div>

            <div>
                <h2 class="text-xl font-semibold mb-3 text-azwara-secondary">
                    Cara Menggunakan Website
                </h2>
                <ol class="list-decimal ml-6 text-gray-700 space-y-2">
                    <li>Akses menu kelas</li>
                    <li>Ikuti live meeting</li>
                    <li>Tonton video rekaman</li>
                    <li>Kerjakan tryout & quiz</li>
                </ol>
            </div>

        </div>
    </div>
</section>
@endsection
