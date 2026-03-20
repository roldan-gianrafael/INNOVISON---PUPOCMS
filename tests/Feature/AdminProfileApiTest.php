<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminProfileApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::dropIfExists('admins');
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email_address')->nullable();
            $table->string('offices')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('emergency_contact_person')->nullable();
            $table->string('emergency_contact_no')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->date('birthday')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('role')->nullable();
            $table->boolean('is_active')->default(true);
        });

        Config::set('services.external_admin_profile.api_key', 'shared-secret');
        Config::set('services.external_admin_profile.header', 'X-External-Api-Key');
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('admins');
        parent::tearDown();
    }

    public function test_it_requires_the_external_api_key(): void
    {
        $response = $this->getJson('/api/external/admin-profile?email_address=admin@example.com');

        $response->assertUnauthorized();
    }

    public function test_it_returns_the_admin_profile_by_email_address(): void
    {
        DB::table('admins')->insert([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'name' => 'Ada Lovelace',
            'email_address' => 'admin@example.com',
            'offices' => 'Clinic Office',
            'address' => 'Taguig City',
            'contact_no' => '09171234567',
            'emergency_contact_person' => 'Charles Babbage',
            'emergency_contact_no' => '09998887777',
            'age' => 32,
            'gender' => 'Female',
            'birthday' => '1994-12-10',
            'civil_status' => 'Single',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this
            ->withHeader('X-External-Api-Key', 'shared-secret')
            ->getJson('/api/external/admin-profile?email_address=admin@example.com');

        $response
            ->assertOk()
            ->assertJson([
                'data' => [
                    'email_address' => 'admin@example.com',
                    'first_name' => 'Ada',
                    'last_name' => 'Lovelace',
                    'name' => 'Ada Lovelace',
                    'offices' => 'Clinic Office',
                    'address' => 'Taguig City',
                    'contact_no' => '09171234567',
                    'emergency_contact_person' => 'Charles Babbage',
                    'emergency_contact_no' => '09998887777',
                    'age' => 32,
                    'gender' => 'Female',
                    'birthday' => '1994-12-10',
                    'civil_status' => 'Single',
                    'role' => 'superadmin',
                    'is_active' => true,
                ],
            ]);
    }
}
