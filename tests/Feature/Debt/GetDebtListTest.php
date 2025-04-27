<?php

namespace Tests\Feature\Debt;

use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetDebtListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_get_list_of_debts_for_borrower(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
        ]);

        Debt::factory()->count(3)->create([
            'borrower_id' => $borrower->id,
        ]);

        $response = $this->getJson("/api/borrowers/{$borrower->id}/debts");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [ // each debt
                    'id',
                    'borrower_id',
                    'amount',
                    'due_date',
                    'status',
                    'interest_type',
                    'interest_value',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    }

    #[Test]
    public function cannot_get_debts_for_other_users_borrower(): void
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("/api/borrowers/{$borrower->id}/debts");

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_get_debts(): void
    {
        $borrower = Borrower::factory()->create();

        $response = $this->getJson("/api/borrowers/{$borrower->id}/debts");

        $response->assertStatus(401);
    }
}
