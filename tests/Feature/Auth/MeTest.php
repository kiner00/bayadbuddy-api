<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_retrieved_me(): void
    {
        $this->loginAsUser();

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'mobile_number',
                'subscription_status',
                'sms_credits',
                'created_at',
                'updated_at',
            ]
        ]);
    }

    public function forbidden_to_retrieved_me(): void
    {
        $this->get('/api/me')
            ->assertStatus(403);
    }
}
