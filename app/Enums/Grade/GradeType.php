<?php

namespace App\Enums\Grade;

enum GradeType: string
{
    case ORDINARY = 'ordinary';
    case EXTRAORDINARY = 'extraordinary';
    case WORK = 'work';
    case PARTIAL = 'partial';
    case FINAL = 'final';

    /**
     * Get all the values of the enum.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
