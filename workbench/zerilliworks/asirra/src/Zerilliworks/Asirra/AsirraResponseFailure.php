<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 12/6/13
// Time: 2:47 AM
// For: CookieSync

namespace Zerilliworks\Asirra;

class AsirraResponseFailure extends \Exception {

    public function __construct($url, $message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(503, ($message === null) ? "The Asirra service could not be reached at $url" : $message,
                            $previous, array(), $code);
    }

}