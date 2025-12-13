<div
    id="sidebar-backdrop"
    onclick="console.log('BACKDROP CLICKED'); toggleSidebar()"
    class="fixed inset-0 bg-black/40 hidden md:hidden z-40">
</div>

<aside id="sidebar"
       class="fixed md:static z-50 inset-y-0 left-0 w-64 bg-azwara-lighter dark:bg-azwara-darker
              border-r border-gray-200 dark:border-azwara-darkest
              transform -translate-x-full md:translate-x-0">

    <div class="flex flex-col items-center py-6 gap-4">

        {{-- Logo --}}
        <a href="{{ route('dashboard.redirect') }}">
            <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-azwara-medium shadow">
                <img src="{{ asset('img/logo.png') }}"
                    alt="Logo"
                    class="object-cover w-full h-full">
            </div>
        </a>

        {{-- @if(!request()->is('siswa/*'))
        <h2 class="text-lg font-bold text-azwara-darkest dark:text-azwara-lighter">
            Azwara
        </h2>
        @endif --}}

    </div>

    {{-- Menu --}}
    <nav class="px-6 flex flex-col gap-2 text-azwara-darkest dark:text-azwara-lighter">

        @role('admin')
            @include('layouts.partials.menus.admin')
        @endrole

        @role('tentor')
            @include('layouts.partials.menus.tentor')
        @endrole

        @role('siswa')
            @include('layouts.partials.menus.siswa')
        @endrole

    </nav>
</aside>
