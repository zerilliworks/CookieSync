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

Route::get('cookiesync', [
  'as' => 'root',
  function () {
      // Send them to the login page
      if (Auth::guest()) {
          return Redirect::route('login');
      }
      else {
          return Redirect::action('SavesController@index');
      }
  }
]);

Route::group(['prefix' => 'cookiesync'], function () {
    Route::get('access', ['as' => 'login', 'uses' => 'AuthController@getLoginView']);
    Route::post('access/login', 'AuthController@postLoginCredentials');
    Route::post('access/register', 'AuthController@postRegistrationInfo');
    Route::get('logout', [
      'as' => 'logout',
      function () {
          Auth::logout();
          return Redirect::route('root');
      }
    ]);
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| The User must be logged in to see these.
|
*/

Route::group(['before' => 'auth', 'prefix' => 'cookiesync'], function () // Auth route group
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

Route::group(['before' => 'auth', 'prefix' => 'cookiesync'], function () // Auth route group
{

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
    Route::get('options/password', 'OptionsController@getResetPassword');
    Route::post('options/password', 'OptionsController@postResetPassword');

    Route::get('shared', 'SharesController@index');

    Route::post('shares/hide/{id}', 'SharesController@hide');


    /*
    | End Authenticated Routes
    */

});

Route::group(['prefix' => 'cookiesync'], function ()
{
    /*
    |--------------------------------------------------------------------------
    | Ancillary Public Routes
    |--------------------------------------------------------------------------
    */

    Route::get('shared/{id}', 'SharesController@show');

    Route::get('about', [
      'as' => 'about_page',
      function () {
          return View::make('about');
      }
    ]);

    Route::get('changelog', function () {
        $log = DB::table('changelog')->orderBy('release_date', 'desc')->remember(120)->get();

        return View::make('changelog')->with('changes', $log);
    });

    Route::get('goodbye', [
      'as' => 'goodbye',
      function () {
          if (Session::get('goodbye') == 'yes' || true) {
              return View::make('goodbye');
          }
          else {
              return Redirect::to('access');
          }
      }
    ]);

    /*
    | End Public Routes
    */

});


Route::post('cookiesync/queue/grind', function () {
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

View::composer(['about', 'access'], function ($view) {
    $view->with('userCount', Cache::remember('user_count', 30, function () {
        return User::count();
    }));
    $view->with('saveCount', Cache::remember('save_count', 30, function () {
        return Save::count();
    }));

    $compute = function () {
        if (!Cache::has('soft_global_cookie_count')) {
            Queue::push('CookieSync\Workers\Statistical\GlobalStats', []);
            return \NumericHelper::makeRoundedHumanReadable(Cache::get('sticky_global_cookie_count'));
        }
        return \NumericHelper::makeRoundedHumanReadable(Cache::get('soft_global_cookie_count'));
    };
    try {
        $view->with('cookieCount', $compute());
    } catch (Exception $e) {
        return $view->with('cookieCount', \NumericHelper::makeRoundedHumanReadable(Cache::get('sticky_global_cookie_count', 'A lot of ')));
    }
});
// ---
// End View Composers
// ---

App::missing(function() {
    Log::error('HTTP 404: No route found to handle '. Request::fullUrl());
    return Response::make('Not Found', 404);
});
