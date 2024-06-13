<?php

namespace App\Exception;

class TelegramApiException extends \Exception
{
    public function __construct(string $description, int $code = 0, \Throwable $previous = null)
    {
        $message = "Telegram API error: $description";
        parent::__construct($message, $code, $previous);
    }
}
