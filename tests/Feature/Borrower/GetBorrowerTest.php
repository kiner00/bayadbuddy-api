<?php

namespace Tests\Feature\Borrower;

use App\Models\Borrower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetBorrowerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function successfully_get_borrowers_list(): void
    {
        $user = $this->loginAsUser();

        Borrower::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/borrowers');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'name',
                    'mobile_number',
                    'notes',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_get_borrowers(): void
    {
        $response = $this->getJson('/api/borrowers');

        $response->assertStatus(401);
    }
}
