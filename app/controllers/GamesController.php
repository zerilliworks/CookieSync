<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/24/14
// Time: 9:00 PM
// For: CookieSync


class GamesController extends BaseController {

    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function index()
    {
        $games     = $this->user->games()->orderBy('date_saved', 'desc')->paginate(Session::get('pagination_length', 30));
        $gameCount = $this->user->games()->count();

        return View::make('games')->with('gameCount', $gameCount)
                   ->with('games', $games);
    }

    public function show($id)
    {
        $game = $this->user->games()->whereId($id)->first();

        if (!$game) {
            return App::abort(404);
        }

        $data = array('saves' => $game->saves()->orderBy('created_at', 'desc')->paginate(Session::get('pagination_length', 30)));

        if ($c = $game->latestSave()) {

            $data['latestSaveDate'] = $c->created_at->diffForHumans();
        }
        else {
            $data['latestSaveDate'] = 'None';
        }

        $data['careerCookies'] = $c->cookies();
        $data['saveCount'] = $game->saves()->count();

        return View::make('mysaves', $data);
    }

} 
