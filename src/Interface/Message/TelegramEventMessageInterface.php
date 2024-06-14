<?php

namespace App\Interface\Message;

use App\Interface\Model\TelegramResponseInterface;
use Symfony\Component\Messenger\MessageBusInterface;

interface TelegramEventMessageInterface
{
    public function getContent(): array;

    public function getData(): array;

    public function getChatId(): int;

    public function getMessageId(): int;

    public function getUpdateId(): int;

    public function getText(): string;

    public function isQuery(): bool;

    public function send(MessageBusInterface $bus): void;

    public function newResponse(string $text): TelegramResponseInterface;
}
