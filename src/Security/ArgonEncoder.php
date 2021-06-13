<?php

declare(strict_types=1);

namespace Security;

use function password_hash;

use const PASSWORD_ARGON2I;

class ArgonEncoder implements EncoderInterface
{
    public function encode(string $plain): string
    {
        return password_hash($plain, PASSWORD_ARGON2I);
    }
}
