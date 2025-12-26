{{-- ================= DASHBOARD ================= --}}
<a href="{{ route('dashboard.redirect') }}"
   class="menu-item {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 9.75L12 4.5l9 5.25V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/>
    </svg>
    Dashboard
</a>

{{-- ================= SISWA ================= --}}
<a href="{{ route('siswa.index') }}"
   class="menu-item {{ request()->routeIs('siswa.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 14l9-5-9-5-9 5 9 5zm0 0v7m0-7l-9-5m9 5l9-5"/>
    </svg>
    Siswa
</a>

{{-- ================= COURSE ================= --}}
<a href="{{ route('course.index') }}"
   class="menu-item {{ request()->routeIs('course.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 5a2 2 0 012-2h11a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M7 5v16"/>
    </svg>
    Course
</a>
{{-- ================= SCHEDULE ================= --}}
<a href="{{ route('schedule.index') }}"
   class="menu-item {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <!-- Icon kalender -->
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0
                 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    Schedule
</a>

{{-- ================= BANK SOAL ================= --}}
<a href="{{ route('bank.category.index') }}"
   class="menu-item {{ request()->routeIs('bank.*','questions.*') ? 'active' : '' }}">
    <svg class="menu-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M20 9V5a2 2 0 00-2-2H6a2 2 0 00-2 2v4m16 0H4m16 0v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9"/>
    </svg>
    Bank Soal
</a>
