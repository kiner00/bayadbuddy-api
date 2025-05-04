<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function maxBorrowers(): ?int
    {
        return optional($this->subscriptionPlan)->borrower_limit;
    }

    public function canAddBorrower(): bool
    {
        $limit = $this->maxBorrowers();
        if (is_null($limit)) return true;
        return $this->borrowers()->count() < $limit;
    }

    public function smsLimit(): int
    {
        return $this->subscriptionPlan->sms_limit ?? 0;
    }

    public function getMonthlySmsUsage(): int
    {
        return $this->smsUsages()
            ->where('month', UserSmsUsage::currentMonthKey())
            ->value('count') ?? 0;
    }

    public function canSendSms(int $messagesToSend = 1): bool
    {
        return ($this->getMonthlySmsUsage() + $messagesToSend) <= $this->smsLimit();
    }

    public function smsUsages()
    {
        return $this->hasMany(UserSmsUsage::class);
    }

    public function incrementSmsUsage(int $count = 1): void
    {
        $this->smsUsages()->updateOrCreate(
            ['month' => UserSmsUsage::currentMonthKey()],
            ['count' => \DB::raw("count + {$count}")]
        );
    }

}
