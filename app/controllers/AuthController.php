<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/24/14
// Time: 7:52 PM
// For: CookieSync


use Carbon\Carbon;
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
        // The credentials were ostensibly valid, but let's prove it.
        if (Auth::attempt(
                array(
                    'name' => Input::get('username'),
                    'password' => Input::get('password')
                ), true
        )) {
            // Go to dashboard
            Event::fire('cookiesync.logged_in', ['name' => Input::get('username')]);
            return Redirect::intended('cookiesync/mysaves');
        }
        else {
            // Return to login page with errors
            return Redirect::route('login')
              ->withErrors("Username and password didn't match any registered combination.")
              ->with('username', Input::get('username'));
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
        return Redirect::to('cookiesync');
    }


    public function getVerifyEmail($hash)
    {
        Log::info("Verifying email with hash $hash");
        $hashids     = new Hashids(Config::get('app.key'));
        Log::info('Decrypting hash...');
        $data        = $hashids->decrypt($hash);
        $userId      = intval($data[0]);
        $requestedAt = Carbon::createFromTimestamp($data[1]);

        Log::info("User id:$userId requested reset " . $requestedAt->diffForHumans());

        $user = User::find($userId);

        if ($user->email_verified) {
            // Abort, link is expired or already used
            Log::info('Email already verified.');
            return Redirect::route('cookiesync.mysaves.index')->with('status', "Email already verified.");
        }

        if ($hash !== $user->verify_hash) {
            // Abort, link does not match user
            Log::info('Verification hash isn\'t present on user.');
            return Redirect::route('cookiesync.mysaves.index')->with('status', "Email verification link was bad.");
        }

        // Make sure the link is less than 24 hours old
        // If older, void it and delete it.
        if ($requestedAt->lt(Carbon::now()->subHours(24))) {
            // Abort, link is expired or already used
            Log::info('Link has expired');
            return Redirect::route('cookiesync.mysaves.index')->with('status', "This link has expired.");
        }

        Log::info("Link looks good. Setting fields on user...");

        $user->email_verified = 1;
        $user->verify_hash = null;
        $user->email = $user->pending_email;
        $user->pending_email = null;
        $user->save();

        return Redirect::route('cookiesync.mysaves.index')->with('success', 'Your email address has been verified.');

    }

}
