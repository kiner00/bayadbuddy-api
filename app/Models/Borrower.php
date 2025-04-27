<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'mobile_number',
        'notes',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
