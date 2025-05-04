<?php

namespace Tests\Feature\Debt;

use App\Models\Borrower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateDebtTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_create_debt_for_borrower(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
        ]);

        $payload = [
            'amount' => 1500,
            'due_date' => now()->addDays(30)->toDateString(),
            'interest_type' => 'none', // still passed, even if not shown in response
            'interest_value' => null,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/borrowers/{$borrower->id}/debts", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'borrower' => [
                        'id',
                        'name',
                        'mobile_number',
                        'avatar',
                    ],
                    'amount',
                    'due_date',
                    'created_at',
                    'status',
                    'interest_rate',
                    'interest_term',
                    'notes',
                    'payments',
                ]
            ]);

        $this->assertDatabaseHas('debts', [
            'borrower_id' => $borrower->id,
            'amount' => $payload['amount'],
            'due_date' => $payload['due_date'],
        ]);
    }

    #[Test]
    public function cannot_create_debt_for_other_users_borrower(): void
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $payload = [
            'amount' => 1000,
            'due_date' => now()->addDays(30)->toDateString(),
        ];

        $response = $this->postJson("/api/borrowers/{$borrower->id}/debts", $payload);

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_create_debt(): void
    {
        $borrower = Borrower::factory()->create();

        $payload = [
            'amount' => 1000,
            'due_date' => now()->addDays(30)->toDateString(),
        ];

        $response = $this->postJson("/api/borrowers/{$borrower->id}/debts", $payload);

        $response->assertStatus(401);
    }
}
