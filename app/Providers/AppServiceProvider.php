<?php

namespace App\Providers;

use App\Http\Controllers\AppointmentController;
use App\Services\ClinicWorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.student', function ($view) {
            $workflow = app(ClinicWorkflowService::class);
            $view->with('clinicClosure', $workflow->activeClosure());
            $view->with('studentAssistantAdminAvailable', $workflow->studentAssistantWorkspaceAvailable());
            $view->with('studentAssistantHoursLabel', $workflow->studentAssistantHoursLabel());

            $existingNotifications = $view->getData()['notifications'] ?? null;
            if ($existingNotifications !== null) {
                return;
            }

            $user = Auth::guard('student')->user();
            if (!$user) {
                $view->with('notifications', collect());
                return;
            }

            $controller = app(AppointmentController::class);
            $view->with('notifications', collect($controller->getStudentNotifications($user)));
        });
    }
}
