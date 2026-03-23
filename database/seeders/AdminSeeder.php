<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Admin::query()->updateOrCreate(
            ['email' => 'pupocms2027@gmail.com'],
            [
                'first_name' => 'PUP',
                'last_name' => 'OCMS Admin',
                'birthday' => '1990-01-01',
                'age' => 35,
                'gender' => 'Prefer not to say',
                'civil_status' => 'Single',
                'address' => 'Taguig City',
                'emergency_contact_person' => 'Emergency Contact',
                'emergency_contact_no' => '09123456789',
                'office' => 'Clinic Office',
                'access_level' => 'admin',
            ]
        );
    }
}
