<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year', now()->year);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $meetings = Meeting::with('course')
            ->whereBetween('scheduled_at', [$startOfMonth, $endOfMonth])
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn ($m) => $m->scheduled_at->format('Y-m-d'));

        return view('schedule.index', [
            'meetings' => $meetings,
            'month'    => $month,
            'year'     => $year,
            'carbon'   => $startOfMonth,
        ]);
    }
}
