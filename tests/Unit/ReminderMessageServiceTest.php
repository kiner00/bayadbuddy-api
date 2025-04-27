<?php

namespace Tests\Unit;

use App\Models\Borrower;
use App\Models\User;
use App\Services\ReminderMessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReminderMessageServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_generates_correct_message_with_middle_name()
    {
        // Arrange
        $user = User::factory()->create([
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
        ]);

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
            'name' => 'Maria',
        ]);

        $service = new ReminderMessageService();

        // Act
        $message = $service->buildReminderMessage($borrower);

        // Assert
        $this->assertStringContainsString('Good day, Maria.', $message);
        $this->assertStringContainsString('outstanding balance with Juan Dela Cruz.', $message);
    }

    #[Test]
    public function it_generates_correct_message_without_middle_name()
    {
        // Arrange
        $user = User::factory()->create([
            'first_name' => 'Juan',
            'middle_name' => null,
            'last_name' => 'Dela Cruz',
        ]);

        $borrower = Borrower::factory()->create([
            'user_id' => $user->id,
            'name' => 'Maria',
        ]);

        $service = new ReminderMessageService();

        // Act
        $message = $service->buildReminderMessage($borrower);

        // Assert
        $this->assertStringContainsString('Good day, Maria.', $message);
        $this->assertStringContainsString('outstanding balance with Juan Dela Cruz.', $message);
    }
}
