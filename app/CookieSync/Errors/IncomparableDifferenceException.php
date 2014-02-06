<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 12/5/13
// Time: 10:31 AM
// For: CookieSync

namespace CookieSync\Errors;

class IncomparableDifferenceException extends \Exception {

    public function __construct($comp1, $comp2)
    {
        parent::__construct("Two items are not comparable or not decoded: " . var_dump($comp1, $comp2));
    }

}