<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 6/30/14
// Time: 12:53 AM
// For: CookieSync


namespace CookieSync\API\TokenAuth;


use Carbon\Carbon;
use DB;

class TokenAuthDatabaseDriver implements TokenAuthDriverInterface {


    public function storeToken($token, $userId)
    {
        DB::table('auth_tokens')->insert([
                                           'token'   => $token,
                                           'enabled' => 1,
                                           'user_id' => $userId,
                                           'expires' => (new Carbon())->addYear()
                                         ]);
    }

    public function removeToken($token)
    {
        DB::table('auth_tokens')->where('token', $token)->delete();
    }

    public function blockToken($token)
    {
        DB::table('auth_tokens')->where('token', $token)->update(['enabled' => 0]);
    }

    public function allowToken($token)
    {
        DB::table('auth_tokens')->where('token', $token)->update(['enabled' => 0]);
    }

    public function hasToken($token)
    {
        return (bool) DB::table('auth_tokens')->where('token', $token)->where('enabled', 1)->count();
    }

    public function countTokens()
    {
        return DB::table('auth_tokens')->count();
    }

    public function userForToken($token)
    {
        return DB::table('auth_tokens')->where('token', $token)->pluck('user_id');
    }

    public function getToken($token)
    {
        return DB::table('auth_tokens')->where('token', $token)->first()->toArray();
    }
}
