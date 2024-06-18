<?php

namespace App\Interface\Message;

interface TelegramEventHandlerInterface
{
    public function __invoke(TelegramEventMessageInterface $message);

    public function defaultAction(TelegramEventMessageInterface $message): void;
}
