<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 3/24/14
// Time: 8:12 PM
// For: CookieSync


/**
 * Class SavesController
 */
class SavesController extends BaseController {

    /**
     * @var Illuminate\Auth\UserInterface|null
     */
    protected $user;

    /**
     *
     */
    public function __construct()
    {
        $this->beforeFilter('csrf', ['on' => 'post']);
        $this->beforeFilter('auth', ['except' => 'shared']);
        $this->user = Auth::user();
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Resource Routes

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->user->load(array(
                              'saves' => function ($query) {
                                      $query->take(30);
                                  }
                          ));
        $data = array(
            'saveCount' => $this->user->saves()->count(),
            'gameCount' => $this->user->games()->count()
        );

        // FIXME: This uses two SQL queries to find the latest save date when one would do.
        // TODO: Use one query to grab all saves and then pick out the newest one.

        if ($c = $this->user->latestSave()) {

            $data['latestSaveDate'] = $c->created_at->diffForHumans();
        }
        else {
            $data['latestSaveDate'] = 'None';
        }

        $data['saves'] = $this->user->saves()->paginate(30);

        $careerCookies = '0';
        // Calculate the total cookies earned in all games
        foreach ($this->user->games()->get() as $game) {
            $careerCookies = bcadd($game->latestSave()->cookies(), $careerCookies);
        }

        $data['careerCookies'] = $careerCookies;


        return View::make('mysaves', $data);
    }



    /////////////////////////////////////////////////////////////////

    /**
     * With the nature of the application, we don't really use this.
     * If called, redirect to dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        return Redirect::action('SavesController@index');
    }



    /////////////////////////////////////////////////////////////////

    /**
     * POST to create save
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->user->saves()->save(new Save(array('save_data' => Input::get('savedata'))));

        return Redirect::action('SavesController@index');
    }




    /////////////////////////////////////////////////////////////////

    /**
     * GET to create save. Used by bookmarklet.
     *
     * The bookmarklet is a snippet of JavaScript that opens a new window
     * to this route with one query option: d (for data). The JS calls
     * Cookie Clicker's Game.WriteSave(1) and appends it to the URL. This
     * data is stored in the currently authenticated user's account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeExternal()
    {
        $didSave = $this->user->saves()->save(new Save(array('save_data' => Input::get('d'))));

        return View::make('bookmark.save')->with('didSave', $didSave);
    }




    /////////////////////////////////////////////////////////////////

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $thisSave = Auth::user()->saves()->whereId($id)->first();

        if (!$thisSave) {
            App::abort(404);
        }

        $ROIArray = [];

        foreach ($thisSave->buildings as $name => $owned)
        {
            $investment = $thisSave->buildings_expense[$name];
            $grossProfit = $thisSave->building_income[$name];
            $netProfit = bcsub($grossProfit, $investment);
            $ROIArray[$name] = floatval(bcmul(bcdiv($netProfit, $investment, 2), 100, 2));
        }

        $totalInvestment = $thisSave->total_buildings_expense;
        $totalProfit = $thisSave->total_building_income;
        $netProfit = bcsub($totalProfit, $totalInvestment);

        $totalROI = floatval(bcmul(bcdiv($netProfit, $totalInvestment), 100));


        return View::make('singlesave')
                   ->with('save', $thisSave)
                   ->with('cookiesBaked', $thisSave->cookies())
                   ->with('allTimeCookies', $thisSave->allTimeCookies())
                   ->with('buildingCount', $thisSave->building_count)
                   ->with('buildings', $thisSave->buildings)
                   ->with('clickedCookies', $thisSave->handmade_cookies)
                   ->with('buildingIncome', $thisSave->building_income)
                   ->with('totalBuildingIncome', $totalProfit)
                   ->with('buildingExpenses', $thisSave->buildings_expense)
                   ->with('buildingROI', $ROIArray)
                   ->with('totalROI', $totalROI)
                   ->with('totalBuildingExpenses', $totalInvestment);
    }



    /////////////////////////////////////////////////////////////////

    /**
     * We also don't really use this. Redirect to dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit()
    {
        return Redirect::action('SavesController@index');
    }



    /////////////////////////////////////////////////////////////////

    /**
     *
     */
    public function update()
    {

    }



    /////////////////////////////////////////////////////////////////

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $s = $this->user->saves->find($id);

        // Do the deletion and redirect to dashboard.
        $s->delete();

        if (Session::has('deleted_game')) {
            return Redirect::action('GamesController@index')->with('info', View::make('partials.undelete')->render());
        }
        else {
            return Redirect::back()->with('info', View::make('partials.undelete')->render());
        }
    }



    /////////////////////////////////////////////////////////////////

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function undoDestroy()
    {
        $lazarusSave = $this->user->saves()->onlyTrashed()->orderBy('deleted_at', 'desc')->first();

        if ($lazarusSave) {
            // First, see if the parent game is deleted
            if ($lazarusGame = $lazarusSave->game()->onlyTrashed()->first()) {
                // If it is, then restore the game.
                $lazarusGame->restore();
            }

            // Restore the save
            $lazarusSave->restore();

            return Redirect::back()->with('success', 'Save data restored!');
        }
        else {
            App::abort(500);
        }
    }






    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Sharing Routes

    public function makePublic()
    {
        $saveToShare = $this->user->saves()->whereId(Input::get('save_id'))->first();
        $pub         = $saveToShare->makePublic();

        return Redirect::action('SharesController@show', $pub->share_code);
    }


    /////////////////////////////////////////////////////////////////

    public function makePrivate()
    {
        $this->user->saves()->whereId(Input::get('save_id'))->first()->makePrivate();

        return Redirect::action('SavesController@index');
    }


    /////////////////////////////////////////////////////////////////

    public function listShared($id)
    {

    }
} 
