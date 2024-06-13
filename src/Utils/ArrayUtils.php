<?php

namespace App\Utils;

class ArrayUtils
{
    public static function isAssociative(array $data): bool
    {
        reset($data);
        return is_string(key($data));
    }
}