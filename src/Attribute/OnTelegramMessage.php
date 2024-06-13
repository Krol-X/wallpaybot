<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class OnTelegramMessage
{
    public ?string $command;
    public ?string $pattern;

    public function __construct(?string $command = null, ?string $pattern = null)
    {
        $this->command = $command;
        $this->pattern = $pattern;
    }
}
