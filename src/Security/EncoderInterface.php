<?php

declare(strict_types=1);

namespace Security;

interface EncoderInterface
{
    public function encode(string $plain): string;
}
