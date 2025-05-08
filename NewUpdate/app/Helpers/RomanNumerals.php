<?php

namespace App\Helpers;

class RomanNumerals
{
    public static function toRoman(int $number): string
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1
        ];
        
        $result = '';
        foreach ($map as $roman => $arabic) {
            while ($number >= $arabic) {
                $result .= $roman;
                $number -= $arabic;
            }
        }
        return $result;
    }

    public static function toNumber(string $roman): int
    {
        $map = [
            'M' => 1000, 'D' => 500, 'C' => 100, 'L' => 50,
            'X' => 10, 'V' => 5, 'I' => 1
        ];
        
        $result = 0;
        for ($i = 0; $i < strlen($roman); $i++) {
            $current = $map[$roman[$i]];
            $next = $map[$roman[$i + 1]] ?? 0;
            $result += ($current < $next) ? -$current : $current;
        }
        return $result;
    }
} 