<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 2/6/14
// Time: 8:07 PM
// For: CookieSync

namespace CookieSync\Traits;

trait NumberFormatting {

    function makeRoundedHumanReadable($number)
    {

    }

    function makeExactHumanReadable($number)
    {

    }

    function makePrettyNumber($num, $placeSeparator = ',', $decimalSeparator = '.')
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