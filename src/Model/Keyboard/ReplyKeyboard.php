<?php

namespace App\Model\Keyboard;

class ReplyKeyboard
{
    private array $keyboard = [];
    private bool $resizeKeyboard = true;
    private bool $oneTimeKeyboard = false;
    private bool $selective = false;

    public function addButton(string $text): self
    {
        $button = ['text' => $text];
        $this->keyboard[] = [$button];
        return $this;
    }

    public function addRow(array $buttons): self
    {
        $this->keyboard[] = $buttons;
        return $this;
    }

    public function setResizeKeyboard(bool $resizeKeyboard): self
    {
        $this->resizeKeyboard = $resizeKeyboard;
        return $this;
    }

    public function setOneTimeKeyboard(bool $oneTimeKeyboard): self
    {
        $this->oneTimeKeyboard = $oneTimeKeyboard;
        return $this;
    }

    public function setSelective(bool $selective): self
    {
        $this->selective = $selective;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'keyboard' => $this->keyboard,
            'resize_keyboard' => $this->resizeKeyboard,
            'one_time_keyboard' => $this->oneTimeKeyboard,
            'selective' => $this->selective
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
