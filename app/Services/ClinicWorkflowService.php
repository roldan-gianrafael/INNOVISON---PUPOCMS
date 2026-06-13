<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class ClinicWorkflowService
{
    private ?Setting $settings = null;

    public function settings(): Setting
    {
        if ($this->settings) {
            return $this->settings;
        }

        $this->settings = Setting::query()->firstOrNew([], [
            'clinic_name' => 'PUP Taguig Clinic',
            'clinic_location' => 'Santos Ave, Lower Bicutan, Taguig',
            'open_time' => '08:00',
            'close_time' => '17:00',
            'student_assistant_open_time' => '08:00',
            'student_assistant_close_time' => '20:00',
            'appointment_reminder_hours' => 24,
        ]);

        return $this->settings;
    }

    public function studentAssistantWorkspaceAvailable(?Carbon $at = null): bool
    {
        $settings = $this->settings();
        $at = ($at ?: now(config('app.timezone')))->copy();
        $currentMinutes = ((int) $at->format('H') * 60) + (int) $at->format('i');
        $openMinutes = $this->timeToMinutes($settings->student_assistant_open_time ?: '08:00');
        $closeMinutes = $this->timeToMinutes($settings->student_assistant_close_time ?: '20:00');

        if ($openMinutes === $closeMinutes) {
            return true;
        }

        if ($openMinutes < $closeMinutes) {
            return $currentMinutes >= $openMinutes && $currentMinutes < $closeMinutes;
        }

        return $currentMinutes >= $openMinutes || $currentMinutes < $closeMinutes;
    }

    public function studentAssistantHoursLabel(): string
    {
        $settings = $this->settings();

        return $this->formatTime($settings->student_assistant_open_time ?: '08:00')
            . '–'
            . $this->formatTime($settings->student_assistant_close_time ?: '20:00');
    }

    public function activeClosure(?Carbon $at = null): ?array
    {
        $settings = $this->settings();
        if (!$settings->clinic_closure_enabled) {
            return null;
        }

        $at = ($at ?: now(config('app.timezone')))->copy();
        $startsAt = $settings->clinic_closure_starts_at
            ? Carbon::parse($settings->clinic_closure_starts_at, config('app.timezone'))
            : $at->copy()->startOfDay();
        $endsAt = $settings->clinic_closure_ends_at
            ? Carbon::parse($settings->clinic_closure_ends_at, config('app.timezone'))
            : null;

        if ($at->lt($startsAt) || ($endsAt && $at->gte($endsAt))) {
            return null;
        }

        $reason = trim((string) ($settings->clinic_closure_reason ?: 'Temporary Clinic Closure'));
        $message = trim((string) ($settings->clinic_closure_message ?: 'The clinic is temporarily unavailable for new appointment bookings.'));

        return [
            'reason' => $reason,
            'message' => $message,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'updated_at' => $settings->updated_at,
        ];
    }

    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = array_pad(explode(':', $time), 2, 0);

        return ((int) $hours * 60) + (int) $minutes;
    }

    private function formatTime(string $time): string
    {
        return Carbon::createFromFormat('H:i', substr($time, 0, 5))->format('g:i A');
    }
}
