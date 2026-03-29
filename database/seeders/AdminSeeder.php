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

        $records = [
            [
                'admin_id' => 1,
                'first_name' => 'PUP',
                'last_name' => 'OCMS Admin',
                'email' => 'pupocms2027@gmail.com',
                'birthday' => '1990-01-01',
                'age' => 35,
                'gender' => 'Prefer not to say',
                'civil_status' => 'Single',
                'address' => 'Taguig City',
                'emergency_contact_person' => 'Emergency Contact',
                'emergency_contact_no' => '09123456789',
                'office' => 'Clinic Office',
                'access_level' => 'admin',
            ],
            [
                'admin_id' => 2,
                'first_name' => 'Alden',
                'last_name' => 'Richard',
                'email' => 'aldenrichard@gmail.com',
                'birthday' => '2000-01-01',
                'age' => 26,
                'gender' => 'Male',
                'civil_status' => 'Single',
                'address' => 'Manila',
                'emergency_contact_person' => 'Maine Mendoza',
                'emergency_contact_no' => '09123456789',
                'office' => 'Registrar',
                'access_level' => 'admin',
            ],
            [
                'admin_id' => 3,
                'first_name' => 'Joyce',
                'middle_name' => null,
                'last_name' => 'Lim',
                'suffix_name' => null,
                'email' => 'nursejoyce@gmail.com',
                'office' => 'Clinic Office',
            ],
        ];

        foreach ($records as $record) {
            Admin::query()->updateOrCreate(
                ['admin_id' => $record['admin_id']],
                $record
            );
        }
    }
}
