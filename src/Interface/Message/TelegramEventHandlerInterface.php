<?php

namespace App\Interface\Message;

use App\Interface\Service\TelegramServiceInterface;

interface TelegramEventHandlerInterface
{
    public function __invoke(TelegramEventMessageInterface $message);

    public function defaultAction(TelegramEventMessageInterface $message): void;
}
