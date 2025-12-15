@extends('layouts.app')

@section('content')

{{-- <h1 class="text-2xl font-bold mb-6">{{ $course->name }}</h1>

<div class="bg-white dark:bg-azwara-darker shadow rounded-xl p-6 mb-8">

    <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('img/course-default.png') }}"
         class="w-full h-60 object-cover rounded-lg mb-4">

    <p class="text-gray-600 dark:text-gray-300 mb-4">
        {{ $course->description }}
    </p>

    <p class="text-sm text-gray-500 dark:text-gray-400">
        <strong>Tentor:</strong>
        {{ $course->teachers->count() ? $course->teachers->pluck('user.name')->join(', ') : '-' }}
    </p>
</div> --}}

<h1 class="text-xl font-bold mb-4">Daftar Pertemuan - {{ $course->name }}</h1>

<table class="w-full text-left border-collapse">
    <thead>
        <tr class="bg-gray-100 dark:bg-azwara-darker text-gray-700 dark:text-gray-300">
            <th class="p-3">Judul</th>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Jam</th>
            <th class="p-3">Zoom</th>
            <th class="p-3">Recording</th>
        </tr>
    </thead>

    <tbody>
        @foreach($course->meetings as $m)
        <tr class="border-b dark:border-gray-700">
            <td class="p-3">{{ $m->title }}</td>
            <td class="p-3">{{ \Carbon\Carbon::parse($m->start_datetime)->format('d M Y') }}</td>
            <td class="p-3">{{ \Carbon\Carbon::parse($m->start_datetime)->format('H:i') }}</td>
            <td class="p-3">
                @if($m->zoom_link)
                    <a href="{{ $m->zoom_link }}" class="text-blue-600" target="_blank">Zoom</a>
                @else
                    -
                @endif
            </td>
            <td class="p-3">
                @if($m->recording_url)
                    <a href="{{ $m->recording_url }}" class="text-green-600" target="_blank">View</a>
                @else
                    -
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
