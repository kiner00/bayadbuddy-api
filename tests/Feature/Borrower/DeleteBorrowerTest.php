<?php

namespace Tests\Feature\Borrower;

use App\Models\Borrower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteBorrowerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_delete_borrower(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/borrowers/{$borrower->id}");

        $response->assertStatus(204); // No Content

        $this->assertDatabaseMissing('borrowers', [
            'id' => $borrower->id,
        ]);
    }

    #[Test]
    public function cannot_delete_borrower_of_other_user(): void
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson("/api/borrowers/{$borrower->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_delete_borrower(): void
    {
        $borrower = Borrower::factory()->create();

        $response = $this->deleteJson("/api/borrowers/{$borrower->id}");

        $response->assertStatus(401);
    }
}
