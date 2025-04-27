<?php

namespace Tests\Feature\Borrower;

use App\Models\Borrower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowBorrowerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_show_borrower(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/borrowers/{$borrower->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
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
    }

    #[Test]
    public function cannot_show_borrower_of_other_user(): void
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("/api/borrowers/{$borrower->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_show_borrower(): void
    {
        $borrower = Borrower::factory()->create();

        $response = $this->getJson("/api/borrowers/{$borrower->id}");

        $response->assertStatus(401);
    }
}
