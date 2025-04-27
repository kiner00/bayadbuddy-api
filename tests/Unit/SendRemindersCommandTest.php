<?php

namespace Tests\Unit;

use App\Contracts\SmsSenderInterface;
use App\Models\Borrower;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendRemindersCommandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Bind a fake SmsSenderInterface during this test
        $this->mockSmsSender = Mockery::mock(SmsSenderInterface::class);
        $this->app->instance(SmsSenderInterface::class, $this->mockSmsSender);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_sends_sms_reminders_to_borrowers_with_unpaid_debts()
    {
        // Arrange: Create User
        $user = User::factory()->create([
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
        ]);

        // Create Borrower linked to User
        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
            'name' => 'Maria',
            'mobile_number' => '09171234567',
        ]);

        Debt::factory()->create([
            'borrower_id' => $borrower->id,
            'status' => 'pending',
            'due_date' => now()->toDateString(), // due today
        ]);

        // Expectation: smsSender->send() will be called once
        $this->mockSmsSender
            ->shouldReceive('send')
            ->withArgs(function ($mobileNumber, $message) use ($borrower) {
                return $mobileNumber === $borrower->mobile_number && is_string($message);
            })
            ->once()
            ->andReturn(true);

        // Act: Run the command
        Artisan::call('send:reminders');

        // Assert: Artisan command output
        $this->assertStringContainsString('Reminder jobs have been queued.', Artisan::output());
    }
}
