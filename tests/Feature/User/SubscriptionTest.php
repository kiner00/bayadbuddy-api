<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_create_checkout_session()
    {
        // Arrange
        Role::firstOrCreate(['name' => 'lender']);
        $user = $this->loginAsUser();

        Http::fake([
            '*' => Http::response([
                'checkout_url' => 'https://payment-gateway.com/checkout/12345'
            ], 201)
        ]);

        // Act
        $response = $this->actingAs($user)
            ->postJson('/api/user/subscribe');

        // Assert
        $response->assertCreated()
            ->assertJsonStructure([
                'checkout_url',
            ]);

        $this->assertStringContainsString('https://payment-gateway.com/checkout/', $response->json('checkout_url'));
    }

    #[Test]
    public function unauthenticated_user_cannot_create_checkout_session()
    {
        $response = $this->postJson('/api/user/subscribe');

        $response->assertUnauthorized();
    }

    #[Test]
    public function payment_webhook_activates_subscription()
    {
        // Arrange
        Role::firstOrCreate(['name' => 'lender']);
        $user = User::factory()->create([
            'subscription_status' => 'inactive', // ✅ Ensure default state
        ]);
        $user->assignRole('lender');
        $this->actingAs($user, 'sanctum');

        $this->assertEquals('inactive', $user->subscription_status);

        $payload = [
            'payment_reference' => 'fake_payment_12345',
            'amount' => 99,
            'email' => $user->email,
            'status' => 'paid',
        ];

        // Act
        $response = $this->postJson('/api/payment/webhook', $payload);

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Subscription activated successfully.',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'subscription_status' => 'active',
        ]);

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser->subscription_expires_at);
    }
}
