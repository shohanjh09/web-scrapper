<?php

namespace App\Message;

class ScrapeMessage
{
    private $registrationCode;

    public function __construct(string $registrationCode)
    {
        $this->registrationCode = $registrationCode;
    }

    public function getRegistrationCode(): string
    {
        return $this->registrationCode;
    }
}

