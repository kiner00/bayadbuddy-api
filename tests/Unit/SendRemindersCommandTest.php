<?php

namespace Tests\Unit;

use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendRemindersCommandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_sends_sms_reminders_to_borrowers_with_unpaid_debts()
    {
        // Spy the Log
        Log::spy();

        // Arrange: Create User
        $user = User::factory()->create([
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
        ]);

        // Create Borrower linked to User
        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
            'mobile_number' => '09171234567',
        ]);

        Debt::factory()->create([
            'borrower_id' => $borrower->id,
            'status' => 'pending',
            'due_date' => now()->toDateString(), // due today
        ]);

        // Act: Run the command
        Artisan::call('send:reminders');

        // Build full names (same as production code)
        $borrowerFullName = trim("{$borrower->name}");
        $lenderFullName = trim("{$user->first_name} {$user->last_name}");

        // Assert: Check if log was created
        Log::shouldHaveReceived('info')
            ->withArgs(function ($message) use ($borrower, $borrowerFullName, $lenderFullName) {
                return str_contains($message, $borrower->mobile_number)
                    && str_contains($message, $borrowerFullName)
                    && str_contains($message, $lenderFullName);
            })
            ->once();

        // Assert: Artisan command output
        $this->assertStringContainsString('Default reminders sent successfully.', Artisan::output());
    }
}
