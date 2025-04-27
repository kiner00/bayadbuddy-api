<?php

namespace Tests\Feature\Borrower;

use App\Models\Borrower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateBorrowerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_update_borrower(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
        ]);

        $payload = [
            'name' => 'Updated Name',
            'mobile_number' => '09998887777',
            'notes' => 'Updated Notes',
        ];

        $response = $this->putJson("/api/borrowers/{$borrower->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $borrower->id,
                    'name' => $payload['name'],
                    'mobile_number' => $payload['mobile_number'],
                    'notes' => $payload['notes'],
                ]
            ]);

        $this->assertDatabaseHas('borrowers', [
            'id' => $borrower->id,
            'name' => $payload['name'],
            'mobile_number' => $payload['mobile_number'],
            'notes' => $payload['notes'],
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_update_borrower(): void
    {
        $borrower = Borrower::factory()->create();

        $response = $this->putJson("/api/borrowers/{$borrower->id}", [
            'name' => 'Updated Name',
            'mobile_number' => '09998887777',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function cannot_update_another_users_borrower(): void
    {
        $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->putJson("/api/borrowers/{$borrower->id}", [
            'name' => 'Hacker Name',
            'mobile_number' => '09171234567',
        ]);

        $response->assertStatus(403);
    }

}
