<?php

use Illuminate\Support\Facades\Route;
use App\Models\Regency;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;


Route::get('/regencies/{province_id}', function ($province_id) {
    return response()->json(
        Regency::where('province_id', $province_id)
            ->orderBy('name')
            ->get(['id', 'name'])
    );
})->withoutMiddleware([
    EnsureFrontendRequestsAreStateful::class,
]);
