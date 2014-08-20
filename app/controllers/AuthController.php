<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/24/14
// Time: 7:52 PM
// For: CookieSync


use Carbon\Carbon;
use CookieSync\Tools\Facades\EmailManager;
use Hashids\Hashids;

class AuthController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('csrf', ['on' => 'post']);
        $this->beforeFilter('guest', ['except' => 'getVerifyEmail']);
    }

    public function getLoginView()
    {
        return View::make('access');
    }

    public function postLoginCredentials()
    {
        // See if the provided username is actually an email address
        $v = Validator::make(['identifier' => Input::get('username')], ['identifier' => 'email']);

        if ($v->fails()) {
            // Do if the username is an email
            $authResult = Auth::attempt(
                              array(
                                  'name'     => Input::get('username'),
                                  'password' => Input::get('password')
                              ), true
            );
        }
        else {
            // Do if the user name is not an email
            $authResult = Auth::attempt(
                              array(
                                  'email'    => Input::get('username'),
                                  'password' => Input::get('password')
                              ), true
            );


        }

        if($authResult) {
            return Redirect::action('SavesController@index');
        } else {
            // Return to login page with errors
            return Redirect::route('login')
                           ->withErrors("Credentials didn't match any registered user.")
                           ->with('username', Input::get('username'));
        }
    }

    public function postRegistrationInfo()
    {
        // Build a validator: require both fields, usernames longer than 4 characters
        $v = Validator::make(
                      Input::only('username', 'email', 'password', 'password_confirmation', 'Asirra_Ticket'), array(
                          'username'      => 'required|min:4|unique:users,name',
                          'email'         => 'email|unique:users,email|unique:users,pending_email',
                          'password'      => 'required|confirmed|min:6',
                          'Asirra_Ticket' => 'required|asirra'
                      ));

        // Were those creds valid?
        if ($v->fails()) {
            // Send back to the access page with validation errors
            return Redirect::route('login')->withErrors($v)->withInput(['username', 'email']);
        }

        // Whip up a new user

        // Make the user and fill its data
        $newb                = new User();
        $newb->name          = Input::get('username');
        $newb->password      = Hash::make(Input::get('password'));

        // Store the user
        $newb->save();

        // Send the verification email
        EmailManager::requestNewEmail($newb, Input::get('email'));

        // Manually log in
        Auth::login($newb);

        Event::fire('cookiesync.newuser', array($newb));

        // Go to welcome page
        return Redirect::route('welcome_page');
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::to('cookiesync');
    }


    public function getVerifyEmail($hash)
    {

        try {
            Log::info("Verifying email with hash $hash");

            EmailManager::verifyEmailHash($hash);

            return Redirect::route('cookiesync.mysaves.index')->with('success', 'Your email address has been verified.');

        } catch (\CookieSync\Authentication\Exceptions\AlreadyVerifiedException $e) {
            return Redirect::route('cookiesync.mysaves.index')->with('status', "Email already verified.");

        } catch (\CookieSync\Authentication\Exceptions\EmailTokenExpiredException $e) {
            return Redirect::route('cookiesync.mysaves.index')->with('status', "This link has expired.");

        } catch (\CookieSync\Authentication\Exceptions\EmailTokenIncorrectException $e) {
            return Redirect::route('cookiesync.mysaves.index')->with('status', "Email verification link was bad.");

        }


    }

}
