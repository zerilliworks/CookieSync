<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/24/14
// Time: 7:52 PM
// For: CookieSync


class AuthController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', ['on' => 'post']);
        $this->beforeFilter('guest');
    }

    public function getLoginView()
    {
        return View::make('access');
    }

    public function postLoginCredentials()
    {
        // The credentials were ostensibly valid, but let's prove it.
        if (Auth::attempt(
                array(
                    'name' => Input::get('username'),
                    'password' => Input::get('password')
                )
        )) {
            // Go to dashboard
            return Redirect::intended('mysaves');
        }
        else {
            // Return to login page with errors
            return Redirect::route('login')->withErrors("Username and password didn't match any registered combination.");
        }
    }

    public function postRegistrationInfo()
    {
        // Build a validator: require both fields, usernames longer than 4 characters
        $v = Validator::make(
                      Input::only('username', 'password', 'password_confirmation', 'Asirra_Ticket'), array(
            'username' => 'required|min:4|unique:users,name',
            'password' => 'required|confirmed|min:6',
            'Asirra_Ticket' => 'required|asirra'
        ));

        // Were those creds valid?
        if($v->fails())
        {
            // Send back to the access page with validation errors
            return Redirect::route('login')->withErrors($v);
        }

        // Whip up a new user

        // Make the user and fill its data
        $newb = new User();
        $newb->name = Input::get('username');
        $newb->password = Hash::make(Input::get('password'));

        // Store the user
        $newb->save();

        // Manually log in
        Auth::login($newb);

        Event::fire('cookiesync.newuser', array($newb));

        // Go to welcome page
        return Redirect::route('welcome_page');
    }

    public function logout()
    {
        Auth::logout();
        return Redirect::root();
    }

} 