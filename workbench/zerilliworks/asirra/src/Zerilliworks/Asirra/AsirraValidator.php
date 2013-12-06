<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 12/6/13
// Time: 2:34 AM
// For: CookieSync

namespace Zerilliworks\Asirra;

class AsirraValidator {

    public function checkTicket($ticket){
        $options = array(
            'http' => array(
                'method' => 'GET',
            )
        );

        $streamContext = stream_context_create($options);
        $url = "http://challenge.asirra.com/cgi/Asirra?action=ValidateTicket&ticket=$ticket";

        $asirraRequest = @fopen($url, 'rb', false, $streamContext);

        $asirraResponse = @stream_get_contents($asirraRequest);
        if($asirraResponse === false)
        {
            throw new \Exception("Asirra could not be reached at $url", $php_errormsg);
        }
        else
        {
            app('log')->info("Asirra response: $asirraResponse");

            $isValid = (substr_count($asirraResponse, 'Pass') > 0) ? true : false;
            $isFailure = (substr_count($asirraResponse, 'Fail') > 0) ? true : false;

            if($isFailure)
            {
                return false;
            } else if ($isValid) {
                return true;
            }
        }

    }

}