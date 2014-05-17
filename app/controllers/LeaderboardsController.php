<?php

class LeaderboardsController extends \BaseController {

  public function getIndex()
  {
    $query = Leader::with('user')->orderBy('cookies');
    return View::make('leaderboard')->with('leaders', $query->paginate());
  }
}
