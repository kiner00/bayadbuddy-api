<?php

namespace App\Contracts;

use App\Models\User;

interface SmsSenderInterface
{
    public function send(User $user, string $mobileNumber, string $message): bool;
}
