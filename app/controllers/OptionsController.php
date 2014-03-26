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

        return Redirect::to('goodbye');
    }

} 