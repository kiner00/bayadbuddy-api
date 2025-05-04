<?php

namespace App\Services;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class PhoneNumberFormatter
{
    protected PhoneNumberUtil $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Normalize any Philippine phone number to 639XXXXXXXXX format
     */
    public function normalizeTo639(string $rawPhone): ?string
    {
        try {
            $numberProto = $this->phoneUtil->parse($rawPhone, 'PH');
            $e164 = $this->phoneUtil->format($numberProto, PhoneNumberFormat::E164); // +639...
            return ltrim($e164, '+');
        } catch (NumberParseException $e) {
            return null;
        }
    }
}
