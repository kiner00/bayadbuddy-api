<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function successfully_login_user(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    #[DataProvider('invalidLoginData')]
    public function fails_login_with_invalid_payload(array $payload, array $expectedErrors): void
    {
        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(422); // Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors($expectedErrors);
    }

    public static function invalidLoginData(): array
    {
        return [
            'missing_email' => [
                [
                    'password' => 'password',
                ],
                ['email'],
            ],
            'missing_password' => [
                [
                    'email' => 'user@example.com',
                ],
                ['password'],
            ],
            'invalid_email_format' => [
                [
                    'email' => 'invalid-email',
                    'password' => 'password',
                ],
                ['email'],
            ],
            'empty_fields' => [
                [
                    'email' => '',
                    'password' => '',
                ],
                ['email', 'password'],
            ],
        ];
    }
}
