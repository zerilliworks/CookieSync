<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 11/1/13
// Time: 5:15 PM
// For: CookieSync
namespace CookieSync\Stat;

class RoughNumber {

    protected $humanNumbersUnderTwenty = array(
        'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen',
        'eighteen', 'nineteen'
    );

    protected $tensPrefixes = array(
        '', 'twenty-', 'thirty-', 'forty-', 'fifty-', 'sixty-', 'seventy-', 'eighty-', 'ninety-'
    );

    protected $ordersOfMagnitudePrefixes = array(
        '', 'thousand', 'million', 'billion', 'trillion', 'quadrillion',
        'quintillion', 'sextillion', 'septillion' // If they make more cookies than this, well...
    );

    public $humanNumber;

    public function __construct($number)
    {
        $floored = bcadd($number, 0, 0);

        // Reverse string so it's not grouped backwards
        $segments = str_split(strrev($floored), 3);

        // Get the segments back in order...
        // $segments = array_reverse($segments);

        // reverse the numbers of each segment back to normal
        $segments = array_map(function($value)
        {
            return strrev($value);
        }, $segments);

        $languageString = '';

        for ($i = count($segments) - 1; $i >= 0; $i--)
        {
            echo 'Segment ' . $i . ': ' . $segments[$i] . "\n";

            $segmentString = '';
            // Use the underTwenty numbers if appropriate
            if(intval($segments[$i]) < 20 && intval($segments[$i]) > 0)
            {
                echo 'Segment is under twenty...' . "\n";
                $segmentString .= $this->humanNumbersUnderTwenty[intval($segments[$i])] . ' ';
                echo $segmentString . "\n";
                echo $segmentString .= $this->ordersOfMagnitudePrefixes[$i];
            }
            elseif(intval($segments[$i]) > 0)
            {

                $hundreds = substr($segments[$i], 0, 1);
                $tens = substr($segments[$i], 1, 1);
                $ones = substr($segments[$i], 2, 1);

                if($iv = intval($hundreds) > 0)
                {
                    $segmentString .= $this->humanNumbersUnderTwenty[$iv] . ' hundred ';
                }

                if(intval($tens) >= 2)
                {
                    $iv = intval($tens);
                    $segmentString .= $this->humanNumbersUnderTwenty[$iv] . $this->tensPrefixes[$iv];
                }

                if(intval($ones) > 0 && intval($tens) >= 2)
                {
                    $segmentString .= $this->humanNumbersUnderTwenty[intval($ones)];
                } elseif(intval($tens) < 2) {
                    $segmentString .= $this->humanNumbersUnderTwenty[intval(bcadd($ones, $tens))] . ' ';
                }

                echo $segmentString .= $this->ordersOfMagnitudePrefixes[$i];
            }

            $languageString = "$segmentString $languageString";
        }

        $this->humanNumber = $languageString;

    }

    public static function make($number)
    {
        return new RoughNumber($number);
    }

    public function __toString()
    {
        return $this->humanNumber;
    }

}