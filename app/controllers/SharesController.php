<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/26/14
// Time: 1:10 PM
// For: CookieSync


class SharesController extends BaseController {

    public function __construct()
    {
        $this->user = Auth::user();
//        $this->beforeFilter('auth', ['only' => 'index']);
    }

    public function index()
    {
        $shared = $this->user->saves()->with('sharedInstance')->whereIsShared(1);
        if ($shared->count() > 0) {
            return View::make('shares')
                       ->with('saves', $shared->paginate(30))
                       ->with('saveCount', $shared->count())
                       ->with('latestSaveDate', $shared->first()->created_at->diffForHumans());
        }
        else {
            return View::make('shares')
                       ->with('saves', null)
                       ->with('saveCount', 0)
                       ->with('latestSaveDate', 'Never');
        }
    }

    public function show($id)
    {
        $sharedSave = SharedSave::whereShareCode($id)->first()->savedGame;
        if ($sharedSave) {
            return View::make('shared')
              ->with('save', $sharedSave)
              ->with('cookiesBaked', $sharedSave->cookies())
              ->with('allTimeCookies', $sharedSave->allTimeCookies());
        }
        // Else
        App::abort(404);
    }

    public function hide($id)
    {
        $this->user->saves()->whereId(Input::get('save_id'))->first()->makePrivate();

        return Redirect::action('SharesController@index');
    }

} 
