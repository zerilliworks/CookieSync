<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 4/16/14
// Time: 9:54 PM
// For: CookieSync


namespace CookieSync\Stat;


use Illuminate\Support\Facades\Config;

class Income {

    public static function buildingCost($type, $number = 1)
    {
        $cost = Config::get("cookiesync.building_costs")[$type];
        $calculated = bcmul($cost, bcpow(1.15, $number, 10), 10);
//        echo "Calculated: $calculated" . PHP_EOL;

        $comparison = bcsub($calculated, bcadd($calculated, 0), 10);

        $compared = bccomp($comparison, 0.5, 10);
//        echo "Compared: $compared" . PHP_EOL;
//        echo "Comparison: $comparison" . PHP_EOL;
        if($comparison == '0.0000000000')
        {
            return bcadd($calculated, 0, 0);
        }
        return bcadd($calculated, 1, 0);
    }

    public static function buildingExpense($type, $number = 1)
    {
        $accumulator = '0';
        for ($i = 0; $i <= $number; $i++) {
            $accumulator = bcadd($accumulator, static::buildingCost($type, $i));
        }
        return $accumulator;
    }

    public static function spentOnBuilding($type, $number)
    {
        $cost = Config::get("cookiesync.building_costs")[$type];
        if($number === 0)
        {
            return 0;
        }

        return static::buildingExpense($type, $number - 1);
    }

} 
