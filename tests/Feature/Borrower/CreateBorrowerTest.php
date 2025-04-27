<?php

namespace Tests\Feature\Borrower;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateBorrowerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function successfully_create_borrower(): void
    {
        $this->loginAsUser();

        $payload = [
            'name' => 'Pedro Penduko',
            'mobile_number' => '09171234567',
            'notes' => 'Kaibigan',
        ];

        $response = $this->postJson('/api/borrowers', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'name',
                    'mobile_number',
                    'notes',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('borrowers', [
            'name' => $payload['name'],
            'mobile_number' => $payload['mobile_number'],
        ]);
    }

    #[Test]
    public function fails_create_borrower_with_invalid_data(): void
    {
        $this->loginAsUser();

        $response = $this->postJson('/api/borrowers', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'mobile_number']);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_borrower(): void
    {
        $response = $this->postJson('/api/borrowers', [
            'name' => 'Test',
            'mobile_number' => '09171234567',
        ]);

        $response->assertStatus(401);
    }
}
