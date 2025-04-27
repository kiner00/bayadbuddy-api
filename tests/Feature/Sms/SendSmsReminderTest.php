<?php

namespace Tests\Feature\Sms;

use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendSmsReminderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_send_sms_reminder_to_borrower(): void
    {
        $user = $this->loginAsUser();

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
            'mobile_number' => '09171234567',
        ]);

        Debt::factory()->count(2)->create([
            'borrower_id' => $borrower->id,
            'status' => 'pending',
        ]);

        // Simulate sending SMS (no actual 3rd party for now)
        $response = $this->postJson("/api/debtors/{$borrower->id}/send-sms", [
            'message' => 'Please settle your balance!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'SMS reminder sent successfully.',
            ]);
    }

    #[Test]
    public function cannot_send_sms_to_borrower_of_other_user(): void
    {
        $user = $this->loginAsUser();
        $otherUser = User::factory()->create();

        $borrower = Borrower::factory()->create([
            'user_id' => $otherUser->id,
            'mobile_number' => '09181234567',
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/debtors/{$borrower->id}/send-sms", [
            'message' => 'Hello!',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticated_user_cannot_send_sms(): void
    {
        $borrower = Borrower::factory()->create([
            'mobile_number' => '09191234567',
        ]);

        $response = $this->postJson("/api/debtors/{$borrower->id}/send-sms", [
            'message' => 'Hi!',
        ]);

        $response->assertStatus(401);
    }
}
