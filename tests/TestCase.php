<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function loginAsUser()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        return $user;
    }
}
