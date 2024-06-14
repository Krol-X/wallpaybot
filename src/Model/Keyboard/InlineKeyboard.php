<?php

namespace App\Model\Keyboard;

class InlineKeyboard
{
    private array $keyboard = [];

    public function addButton(string $text, string $callbackData): self
    {
        $button = [
            'text' => $text,
            'callback_data' => $callbackData
        ];
        $this->keyboard[] = [$button];
        return $this;
    }

    public function addRow(array $buttons): self
    {
        $this->keyboard[] = $buttons;
        return $this;
    }

    public function toArray(): array
    {
        return ['inline_keyboard' => $this->keyboard];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
