<?php

namespace Tests\Feature\Dashboard;

use App\Models\Borrower;
use App\Models\Debt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_get_dashboard_summary(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create(['user_id' => $user->id]);

        Debt::factory()->count(3)->create([
            'borrower_id' => $borrower->id,
            'status' => 'pending',
            'amount' => 1000,
        ]);

        Debt::factory()->count(2)->create([
            'borrower_id' => $borrower->id,
            'status' => 'paid',
            'amount' => 1500,
        ]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'unpaid_count',
                'paid_count',
                'total_unpaid_amount',
            ])
            ->assertJson([
                'unpaid_count' => 3,
                'paid_count' => 2,
                'total_unpaid_amount' => 3000.00,
            ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(401);
    }
}
