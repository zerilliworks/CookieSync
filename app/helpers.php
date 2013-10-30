<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 10/28/13
// Time: 10:55 PM
// For: CookieSync


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