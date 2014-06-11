<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/24/14
// Time: 9:00 PM
// For: CookieSync


use CookieSync\Stat\Aggregator;
use Illuminate\Support\Collection;

class GamesController extends BaseController {

    protected $user;

    use \CookieSync\Traits\ModelBatchTrait;

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
        $data['game'] = $game;
        $data['latestSave'] = $game->latestSave()->decode();
        $data['saveCount'] = $game->saves()->count();

        return View::make('gamedetail', $data);
    }

    public function getBuildingHistory($gameId)
    {
        $sample = Input::get('sample', 30);
        $output = [
            'cursors'       => [],
            'grandmas'      => [],
            'farms'         => [],
            'factories'     => [],
            'mines'         => [],
            'shipments'     => [],
            'labs'          => [],
            'portals'       => [],
            'time_machines' => [],
            'condensers'    => [],
            'prisms'        => [],
        ];

        $current = 0;
        foreach($this->user->saves()->whereGameId($gameId)->paginate(Session::get('pagination_length', 30)) as $save) {
            $current++;
            $save->decode();

            foreach($save->buildings as $building => $count) {
                $output[$building][] = $count;
            }
        }

        return Response::json($output);

    }

    public function getCookieHistory($gameId)
    {
        $history = new Collection;

        foreach($this->user->saves()->whereGameId($gameId)->paginate(Session::get('pagination_length', 30)) as $save) {
            $history->push([$save->created_at, $save->gameStat('raw_banked_cookies')]);
        }

        return Response::json($history);
    }

}
