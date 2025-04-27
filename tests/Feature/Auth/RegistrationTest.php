<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function successfully_register_user(): void
    {
        $payload = [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->lastName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_number' => $this->faker->numerify('09#########'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'mobile_number' => $payload['mobile_number'],
        ]);
    }

    #[Test]
    #[DataProvider('invalidRegistrationData')]
    public function fails_registration_with_invalid_payload(array $invalidPayload, array $expectedErrors): void
    {
        $response = $this->postJson('/api/register', $invalidPayload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors($expectedErrors);
    }

    public static function invalidRegistrationData(): array
    {
        return [
            'missing_first_name' => [
                [
                    'middle_name' => 'Dela',
                    'last_name' => 'Cruz',
                    'email' => 'juan@example.com',
                    'mobile_number' => '09171234567',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                ['first_name'],
            ],
            'missing_email' => [
                [
                    'first_name' => 'Juan',
                    'middle_name' => 'Dela',
                    'last_name' => 'Cruz',
                    'mobile_number' => '09171234567',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                ['email'],
            ],
            'invalid_email_format' => [
                [
                    'first_name' => 'Juan',
                    'middle_name' => 'Dela',
                    'last_name' => 'Cruz',
                    'email' => 'not-an-email',
                    'mobile_number' => '09171234567',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                ['email'],
            ],
            'password_confirmation_mismatch' => [
                [
                    'first_name' => 'Juan',
                    'middle_name' => 'Dela',
                    'last_name' => 'Cruz',
                    'email' => 'juan@example.com',
                    'mobile_number' => '09171234567',
                    'password' => 'password',
                    'password_confirmation' => 'wrong_password',
                ],
                ['password'],
            ],
            'missing_mobile_number' => [
                [
                    'first_name' => 'Juan',
                    'middle_name' => 'Dela',
                    'last_name' => 'Cruz',
                    'email' => 'juan@example.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
                ['mobile_number'],
            ],
        ];
    }
}
