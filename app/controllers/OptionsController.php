<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/26/14
// Time: 12:17 PM
// For: CookieSync


class OptionsController extends BaseController {

    protected $user;

    public function __construct()
    {
        $this->beforeFilter('auth');
        $this->beforeFilter('csrf', ['on' => 'post']);
        $this->user = Auth::user();
    }

    public function getIndex()
    {
        return View::make('options');
    }

    public function getBookmarklet()
    {
        return View::make('options.bookmarklet');
    }

    public function getDeleteUserView()
    {
        return View::make('options.nukeme')->with('username', $this->user->name);
    }

    public function postDeleteUserRequest()
    {
        $victim =& $this->user;

        $id   = $victim->id;
        $name = $victim->name;

        // Delete all game saves
        $victim->saves()->forceDelete();
        $victim->games()->forceDelete();

        // CUT THE TIES
        Auth::logout();

        $victim->forceDelete();

        Session::flash('goodbye', 'yes');
        Event::fire('cookiesync.userdestroyed', array($id, $name));

        return Redirect::route('goodbye');
    }

    public function getResetPassword()
    {
        return View::make('options.password');
    }

    public function postResetPassword()
    {
        $data = Input::only('password', 'password_confirmation');

        $rules = [
            'password' => 'required|min:6|confirmed'
        ];

        $messages = [
            'confirmed' => 'Those passwords didn\'t match. Try again.',
            'min'       => 'Passwords must be at least 6 characters long'
        ];

        $v = Validator::make($data, $rules, $messages);

        if ($v->passes()) {
            $this->user->password = Hash::make($data['password']);
            $this->user->save();

            return Redirect::action('OptionsController@getIndex')->withSuccess('Password has been reset!');
        }
        else {
            return Redirect::back()->withErrors($v);
        }
    }

    public function postSetListLength()
    {
        $this->user->preferred_pagination_length = Input::get('list-length');
        $this->user->save();
        Session::set('pagination_length', Input::get('list-length'));

        return Redirect::back();
    }


    public function getEmailOptions()
    {
        return View::make('options.email')->with('emailAddress', $this->user->email);
    }

    public function postEmailOptions()
    {
        $v = Validator::make(
                      ['email' => Input::get('email')],
                      ['email' => 'required|email|unique:users']
        );

        if ($v->passes()) {
            $hashids             = new \Hashids\Hashids(Config::get('app.key'));
            $user                = $this->user;
            $newEmail            = Input::get('email');
            $userId              = $user->getAuthIdentifier();
            $verifyHash = $hashids->encrypt($userId, time());
            $userName            = $user->name;

            $user->email_verified = 0;
            $user->pending_email = $newEmail;
            $user->verify_hash   = $verifyHash;
            $user->save();

            Mail::send('emails.verify', ['hash' => $verifyHash, 'username' => $userName], function ($message) use ($userId, $newEmail, $userName) {
                $message->to($newEmail, $userName)
                        ->subject('Verify your Email Address');
            });

            return Redirect::action('OptionsController@getIndex')->withSuccess('Your Email Address has been updated! Look for a verification link in your inbox.');
        }
        else {
            return Redirect::back()->withErrors($v);
        }
    }

} 
