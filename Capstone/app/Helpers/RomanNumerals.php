<?php

namespace App\Helpers;

class RomanNumerals
{
    /**
     * Convert an integer to a Roman numeral.
     *
     * @param int $number
     * @return string
     */
    public static function toRoman($number)
    {
        $map = [
            1000 => 'M',
            900 => 'CM',
            500 => 'D',
            400 => 'CD',
            100 => 'C',
            90 => 'XC',
            50 => 'L',
            40 => 'XL',
            10 => 'X',
            9 => 'IX',
            5 => 'V',
            4 => 'IV',
            1 => 'I'
        ];

        $result = '';

        foreach ($map as $key => $value) {
            while ($number >= $key) {
                $result .= $value;
                $number -= $key;
            }
        }

        return $result;
    }

    /**
     * Convert a Roman numeral to an integer.
     *
     * @param string $roman
     * @return int
     */
    public static function fromRoman($roman)
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        $result = 0;
        $roman = strtoupper($roman);

        foreach ($map as $key => $value) {
            while (strpos($roman, $key) === 0) {
                $result += $value;
                $roman = substr($roman, strlen($key));
            }
        }

        return $result;
    }
} 