<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/19/14
// Time: 12:11 AM
// For: CookieSync


namespace CookieSync\Traits;


trait BigNumberHandler {

    public function expandScientific($val)
    {
        if(strpos($val, 'e+') !== false) {
            $parts = explode('e+', $val);
            return bcmul($parts[0], bcpow('10', $parts[1]));
        } else {
            return $val;
        }
    }

} 
