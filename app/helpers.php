<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 10/28/13
// Time: 10:55 PM
// For: CookieSync


class NumericHelper {
    use CookieSync\Traits\NumberFormatting;
}


/**
 * Checks the current URL against a pattern and returns one value or another
 *
 * @param      $pattern
 * @param bool $if_true
 * @param bool $if_false
 * @return mixed
 */
function if_page($pattern, $if_true = null, $if_false = null) {
    return (Request::is($pattern))? $if_true : $if_false;
}

/**
 * Make numbers readable with place-separators and decimals.
 *
 * @param $num
 * @param string $placeSeparator Defaults to comma
 * @param string $decimalSeparator Defaults to period
 * @return string
 */
function prettyNumbers($num, $placeSeparator = ',', $decimalSeparator = '.')
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

function round_num($num) {
    return NumericHelper::makeRoundedHumanReadable($num);
}
