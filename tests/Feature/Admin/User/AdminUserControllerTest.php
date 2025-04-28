<?php

namespace Tests\Feature\Admin\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_list_lenders()
    {
        // Arrange
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'lender']);

        $admin = $this->loginAsAdmin();

        $lenders = User::factory()->count(5)->create();
        foreach ($lenders as $lender) {
            $lender->assignRole('lender');
        }

        // Act
        $response = $this->actingAs($admin)
            ->getJson('/api/admin/users');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'mobile_number',
                        'created_at',
                    ]
                ],
                'links',
                'meta',
            ]);

        // Confirm we got 5 lenders
        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function non_admin_cannot_list_lenders()
    {
        // Arrange
        $user = $this->loginAsUser(); // a lender user, not admin

        // Act
        $response = $this->actingAs($user)
            ->getJson('/api/admin/users');

        // Assert
        $response->assertForbidden();
    }
}
