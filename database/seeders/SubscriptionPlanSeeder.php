<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => 0,
                'sms_limit' => 0,
                'borrower_limit' => 5,
            ],
            [
                'name' => 'Solo',
                'price' => 14900, // ₱149
                'sms_limit' => 30,
                'borrower_limit' => 30,
            ],
            [
                'name' => 'Business',
                'price' => 49900, // ₱499
                'sms_limit' => 200,
                'borrower_limit' => null, // unlimited
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::firstOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
