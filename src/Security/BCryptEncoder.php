<?php

declare(strict_types=1);

namespace Security;

use function password_hash;

use const PASSWORD_BCRYPT;

class BCryptEncoder implements EncoderInterface
{
    public function encode(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT);
    }
}
