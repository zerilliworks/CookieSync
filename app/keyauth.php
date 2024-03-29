<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/26/13
// Time: 11:15 PM
// For: CookieSync

use Illuminate\Auth\UserInterface;

class KeyAuth implements \Illuminate\Auth\UserProviderInterface {

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveById($identifier)
    {
        return User::find($identifier);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return User::whereName($credentials['name'])->wherePasskey($credentials['passkey'])->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Auth\UserInterface $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserInterface $user, array $credentials)
    {
        if(
            $user->name === $credentials['name']
            &&
            $user->passkey === $credentials['passkey']
        ) {
            return true;
        }
        else
        {
            return false;
        }
    }
}