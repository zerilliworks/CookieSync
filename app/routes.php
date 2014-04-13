<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function () {
    // Send them to the login page
    if (Auth::guest()) {
        return Redirect::route('login');
    }
    else {
        return Redirect::to('mysaves');
    }
});

//Route::group(['before' => 'guest'], function () {
    Route::get('access', ['as' => 'login', 'uses' => 'AuthController@getLoginView']);
    Route::post('access/login', 'AuthController@postLoginCredentials');
    Route::post('access/register', 'AuthController@postRegistrationInfo');
    Route::get('logout', function()
    {
        Auth::logout();
        return Redirect::route('login');
    });
//});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| The User must be logged in to see these.
|
*/

Route::group(['before' => 'auth'], function () // Auth route group
{

    Route::get('welcome', [
        'as' => 'welcome_page',
        function () {
            return View::make('welcome');
        }
    ]);

    Route::get('welcome/example', [
        'as' => 'welcome_example',
        function () {
            $thisSave            = new Save();
            $thisSave->save_data = Config::get('cookiesync.example_data');
            $thisSave->decode();

            return View::make('example')
                       ->with('save', $thisSave)
                       ->with('cookiesBaked', $thisSave->cookies())
                       ->with('allTimeCookies', $thisSave->allTimeCookies());
        }
    ]);
});

Route::resource('mysaves', 'SavesController');

Route::post('mysaves/undelete', 'SavesController@undoDestroy');
Route::post('mysaves/makepublic', 'SavesController@makePublic');
Route::post('mysaves/makeprivate', 'SavesController@makePrivate');
Route::get('external', 'SavesController@storeExternal');



Route::resource('games', 'GamesController');

Route::get('options', 'OptionsController@getIndex');
Route::get('options/bookmarklet', 'OptionsController@getBookmarklet');
Route::get('options/nukeme', 'OptionsController@getDeleteUserView');
Route::post('options/nukeme/doit', 'OptionsController@postDeleteUserRequest');

Route::resource('shared', 'SharesController');

Route::post('shares/hide/{id}', 'SharesController@hide');

/*
| End Authenticated Routes
*/



/*
|--------------------------------------------------------------------------
| Ancillary Public Routes
|--------------------------------------------------------------------------
*/

Route::get('shared/{id}', 'SavesController@shared');

Route::get('about', function () {
    return View::make('about');
});

Route::get('changelog', function () {
    $log = DB::table('changelog')->orderBy('release_date', 'desc')->remember(120)->get();

    return View::make('changelog')->with('changes', $log);
});

Route::get('goodbye', function () {
    if (Session::get('goodbye') == 'yes' || true) {
        return View::make('goodbye');
    }
    else {
        return Redirect::to('access');
    }
});

/*
| End Public Routes
*/


Route::get('queue/grind', function()
{
    return Queue::marshal();
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::group(['prefix' => '{api}/v1'], function () {
    Route::get("users/{id}", "ApiController@getUser");
    Route::get("users/{id}/{attribute}", "ApiController@getUserAttribute");
    Route::get("users/findby/email/{email}", "ApiController@getFindUserByEmail");
    Route::get("users/findby/username/{username}", "ApiController@getFindUserByUsername");

    Route::get('users/{id}/cookies', 'ApiController@getCookiesForUser');
    Route::get('users/{id}/saves', 'ApiController@getSavesForUser');
    Route::get('users/{id}/games', 'ApiController@getGamesForUser');
    Route::get('users/{id}/shares', 'ApiController@getSharedSavesForUser');

    Route::post('users/{id}/saves', 'ApiController@createSaveForUser');
});

/*
| End API Routes
*/


/**
 * View Composers
 */

View::composer('about', function ($view) {
    $view->with('userCount', Cache::remember('user_count', 30, function()
    {
        return User::count();
    }));
    $view->with('saveCount', Cache::remember('save_count', 30, function()
    {
        return Save::count();
    }));
    $view->with('cookieCount', function() {
        if (Cache::has('global_cookie_count')) {
           return \NumericHelper::makeRoundedHumanReadable(Cache::get('global_cookie_count'));
        } else {
            Queue::push('CookieSync\Workers\Statistical\GlobalStats', []);
            return "A whole lot of cookies";
        }
    });
});
// ---
// End View Composers
// ---
