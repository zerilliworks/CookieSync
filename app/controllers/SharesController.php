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
    }

    public function index()
    {
        $shared = $this->user->saves()->whereIsShared(1);
        return View::make('shares')
            ->with('saves', $shared->paginate(30))
            ->with('saveCount', $shared->count())
            ->with('latestSaveDate', $shared->first()->created_at->diffForHumans());
    }

    public function show($id)
    {
        $sharedSave = SharedSave::whereShareCode($id)->first()->savedGame;
        if ($sharedSave) {
            return View::make('shared')->with('save', $sharedSave);
        }
        // Else
        App::abort(404);
    }

    public function hide($id)
    {

    }

} 