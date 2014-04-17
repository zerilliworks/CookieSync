<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 4/16/14
// Time: 6:31 PM
// For: CookieSync

use CookieSync\Stat\Aggregator as Stats;

class CareerController extends BaseController {

    protected $user;

    function __construct()
    {
        $this->beforeFilter('auth');
        $this->beforeFilter('csrf', ['on' => 'post']);
        $this->user = Auth::user();
    }

    public function getCareerPage()
    {
        return View::make('career.index')
          ->withCareerCookies(Stats::careerCookies($this->user))
          ->withSaveCount(Stats::careerSaves($this->user))
          ->withGameCount(Stats::careerGames($this->user));
    }

    public function getCookieHistory()
    {
        return Stats::cookieHistory($this->user)->toJson();
    }
}
