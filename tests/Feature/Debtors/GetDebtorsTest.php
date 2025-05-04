<?php

namespace Feature\Debtors;

use App\Models\Borrower;
use App\Models\Debt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetDebtorsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_get_list_of_debtors_with_unpaid_debts(): void
    {
        $user = $this->loginAsUser();

        // Borrower 1 with unpaid debts
        $borrower1 = Borrower::factory()->create(['user_id' => $user->id]);
        Debt::factory()->count(2)->create([
            'borrower_id' => $borrower1->id,
            'status' => 'pending',
        ]);

        // Borrower 2 fully paid
        $borrower2 = Borrower::factory()->create(['user_id' => $user->id]);
        Debt::factory()->create([
            'borrower_id' => $borrower2->id,
            'status' => 'paid',
        ]);

        $response = $this->getJson('/api/debtors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'borrower' => [
                            'id',
                            'name',
                            'mobile_number',
                        ],
                        'amount',
                        'due_date',
                        'status',
                        'interest_rate',
                        'notes',
                    ]
                ]
            ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_debtors(): void
    {
        $response = $this->getJson('/api/debtors');

        $response->assertStatus(401);
    }
}
