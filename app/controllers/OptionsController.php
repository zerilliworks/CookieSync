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
        return View::make('bookmarklet');
    }

    public function getDeleteUserView()
    {
        return View::make('nukeme')->with('username', $this->user->name);
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
        return View::make('password');
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

        if ($v->passes())
        {
            $this->user->password = Hash::make($data['password']);
            $this->user->save();

            return Redirect::action('OptionsController@getIndex')->withSuccess('Password has been reset!');
        } else {
            return Redirect::back()->withErrors($v);
        }
    }

} 
