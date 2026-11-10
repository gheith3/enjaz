<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Dates as the design writes them: "٢٩ أبريل ٢٠١٧", "يناير ٢٠٢٦", "٢٠٢١" (Arabic digits, Gulf month names).
 */
class ArabicDate
{
    private const MONTHS = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

    public static function format(?CarbonInterface $date, string $precision = 'day'): string
    {
        if (! $date) {
            return '';
        }
        if (app()->getLocale() !== 'ar') {
            return match ($precision) {
                'year' => $date->format('Y'),
                'month' => $date->format('F Y'),
                'daymonth' => $date->format('j F'),
                default => $date->format('j F Y'),
            };
        }

        $month = self::MONTHS[$date->month - 1];

        return self::digits(match ($precision) {
            'year' => (string) $date->year,
            'month' => "$month {$date->year}",
            'daymonth' => "{$date->day} $month",
            default => "{$date->day} $month {$date->year}",
        });
    }

    public static function digits(string|int $value): string
    {
        return strtr((string) $value, ['0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤', '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩']);
    }
}
