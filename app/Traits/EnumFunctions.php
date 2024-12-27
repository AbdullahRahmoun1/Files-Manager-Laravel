<?php

namespace App\Traits;
trait EnumFunctions
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
