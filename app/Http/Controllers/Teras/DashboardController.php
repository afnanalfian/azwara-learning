<?php

namespace App\Http\Controllers\Teras;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function admin()
    {

        return view('admin.dashboard');
    }

    public function tentor()
    {

        return view('tentor.dashboard');
    }

    public function siswa()
    {

        return view('siswa.dashboard');
    }
}
