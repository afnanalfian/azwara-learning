@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 px-4 sm:px-0">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-azwara-darker dark:text-white">
                Hak Akses Siswa
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Ringkasan hak akses course, meeting, tryout, dan quiz
            </p>
        </div>

        {{-- SEARCH --}}
        <form method="GET" class="w-full sm:w-72">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Cari nama / email siswa..."
                class="w-full rounded-xl
                       border-gray-300 dark:border-white/10
                       bg-azwara-lightest dark:bg-azwara-darkest
                       text-sm dark:text-white
                       focus:ring-primary focus:border-primary">
        </form>
    </div>

    {{-- LIST --}}
    <div class="space-y-4">

        @forelse($users as $user)
            <div
                class="rounded-2xl p-5
                       bg-azwara-lightest dark:bg-azwara-darkest
                       border border-gray-200 dark:border-azwara-darker">

                {{-- USER HEADER --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            {{ $user->name }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </p>
                    </div>

                    <span class="text-xs px-3 py-1 rounded-full
                                 bg-primary/10 text-primary
                                 font-medium self-start sm:self-auto">
                        {{ $user->entitlements->count() }} akses
                    </span>
                </div>

                {{-- ENTITLEMENTS --}}
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    @forelse($user->entitlements as $entitlement)
                        <div
                            class="rounded-xl p-3
                                   border border-gray-200 dark:border-azwara-darker
                                   bg-gray-50 dark:bg-azwara-darker/50">

                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase
                                             text-gray-600 dark:text-gray-300">
                                    {{ $entitlement->entitlement_type }}
                                </span>

                                <span class="text-[11px] px-2 py-0.5 rounded
                                    {{ $entitlement->source === 'purchase'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300'
                                        : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' }}">
                                    {{ ucfirst($entitlement->source) }}
                                </span>
                            </div>

                            <div class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                @if($entitlement->entitlement_id)
                                    ID #{{ $entitlement->entitlement_id }}
                                @else
                                    Global Access
                                @endif
                            </div>

                            @if($entitlement->expires_at)
                                <div class="mt-1 text-xs text-red-600 dark:text-red-400">
                                    Exp: {{ $entitlement->expires_at->format('d M Y') }}
                                </div>
                            @else
                                <div class="mt-1 text-xs text-gray-400">
                                    Tidak kadaluarsa
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-sm text-gray-500 dark:text-gray-400 italic">
                            Tidak memiliki hak akses
                        </div>
                    @endforelse

                </div>
            </div>
        @empty
            <div class="py-12 text-center text-gray-500 dark:text-gray-400">
                Tidak ada data siswa.
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="pt-4">
        {{ $users->links() }}
    </div>

</div>
@endsection
