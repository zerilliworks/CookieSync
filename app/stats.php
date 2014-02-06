<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 11/21/13
// Time: 9:50 PM
// For: CookieSync

/**
 * http://stathat.com
*/


function do_post_request($url, $data, $optional_headers = null)
{
    $params = array('http' => array(
        'method' => 'POST',
        'content' => $data
    ));
    if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if (!$fp) {
        throw new Exception("Problem with $url, $php_errormsg");
    }
    $response = @stream_get_contents($fp);
    if ($response === false) {
        throw new Exception("Problem reading data from $url, $php_errormsg");
    }
    return $response;
}

function do_async_post_request($url, $params)
{
    foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
                    isset($parts['port'])?$parts['port']:80,
                    $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}

function stathat_count($stat_key, $user_key, $count)
{
    return do_async_post_request("http://api.stathat.com/c", array('key' => $stat_key, 'ukey' => $user_key, 'count' => $count));
}

function stathat_value($stat_key, $user_key, $value)
{
    do_async_post_request("http://api.stathat.com/v", array('key' => $stat_key, 'ukey' => $user_key, 'value' => $value));
}

function stathat_ez_count($email, $stat_name, $count)
{
    do_async_post_request("http://api.stathat.com/ez", array('email' => $email, 'stat' => $stat_name, 'count' => $count));
}

function stathat_ez_value($email, $stat_name, $value)
{
    do_async_post_request("http://api.stathat.com/ez", array('email' => $email, 'stat' => $stat_name, 'value' => $value));
}

function stathat_count_sync($stat_key, $user_key, $count)
{
    return do_post_request("http://api.stathat.com/c", "key=$stat_key&ukey=$user_key&count=$count");
}

function stathat_value_sync($stat_key, $user_key, $value)
{
    return do_post_request("http://api.stathat.com/v", "key=$stat_key&ukey=$user_key&value=$value");
}

function stathat_ez_count_sync($email, $stat_name, $count)
{
    return do_post_request("http://api.stathat.com/ez", "email=$email&stat=$stat_name&count=$count");
}

function stathat_ez_value_sync($email, $stat_name, $value)
{
    return do_post_request("http://api.stathat.com/ez", "email=$email&stat=$stat_name&value=$value");
}


/**
 * Register stat tracking for 404 errors
*/

App::missing(function($exception)
{
    stathat_ez_count(Config::get('stathat.ezkey'), 'CS 404 Errors', 1);
});

App::singleton('stathat.querylog', function()
{
    return new \CookieSync\Stat\StatHatEvent();
});

Event::listen('cookiesync.done', function($responseTime) {
//    Log::debug("Application response time: $responseTime ms");
    stathat_ez_value(Config::get('stathat.ezkey'), 'CS Response Times', $responseTime);
    App::make('stathat.querylog')->flush();
});

Event::listen('illuminate.query', function($query, $bindings, $time, $connectionName) {
    App::make('stathat.querylog')->statName('CS SQL Queries per Request')->fire();
});

Event::listen('cookiesync.newuser', function($newbie) {
    stathat_ez_count(Config::get('stathat.ezkey'), 'CS New Users', 1);
    stathat_ez_value(Config::get('stathat.ezkey'), 'CS Total Users', intval(User::count()));
});

Event::listen('cookiesync.newsave', function($save) {
    Log::info('Event cookiesync.newsave fired.');
    stathat_ez_count(Config::get('stathat.ezkey'), 'CS New Saves', 1);
    stathat_ez_value(Config::get('stathat.ezkey'), 'CS Total Saves', intval(Save::count()));
});

Event::listen('cookiesync.savedeleted', function($save) {
    stathat_ez_count(Config::get('stathat.ezkey'), 'CS Saves Deleted', 1);
    stathat_ez_value(Config::get('stathat.ezkey'), 'CS Total Saves', intval(Save::count()));
});

Event::listen('cookiesync.saveshared', function($save) {
    stathat_ez_count(Config::get('stathat.ezkey'), 'CS Shared Saves', 1);
});

Event::listen('cookiesync.userdestroyed', function($name, $id) {
    stathat_ez_count(Config::get('stathat.ezkey'), 'CS Users Deleted', 1);
    stathat_ez_value(Config::get('stathat.ezkey'), 'CS Total Users', intval(User::count()));

});