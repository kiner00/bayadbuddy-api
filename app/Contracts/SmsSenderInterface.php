<?php

namespace App\Contracts;

interface SmsSenderInterface
{
    public function send(string $mobileNumber, string $message): bool;
}
