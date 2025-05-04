<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSmsUsage extends Model
{
    /** @use HasFactory<\Database\Factories\UserSmsUsageFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'month', 'count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function currentMonthKey(): int
    {
        return now()->format('Ym'); // e.g. 202405
    }
}
