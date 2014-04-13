<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 2/6/14
// Time: 8:07 PM
// For: CookieSync

namespace CookieSync\Traits;
use Illuminate\Support\Facades\Lang;

trait NumberFormatting {

    static function makeRoundedHumanReadable($number)
    {
        $number = strval($number);
        // Chop off the decimal if it has one
        $number = explode('.', $number)[0];
        $places = strlen($number);

        // Determine how many powers of ten apply to this number, rounded to the
        // next lowest increment of three (equates to thousand -> million -> trillion, etc.)
        if($places > 3 && $places < 46)
        {
            if($places % 3 == 0)
            {
                $powersOfTen = $places - 3;
            } else {
                $powersOfTen = max($places - ($places % 3), 0);
            }
        } else if ($places < 46) {
            return "Exactly $number";
        } else {
            return "An insane number";
        }

        // Slice off at most the first three digits of the number
        $numericLead = substr($number, 0, ($places % 3 ? $places % 3 : 3 ));

        // Add a few decimals for precision if necessary
        if($places > 6) {
            $numericLead .= '.' . substr($number, ($places % 3 ? $places % 3 : 3 ), 3);
        }

        return "$numericLead " . studly_case(Lang::get("numbers.powers_of_ten.$powersOfTen"));
    }

    function makeExactHumanReadable($number)
    {

    }

    static function makePrettyNumber($num, $placeSeparator = ',', $decimalSeparator = '.')
    {
        $numstring = strval($num);
        if(strpos($numstring, '.'))
        {
            $components = explode('.', $numstring);
            $vl = strrev($components[0]);
            $output = "";
            foreach (str_split($vl, 3) as $chunk){
                $output = strrev($chunk) . $placeSeparator . $output;
            }

            return substr($output, 0, -1) . $decimalSeparator . substr($components[1],0,2);
        }
        else
        {
            $vl = strrev($numstring);
            $output = "";
            foreach (str_split($vl, 3) as $chunk){
                $output = strrev($chunk) . $placeSeparator . $output;
            }

            return substr($output, 0, -1);
        }
    }

}
