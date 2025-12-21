<?php

namespace App\Http\Controllers\Teras;

use App\Http\Controllers\Controller;
use App\Models\Course;

class LandingController extends Controller
{
    public function index()
    {

        return view('front.landing');
    }
}
