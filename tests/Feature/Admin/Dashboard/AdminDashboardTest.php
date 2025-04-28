<?php

namespace Tests\Feature\Admin\Dashboard;

use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    //#[Test]
    public function admin_can_view_dashboard_summary()
    {
        Role::firstOrCreate(['name' => 'lender']);
        $admin = $this->loginAsAdmin();

        $lenders = User::factory()->count(5)->create();
        foreach ($lenders as $lender) {
            $lender->assignRole('lender');
        }

        $borrowerLender = $lenders->first(); // Pick one lender

        // ✅ Always provide user_id
        Borrower::factory()->count(10)->create([
            'user_id' => $borrowerLender->id,
        ]);

        Debt::factory()->count(20)->create([
            'status' => 'pending'
        ]);

        Debt::factory()->count(10)->create([
            'status' => 'paid'
        ]);

        $response = $this->actingAs($admin)
            ->getJson('/api/admin/dashboard');

        $response->assertOk()
            ->assertJsonStructure([
                'total_users',
                'total_borrowers',
                'total_debts',
                'total_unpaid_debts',
            ])
            ->assertJson([
                'total_users' => 5,
                'total_borrowers' => 10,
                'total_debts' => 30,
                'total_unpaid_debts' => 20,
            ]);
    }
}
