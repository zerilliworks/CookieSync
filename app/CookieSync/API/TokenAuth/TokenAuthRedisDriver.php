<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 6/30/14
// Time: 12:42 AM
// For: CookieSync


namespace CookieSync\API\TokenAuth;


/**
 * Class TokenAuthRedisDriver
 * @package CookieSync\API\TokenAuth
 */
class TokenAuthRedisDriver implements TokenAuthDriverInterface {

    /**
     * @var
     */
    protected $redis;

    /**
     * @param $redis
     */
    function __construct($redis)
    {
        $this->redis = $redis;
    }

    /**
     * @param $token
     * @param $userId
     *
     * Store the token as a hash with enabled flag and user ID;
     * Add the token to a set for fast lookups and to keep track of the
     * total count without having to count individual keys (O(1) time
     * instead of O(N) time).
     */
    public function storeToken($token, $userId)
    {
        // Create the token key with a value 'enabled'
        $this->redis->hmset($token, 'enabled', 1, 'user_id', $userId);

        // Add the token to a set for fast lookup
        $this->redis->sAdd('api:tokens', $token);
    }

    /**
     * @param $token
     * @return bool
     *
     * Check whether a token exists and is enabled: checks if the key
     * is a member of the active set.
     */
    public function hasToken($token)
    {
        return ($this->redis->sIsMember('api:tokens', $token) && $this->redis->get($token) == 'enabled');
    }

    /**
     * @return int
     *
     * Return the number of tokens currently in the system
     */
    public function countTokens()
    {
        return (int) $this->redis->scard('api:tokens');
    }

    /**
     * @param $token
     *
     * Delete a token and remove it from the set
     */
    public function removeToken($token)
    {
        $this->redis->sRem('api:tokens', $token);
        $this->redis->del($token);
    }

    /**
     * @param $token
     *
     * Deactivate a token but leave it in the system
     */
    public function blockToken($token)
    {
        $this->redis->hset($token, 'enabled', 0);
    }

    /**
     * @param $token
     *
     * (Re)activate a token in the system
     */
    public function allowToken($token)
    {
        $this->redis->hset($token, 'enabled', 1);
    }

    /**
     * @param $token
     * @return int
     *
     * Get the user ID that belongs to a token
     */
    public function userForToken($token)
    {
        return (int) $this->redis->hget($token, 'user_id');
    }

    /**
     * @param $token
     * @return mixed
     *
     * Get the full data of the token object
     */
    public function getToken($token)
    {
        return $this->redis->hgetall($token);
    }
}
