@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-4 space-y-6 overflow-x-hidden">

    {{-- HEADER --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white">
                Student Dashboard
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Welcome back, {{ auth()->user()->name }}! Here's your learning overview.
            </p>
        </div>
        <span class="self-start sm:self-auto px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-lg text-sm font-medium">
            {{ now()->format('d M Y') }}
        </span>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label'=>'My Courses','value'=>$stats['total_course'],'icon'=>'📚','color'=>'blue'],
                ['label'=>'Total Meetings','value'=>$stats['done_meeting'].'/'.$stats['total_meeting'],'icon'=>'🎯','color'=>'purple'],
                ['label'=>"Today's Quiz",'value'=>$todayQuizzes->count(),'icon'=>'✏️','color'=>'green'],
                ['label'=>'Active Assignments','value'=>$stats['active_quiz'],'icon'=>'📝','color'=>'orange'],
            ];
        @endphp
        @foreach($cards as $c)
        <div class="rounded-xl p-4 border bg-azwara-lighter dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">{{ $c['label'] }}</p>
                    <p class="text-xl font-bold text-gray-800 dark:text-white">{{ $c['value'] }}</p>
                </div>
                <div class="text-2xl">{{ $c['icon'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- MY COURSES --}}
    <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-4 border dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800 dark:text-white">My Courses</h3>
            <a href="{{ route('course.index') }}" class="text-sm text-blue-600">View all</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($myCourses as $course)
            <div onclick="window.location='{{ route('course.show',$course->slug) }}'"
                 class="cursor-pointer rounded-xl border p-3 dark:border-gray-700 hover:border-blue-400 transition">
                <div class="h-32 rounded-lg bg-gray-200 dark:bg-gray-700 mb-2 overflow-hidden">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-3xl">📘</div>
                    @endif
                </div>
                <p class="font-medium text-gray-800 dark:text-white truncate">{{ $course->name }}</p>
                <p class="text-xs text-gray-500">{{ $course->meetings_count }} meetings</p>
            </div>
            @empty
            <div class="col-span-full text-center text-gray-500 py-6">
                No courses enrolled
            </div>
            @endforelse
        </div>
    </div>

    {{-- CONTENT GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- MEETINGS --}}
        <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-4 border dark:border-gray-700">
            <h3 class="font-semibold mb-4 text-gray-800 dark:text-white">
                Meetings This Week
            </h3>

            <div class="space-y-3">
                @forelse($weeklyMeetings as $m)
                <div onclick="window.location='{{ route('meeting.show',$m) }}'"
                     class="border rounded-lg p-3 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition cursor-pointer">
                    <p class="font-medium text-gray-800 dark:text-white">{{ $m->title }}</p>
                    <p class="text-xs text-gray-500">{{ $m->course->name ?? '-' }}</p>
                    <div class="flex justify-between text-xs mt-2 text-gray-500">
                        <span>{{ ucfirst($m->status) }}</span>
                        <span>{{ $m->scheduled_at->format('d M H:i') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-6">No meetings</div>
                @endforelse
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="space-y-6">

            {{-- TODAY QUIZ --}}
            <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-4 border dark:border-gray-700">
                <h3 class="font-semibold mb-4 text-gray-800 dark:text-white">Today's Quiz</h3>
                <div class="space-y-3">
                    @forelse($todayQuizzes as $quiz)
                    <div class="border rounded-lg p-3 flex justify-between items-center dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">{{ $quiz->title }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $quiz->exam_date->format('H:i') }} • {{ $quiz->duration_minutes }} min
                            </p>
                        </div>
                        <a href="{{ route('exams.show',$quiz) }}"
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded-lg">
                            Start
                        </a>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">No quizzes today</div>
                    @endforelse
                </div>
            </div>

            {{-- TRYOUTS --}}
            <div class="bg-azwara-lightest dark:bg-gray-800 rounded-2xl p-4 border dark:border-gray-700">
                <div class="flex justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Upcoming Tryouts</h3>
                    <a href="{{ route('exams.index',['type'=>'tryout']) }}" class="text-sm text-blue-600">View all</a>
                </div>

                <div class="space-y-3">
                    @forelse($upcomingTryouts as $t)
                    <div class="border rounded-lg p-3 dark:border-gray-700">
                        <p class="font-medium text-gray-800 dark:text-white">{{ $t->title }}</p>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ $t->exam_date->format('d M Y') }}</span>
                            <span>{{ $t->duration_minutes }} min</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">No upcoming tryouts</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
