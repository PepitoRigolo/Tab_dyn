<?php

namespace App;

 class NumberHelper {


    public static function price(float $number): string
    {
        return number_format($number, 0, '', ' '). ' €';
    }
 }