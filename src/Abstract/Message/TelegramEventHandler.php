<?php

namespace App\Abstract\Message;

use App\Attribute\OnTelegramMessage;
use App\Attribute\OnTelegramQuery;
use App\Interface\Message\TelegramEventHandlerInterface;
use App\Interface\Message\TelegramEventMessageInterface;
use ReflectionClass;

// Наследники используют #[AsMessageHandler]
abstract class TelegramEventHandler implements TelegramEventHandlerInterface
{


    public function __construct(
        private readonly bool $useRedis
    )
    {
    }

    public function __invoke(TelegramEventMessageInterface $message)
    {
        if (!$this->useRedis) {
            $message->delay();
        }
        $text = $message->getText();
        $isQuery = $message->isQuery();

        $reflection = new ReflectionClass($this);
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $attrInstance = $attribute->newInstance();

                if ($isQuery) {
                    if ($this->checkAttribute($attrInstance, OnTelegramQuery::class, $text)) {
                        if ($this->{$method->getName()}($message))
                            return;
                    }
                } else {
                    if ($this->checkAttribute($attrInstance, OnTelegramMessage::class, $text)) {
                        if ($this->{$method->getName()}($message))
                            return;
                    }
                }
            }
        }

        $this->defaultAction($message);
    }

    private function checkAttribute($attribute, $need_attr, string $text): bool
    {
        if ($attribute instanceof $need_attr) {
            if ($attribute->command && $attribute->command === $text) {
                return true;
            }
            if ($attribute->pattern && preg_match($attribute->pattern, $text)) {
                return true;
            }
        }
        return false;
    }

    function defaultAction(TelegramEventMessageInterface $message): void
    {
    }
}
