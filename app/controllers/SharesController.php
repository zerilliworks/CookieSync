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

            $sharedSave->decode();

            $cacheId = ($id == 'latest') ? $sharedSave->id : $id;
            $userId = $sharedSave->user->id;

            $ROIArray = Cache::remember("users:$userId:saves:$cacheId:roi", 120, function () use ($sharedSave) {
                $_ROIArray = [];
                foreach ($sharedSave->buildings as $name => $owned) {
                    $investment  = $sharedSave->building_expense[$name];
                    $grossProfit = $sharedSave->building_production[$name];
                    $netProfit   = bcsub($grossProfit, $investment);
                    if ($investment) {
                        $_ROIArray[$name] = floatval(bcmul(bcdiv($netProfit, $investment, 2), 100, 2));
                    }
                    else {
                        $_ROIArray[$name] = 0;
                    }
                }
                return $_ROIArray;
            });

            $totalInvestment = $sharedSave->total_buildings_expense;
            $totalProfit = $sharedSave->buildings_income;
            $netProfit       = bcsub($totalProfit, $totalInvestment);

            $totalROI = $totalInvestment ? floatval(bcmul(bcdiv($netProfit, $totalInvestment), 100)) : 0;


            return View::make('singlesave')
                       ->with('save', $sharedSave)
                       ->with('cookiesBaked', $sharedSave->cookies())
                       ->with('allTimeCookies', $sharedSave->allTimeCookies())
                       ->with('buildingCount', $sharedSave->building_count)
                       ->with('buildings', $sharedSave->buildings)
                       ->with('clickedCookies', $sharedSave->handmade_cookies)
                       ->with('buildingIncome', $sharedSave->building_production)
                       ->with('totalBuildingIncome', $totalProfit)
                       ->with('buildingExpenses', $sharedSave->building_expense)
                       ->with('buildingROI', $ROIArray)
                       ->with('totalROI', $totalROI)
                       ->with('totalBuildingExpenses', $totalInvestment)
                       ->with('sharedView', true);
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
