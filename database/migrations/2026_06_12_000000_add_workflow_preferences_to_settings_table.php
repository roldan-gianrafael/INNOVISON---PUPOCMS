<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkflowPreferencesToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('admin_live_notifications')->default(true)->after('email_notifications');
            $table->time('student_assistant_open_time')->default('08:00')->after('auto_approve');
            $table->time('student_assistant_close_time')->default('20:00')->after('student_assistant_open_time');
            $table->unsignedSmallInteger('appointment_reminder_hours')->default(24)->after('student_assistant_close_time');
            $table->boolean('clinic_closure_enabled')->default(false)->after('appointment_reminder_hours');
            $table->dateTime('clinic_closure_starts_at')->nullable()->after('clinic_closure_enabled');
            $table->dateTime('clinic_closure_ends_at')->nullable()->after('clinic_closure_starts_at');
            $table->string('clinic_closure_reason')->nullable()->after('clinic_closure_ends_at');
            $table->text('clinic_closure_message')->nullable()->after('clinic_closure_reason');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'admin_live_notifications',
                'student_assistant_open_time',
                'student_assistant_close_time',
                'appointment_reminder_hours',
                'clinic_closure_enabled',
                'clinic_closure_starts_at',
                'clinic_closure_ends_at',
                'clinic_closure_reason',
                'clinic_closure_message',
            ]);
        });
    }
}
