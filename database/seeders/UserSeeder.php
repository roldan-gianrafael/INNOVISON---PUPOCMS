<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $payload = [
            'student_id' => 'superadmin-001',
            'first_name' => 'PUP',
            'last_name' => 'OCMS Admin',
            'name' => 'PUP OCMS Admin',
            'email' => 'pupocms2027@gmail.com',
            'user_role' => User::ROLE_SUPERADMIN,
            'status' => 'active',
            'password' => Hash::make('Innovision2027'),
        ];

        if (Schema::hasColumn('users', 'user_type')) {
            $payload['user_type'] = 'Assistant';
        }

        User::query()->updateOrCreate(
            ['email' => $payload['email']],
            $payload
        );
    }
}
