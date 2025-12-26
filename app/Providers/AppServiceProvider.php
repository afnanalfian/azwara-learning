<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

use App\Observers\OrderObserver;
use App\Observers\ProductAutoCreateObserver;
use App\Models\Order;
use Carbon\Carbon;

// MODELS
use App\Models\Course;
use App\Models\Meeting;
use App\Models\Exam;

// POLICIES
use App\Policies\CoursePolicy;
use App\Policies\MeetingPolicy;
use App\Policies\ExamPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useTailwind();
        Carbon::setLocale('id');

        // ===============================
        // OBSERVER
        // ===============================
        Order::observe(OrderObserver::class);
        Meeting::observe(ProductAutoCreateObserver::class);
        Course::observe(ProductAutoCreateObserver::class);
        Exam::observe(ProductAutoCreateObserver::class);

        // ===============================
        // POLICY REGISTRATION (Laravel 11)
        // ===============================
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Meeting::class, MeetingPolicy::class);
        Gate::policy(Exam::class, ExamPolicy::class);
    }
}
