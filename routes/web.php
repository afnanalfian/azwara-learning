<?php

use Illuminate\Support\Facades\Route;
use App\Models\QuestionCategory;
use App\Models\QuestionMaterial;
use App\Http\Controllers\Front\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TentorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingMaterialController;
use App\Http\Controllers\MeetingPostTestController;
use App\Http\Controllers\MeetingPostTestAttemptController;
use App\Http\Controllers\MeetingAttendanceController;
use App\Http\Controllers\MeetingVideoController;
use App\Http\Controllers\BunnyWebHookController;
use App\Http\Controllers\QuestionCategoryController;
use App\Http\Controllers\QuestionMaterialController;
use App\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
/*
|--------------------------------------------------------------------------
| AUTH DAN API ROUTES
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
Route::middleware(['auth', 'role:admin|tentor'])
    ->get('/ajax/categories/{category}/materials', function ($category) {

        $category = QuestionCategory::withTrashed()->findOrFail($category);

        return response()->json(
            $category->materials()
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    })
    ->name('ajax.categories.materials');
Route::middleware(['auth', 'role:admin|tentor'])
    ->get('/ajax/post-tests/{postTest}/questions/by-material/{material}',
        function (
            \Illuminate\Http\Request $request,
            $postTest,
            $material
        ) {
            $postTest = \App\Models\MeetingPostTest::findOrFail($postTest);

            $material = QuestionMaterial::withTrashed()
                ->findOrFail($material);

            return app(MeetingPostTestController::class)
                ->questionsByMaterial($request, $postTest, $material);
        }
    )
    ->name('ajax.posttests.questions.byMaterial');

/*
|--------------------------------------------------------------------------
| DASHBOARD ROUTES
|--------------------------------------------------------------------------
*/
// helper dashboard redirect
Route::get('/dashboard-redirect', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('dashboard.admin');
    }

    if ($user->hasRole('tentor')) {
        return redirect()->route('dashboard.tentor');
    }

    return redirect()->route('dashboard.siswa'); // default
})->name('dashboard.redirect')->middleware(['auth', 'verified']);


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/get-regencies/{province_id}', function($province_id) {
        return \App\Models\Regency::where('province_id', $province_id)
            ->orderBy('id')
            ->get();
    })->name('get.regencies');
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ROUTES
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('dashboard.admin')
        ->middleware(['role:admin']);

    Route::get('/tentor/dashboard', [DashboardController::class, 'tentor'])
        ->name('dashboard.tentor')
        ->middleware(['role:tentor']);

    Route::get('/siswa/dashboard', [DashboardController::class, 'siswa'])
        ->name('dashboard.siswa')
        ->middleware(['role:siswa']);

    /*
    |--------------------------------------------------------------------------
    | PROFILES ROUTES
    |--------------------------------------------------------------------------
    */
        // Profile main
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Edit profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');

    // Change password
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Delete account (deactivate)
    Route::get('/profile/delete', [ProfileController::class, 'delete'])->name('profile.delete');
    Route::post('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | MANAGE STUDENTS AND TEACHERS ROUTES
    |--------------------------------------------------------------------------
    */
    // SISWA
    Route::prefix('siswa')->middleware(['role:admin'])->group(function () {

        Route::get('/', [SiswaController::class, 'index'])->name('siswa.index');
        Route::get('/{id}', [SiswaController::class, 'show'])->name('siswa.show');
        Route::post('/{id}/toggle', [SiswaController::class, 'toggleActive'])->name('siswa.toggle');
    });
    // TENTOR
    Route::prefix('tentor')->middleware(['role:admin'])->group(function () {
        Route::get('/add', [TentorController::class, 'create'])->name('tentor.create');
        Route::post('/store', [TentorController::class, 'store'])->name('tentor.store');
        Route::get('/{id}/edit', [TentorController::class, 'edit'])->name('tentor.edit');
        Route::put('/{id}', [TentorController::class, 'update'])->name('tentor.update');
        Route::delete('/{id}', [TentorController::class, 'remove'])->name('tentor.remove');
        Route::post('/{id}/toggle', [TentorController::class, 'toggleActive'])->name('tentor.toggle');
    });
    Route::prefix('tentor')->middleware(['role:admin|siswa'])->group(function () {
        Route::get('/', [TentorController::class, 'index'])->name('tentor.index');
        Route::get('/{id}', [TentorController::class, 'show'])->name('tentor.show');
    });

    /*
    |--------------------------------------------------------------------------
    | COURSE ROUTES
    |--------------------------------------------------------------------------
    */
    // Admin-only
    Route::prefix('course')->middleware(['role:admin'])->group(function () {
        Route::get('/create',            [CourseController::class, 'create'])->name('course.create');
        Route::post('/store',            [CourseController::class, 'store'])->name('course.store');
        Route::get('/{slug}/edit',       [CourseController::class, 'edit'])->name('course.edit');
        Route::post('/{slug}/update',    [CourseController::class, 'update'])->name('course.update');
        Route::delete('/{slug}/delete',  [CourseController::class, 'destroy'])->name('course.delete');
    });

    // All roles can view course
    Route::prefix('course')->middleware(['role:admin|tentor|siswa'])->group(function () {
        Route::get('/',            [CourseController::class, 'index'])->name('course.index');
        Route::get('/{slug}',      [CourseController::class, 'show'])->name('course.show');
    });

    /*
    |--------------------------------------------------------------------------
    | MEETING ROUTES
    |--------------------------------------------------------------------------
    */

    //ADMIN & TENTOR
    Route::middleware(['role:admin|tentor'])->group(function () {
        // CREATE & EDIT MEETING
        Route::get('/course/{course}/meetings/create',[MeetingController::class, 'create'])->name('meeting.create');
        Route::post('/course/{course}/meetings',[MeetingController::class, 'store'])->name('meeting.store');
        Route::get('/meetings/{meeting}/edit',[MeetingController::class, 'edit'])->name('meeting.edit');
        Route::put('/meetings/{meeting}', [MeetingController::class, 'update'])->name('meeting.update');

        // MEETING STATE
        Route::post('/meetings/{meeting}/start',[MeetingController::class, 'start'])->name('meeting.start');
        Route::post('/meetings/{meeting}/finish',[MeetingController::class, 'finish'])->name('meeting.finish');
        Route::post('/meetings/{meeting}/cancel',[MeetingController::class, 'cancel'])->name('meeting.cancel');

        // DELETE (SOFT DELETE)
        Route::delete('/meetings/{meeting}',[MeetingController::class, 'destroy'])->name('meeting.destroy');
    });

    // ALL ROLES (ADMIN / TENTOR / SISWA)
    Route::middleware(['role:admin|tentor|siswa'])->group(function () {
        // VIEW MEETING
        Route::get('/meetings/{meeting}',[MeetingController::class, 'show'])->name('meeting.show');

        // JOIN ZOOM
        Route::get('/meetings/{meeting}/join-zoom',[MeetingController::class, 'joinZoom'])->name('meeting.joinZoom');
    });

    /*
    |--------------------------------------------------------------------------
    | MEETING ATTENDANCE
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|tentor'])->group(function () {
        Route::get('/meetings/{meeting}/attendance',[MeetingAttendanceController::class, 'index'])->name('meeting.attendance.index');
        Route::post('/meetings/{meeting}/attendance',[MeetingAttendanceController::class, 'store'])->name('meeting.attendance.store');
    });

    /*
    |--------------------------------------------------------------------------
    | MEETING MATERIAL (PDF)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|tentor'])->group(function () {
        Route::post('/meetings/{meeting}/material',[MeetingMaterialController::class, 'store'])->name('meeting.material.store');
        Route::delete('/meetings/{meeting}/material',[MeetingMaterialController::class, 'destroy'])->name('meeting.material.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | MEETING VIDEO (BUNNY STREAM)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|tentor'])->group(function () {
        Route::post('/meetings/{meeting}/video',[MeetingVideoController::class, 'store'])->name('meeting.video.store');
        Route::delete('/meetings/{meeting}/video',[MeetingVideoController::class, 'destroy'])->name('meeting.video.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | MEETING POST TEST
    |--------------------------------------------------------------------------
    */

    //ADMIN & TENTOR
    Route::middleware(['role:admin|tentor'])->group(function () {
        Route::post('/meetings/{meeting}/post-test',[MeetingPostTestController::class, 'store'])->name('posttest.store');
        Route::get('/post-tests/{postTest}/edit',[MeetingPostTestController::class, 'edit'])->name('posttest.edit');
        Route::post('/post-tests/{postTest}/duration',[MeetingPostTestController::class, 'updateDuration'])->name('posttest.duration.update');
        Route::post('/post-tests/{postTest}/questions',[MeetingPostTestController::class, 'attachQuestions'])->name('posttest.questions.attach');
        Route::post('/post-tests/{postTest}/launch',[MeetingPostTestController::class, 'launch'])->name('posttest.launch');
        Route::post('/post-tests/{postTest}/close',[MeetingPostTestController::class, 'close'])->name('posttest.close');
        Route::get('/post-tests/{postTest}/questions/by-material/{material}',[MeetingPostTestController::class, 'questionsByMaterial'])->name('posttest.questions.byMaterial');
        Route::delete('/post-tests/{postTest}/questions/{question}',[MeetingPostTestController::class, 'detachQuestion'])->name('posttest.questions.detach');
        Route::get('/post-tests/{postTest}/result-admin',[MeetingPostTestController::class, 'resultAdmin'])->name('posttest.result.admin');
    });
    /*
    | SISWA (ATTEMPT)
    */
    Route::middleware(['role:admin|siswa'])->group(function () {
        Route::post('/post-tests/{postTest}/start',[MeetingPostTestAttemptController::class, 'start'])->name('posttest.attempt.start');
        Route::get('/post-test-attempts/{attempt}',[MeetingPostTestAttemptController::class, 'show'])->name('posttest.attempt.show');
        Route::post('/post-test-attempts/{attempt}/answer',[MeetingPostTestAttemptController::class, 'saveAnswer'])->name('posttest.answer.save');
        Route::post('/post-test-attempts/{attempt}/submit',[MeetingPostTestAttemptController::class, 'submit'])->name('posttest.submit');
        Route::get('/post-test-attempts/{attempt}/result',[MeetingPostTestAttemptController::class, 'result'])->name('posttest.result');
    });


    /*
    |--------------------------------------------------------------------------
    | BUNNY WEBHOOK
    |--------------------------------------------------------------------------
    */
    Route::post('/webhooks/bunny',[BunnyWebhookController::class, 'handle'])->name('webhooks.bunny');

    /*
    |--------------------------------------------------------------------------
    | BANK SOAL (QUESTIONS) ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin|tentor'])->prefix('bank-soal')->name('bank.')->group(function () {

        // KATEGORI SOAL
        Route::prefix('categories')->name('category.')->group(function () {
            Route::get('/',[QuestionCategoryController::class, 'index'])->name('index');
            Route::get('/create',[QuestionCategoryController::class, 'create'])->name('create');
            Route::post('/store',[QuestionCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit',[QuestionCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}',[QuestionCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}',[QuestionCategoryController::class, 'destroy'])->name('delete');

            Route::get('/{category}/materials',[QuestionMaterialController::class, 'index'])->name('materials.index');
            Route::get('/{category}/materials/create',[QuestionMaterialController::class, 'create'])->name('materials.create');
            Route::post('/{category}/materials/store',[QuestionMaterialController::class, 'store'])->name('materials.store');
        });

        // MATERI SOAL UNTUK EDIT/UPDATE/DELETE
        Route::get('/materials/{material}/edit',[QuestionMaterialController::class, 'edit'])->name('material.edit');
        Route::put('/materials/{material}',[QuestionMaterialController::class, 'update'])->name('material.update');
        Route::delete('/materials/{material}',[QuestionMaterialController::class, 'destroy'])->name('material.delete');


        // SOAL SOAL
        Route::prefix('materials')->name('material.')->group(function () {
            Route::get('/{material}/questions',[QuestionController::class, 'index'])->name('questions.index');
            Route::get('/{material}/questions/create',[QuestionController::class, 'create'])->name('questions.create');
            Route::post('/{material}/questions/store',[QuestionController::class, 'store'])->name('questions.store');
        });

        Route::get('/questions/{question}/edit',[QuestionController::class, 'edit'])->name('question.edit');
        Route::put('/questions/{question}',[QuestionController::class, 'update'])->name('question.update');
        Route::delete('/questions/{question}',[QuestionController::class, 'destroy'])->name('question.delete');
    });
});


