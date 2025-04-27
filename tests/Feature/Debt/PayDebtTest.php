<?php

namespace Tests\Feature\Debt;

use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayDebtTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_pay_debt(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
        ]);

        $debt = Debt::factory()->create([
            'borrower_id' => $borrower->id,
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/debts/{$debt->id}/pay");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $debt->id,
                    'status' => 'paid',
                ]
            ]);

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'status' => 'paid',
        ]);
    }

    #[Test]
    public function cannot_pay_debt_of_other_users_borrower(): void
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $debt = Debt::factory()->create([
            'borrower_id' => $borrower->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/debts/{$debt->id}/pay");

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_pay_debt(): void
    {
        $debt = Debt::factory()->create([
            'status' => 'pending',
        ]);

        $response = $this->postJson("/api/debts/{$debt->id}/pay");

        $response->assertStatus(401);
    }
}
