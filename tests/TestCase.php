<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    public function seedRoles()
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'lender']);
    }

    public function loginAsUser()
    {
        $this->seedRoles();
        $user = User::factory()->create();
        $user->assignRole('lender');
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function loginAsAdmin()
    {
        $this->seedRoles();
        $user = User::factory()->create();
        $user->assignRole('admin');
        $this->actingAs($user, 'sanctum');

        return $user;
    }
}
