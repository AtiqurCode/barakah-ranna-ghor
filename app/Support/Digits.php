<?php

namespace App\Support;

class Digits
{
    /**
     * Bengali (Bangla) numeral glyphs indexed 0-9.
     *
     * @var array<int, string>
     */
    private const BENGALI = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    /**
     * Convert the ASCII digits in a value to the active locale's numerals.
     */
    public static function localize(int|string $value): string
    {
        $value = (string) $value;

        if (app()->getLocale() !== 'bn') {
            return $value;
        }

        return preg_replace_callback('/\d/', fn (array $m): string => self::BENGALI[(int) $m[0]], $value);
    }
}
