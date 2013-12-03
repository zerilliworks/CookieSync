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

Route::get('/', function()
{
    // Send them to the login page
    if(Auth::guest())
    {
        return Redirect::to('access');
    }
    else
    {
        return Redirect::to('mysaves');
    }
});



/*
 * Access Page Routes
 */
Route::get('access', function() {
    return View::make('access');
});

Route::post('access/login', function() {
    $creds = array();

    // Set credentials to the form fields from the access page
    $creds['name'] = Input::get('username');
    $creds['password'] = Input::get('password');

    // Build a validator: require both fields, usernames longer than 4 characters
    $v = Validator::make($creds, array('name' => 'required|min:4', 'password' => 'required'));

    // Were those creds valid?
    if($v->fails())
    {
        // Send back to the access page with validation errors
        return Redirect::to('access')->withErrors($v);
    }

    // The credentials were ostensibly valid, but let's prove it.
    if(Auth::attempt($creds))
    {
        // Go to dashboard
        return Redirect::intended('mysaves');
    }
    else
    {
        // Return to login page with errors
        return Redirect::to('access')->withErrors("Username and password didn't match any registered combination.");
    }


});

Route::post('access/register', function() {
    $creds = array();

    // Set credentials to the form fields from the access page
    $creds['name'] = Input::get('username');
    $creds['password'] = Input::get('password');
    $creds['recaptcha'] = Input::get('recaptcha_response_field');

    // Build a validator: require both fields, usernames longer than 4 characters
    $v = Validator::make($creds, array('name' => 'required|min:4', 'password' => 'required', 'recaptcha' => 'required|recaptcha'));

    // Were those creds valid?
    if($v->fails())
    {
        // Send back to the access page with validation errors
        return Redirect::to('access')->withErrors($v);
    }

    // Whip up a new user
    if(!Auth::attempt(array('name' => $creds['name'], 'password' => $creds['password'])))
    {
        // Make the user and fill its data
        $newb = new User();
        $newb->name = $creds['name'];
        $newb->password = Hash::make($creds['password']);

        // Store the user
        $newb->save();

        // Manually log in
        Auth::login($newb);
    }

    // Go to dashboard
    return Redirect::to('welcome');
});

/*
 * End Access Page Routes
 */





/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| The User must be logged in to see these.
|
*/

Route::group(array('before' => 'auth'), function()      // Auth route group
{

    Route::get('welcome', function()
    {
        return View::make('welcome');
    });

    Route::get('welcome/example', function()
    {
        $thisSave = new Save();
        $thisSave->save_data = "MS4wMzkzfHwxMzg2MDg2MDEzNTE5OzEzODYwODYwMTM1MzM7MTM4NjA4NzA5NzI3MXwxMTExMTF8Njc5Ljg5NzcxMzc0NTc0NTQ7MTcwMC43OTk5OTk5OTk0Nzg0OzMzOTsw
OzQyOTsyOy0xOy0xOzA7MDswOzA7MDswOzA7MDswOzB8OCw4LDY0MywwOzIsMiw1MDgsMDsxLDEsMTE5LDA7MCwwLDAsMDswLDAsMCwwOzAsMCwwLDA7MCwwLDAsMDswLDAs
MCwwOzAsMCwwLDA7MCwwLDAsMDt8MjI1MTc5OTgyNDMzNDg2MzsyMjUxNzk5ODEzNjg1MjQ5OzIyNTE3OTk4MTM2ODUyNDk7MjI1MTc5OTgxMzY4NTI0OTsyMjUxNzk5ODEz
Njg1MjQ5OzEzNzQzODk1MzQ3M3wyMjcxNjk0MTAyMzMxNDA3OzIyNTE3OTk4MTM2ODUyNDk7MTAyNQ%3D%3D%21END%21";
        $thisSave->decode();
        return View::make('example')
               ->with('save', $thisSave)
               ->with('cookiesBaked', $thisSave->cookies(true))
               ->with('allTimeCookies', $thisSave->allTimeCookies());
    });
    
    
    /**
     * View all saves: Show a list of all saved games for the current user.
     */
    Route::get('mysaves', function() {

        $user = Auth::user();
        $data = array('saveCount' => $user->saves()->count());

        // FIXME: This uses two SQL queries to find the latest save date when one would do.
        // TODO: Use one query to grab all saves and then pick out the newest one.

        if($c = $user->latestSave()) {

            $data['latestSaveDate'] = $c->created_at->diffForHumans();
        }
        else {
            $data['latestSaveDate'] = 'None';
        }

        $data['saves'] = $user->saves()->orderBy('created_at', 'desc')->paginate(30);


        return View::make('mysaves', $data);
    });
    
    
    /**
     * View a save: fetch the saved game by ID and display it
     */
    Route::get('mysaves/{id}', array('before' => 'auth', function($id) {
        $thisSave = Auth::user()->saves()->whereId($id)->first();

        if(!$thisSave)
        {
            App::abort(404);
        }

        $thisSave->decode();
        return View::make('singlesave')
               ->with('save', $thisSave)
               ->with('cookiesBaked', $thisSave->cookies(true))
               ->with('allTimeCookies', $thisSave->allTimeCookies());
    }));
    
    /**
     * Delete a save: requires a form submission with CSRF tokens, to avoid someone
     * illegitimately spamming nuke requests.
     */
    Route::post('mysaves/nuke/{id}', array('before' => 'csrf', function($id) {

        $s = Auth::user()->saves->find($id);

        // Do the deletion and redirect to dashboard.
        $s->delete();

        if(Session::has('deleted_game'))
        {
            return Redirect::to('games')->with('info', View::make('partials.undelete')->render());
        }
        else
        {
            return Redirect::to(URL::previous())->with('info', View::make('partials.undelete')->render());
        }
    }));
    
    /**
     * Undo deleting a save.
     */
    
    Route::post('mysaves/undelete', array('before' => 'csrf', function()
    {
        $lazarusSave = Auth::user()->saves()->onlyTrashed()->orderBy('deleted_at', 'desc')->first();

        if($lazarusSave)
        {
            // First, see if the parent game is deleted
            if($lazarusGame = $lazarusSave->game()->onlyTrashed()->first())
            {
                // If it is, then restore the game.
                $lazarusGame->restore();
            }

            // Restore the save
            $lazarusSave->restore();
            return Redirect::to(URL::previous())->with('success', 'Save data restored!');
        }
        else
        {
            App::abort(500);
        }
    }));
    
    /**
     * Make a saved game public
     */
    Route::post('mysaves/makepublic', array('before' => 'csrf', function() {
        $saveId = Input::get('save_id');

        $saveToShare = Auth::user()->saves()->whereId($saveId)->first();
        $pub = $saveToShare->makePublic();

        return Redirect::to('shared/'.$pub->share_code);
    }));

    /**
     * View all games: Show a list of all games for the current user.
     */
    
    Route::get('games', function()
    {
        $games = Auth::user()->games()->orderBy('date_saved', 'desc')->paginate(30);
        $gameCount = Auth::user()->games()->count();

        return View::make('games')->with('gameCount', $gameCount)
            ->with('games', $games);
    });
    
    /**
     * View a specific game
     */

    Route::get('games/{id}', function($id)
    {
        $user = Auth::user();
        $game = $user->games()->whereId($id)->first();

        if(!$game)
        {
            return App::abort(404);
        }

        $data = array('saves' => $game->saves()->orderBy('created_at', 'desc')->paginate(30));

        if($c = $user->latestSave()) {

            $data['latestSaveDate'] = $c->created_at->diffForHumans();
        }
        else {
            $data['latestSaveDate'] = 'None';
        }

        $data['saveCount'] = $game->saves()->count();


        return View::make('mysaves', $data);
    });



    /**
     * Add a save: create a new save from the form on the site
     */
    Route::post('addsave', array('before' => 'auth|csrf', function() {
        $user = Auth::user();
        $user->saves()->save(new Save(array('save_data' => Input::get('savedata'))));

        return Redirect::to('mysaves');
    }));


    /**
     * Show the options page
     */
    Route::get('options', function() {
        return View::make('options');
    });


    /**
     * Show the page with the CookieSync bookmarklet
     */
    Route::get('bookmarklet', function() {
        return View::make('bookmarklet');
    });


    /**
     * Show the account deletion page
     */
    Route::get('nukeme', function() {
        return View::make('nukeme')->with('username', Auth::user()->name);
    });


    /**
     * Nuke me: Permanently delete an account and all data associated with it
     */
    Route::post('nukeme/doit', array('before' => 'csrf', function()
    {
        $victim = Auth::user();

        // Delete all game saves
        $victim->saves()->delete();
        $victim->games()->delete();

        // CUT THE TIES
        Auth::logout();

        $victim->delete();

        Session::flash('goodbye', 'yes');
        return Redirect::to('goodbye');
    }));

    


    /**
     * Load the view used by the bookmarklet.
     *
     * The bookmarklet is a snippet of JavaScript that opens a new window
     * to this route with one query option: d (for data). The JS calls
     * Cookie Clicker's Game.WriteSave(1) and appends it to the URL. This
     * data is stored in the currently authenticated user's account.
     */
    Route::get('external', function()
    {
        $datastring = Input::get('d'); // For "data"

        $didSave = Auth::user()->saves()->save(new Save(array('save_data' => $datastring)));

        return View::make('bookmark.save')->with('didSave', $didSave);
    });

});
/*
| End Authenticated Routes
*/






/*
|--------------------------------------------------------------------------
| Ancillary Public Routes
|--------------------------------------------------------------------------
*/

Route::get('about', function() {
    return View::make('about');
});


Route::get('goodbye', function() {
    if(Session::get('goodbye') == 'yes' || true)
    {
        return View::make('goodbye');
    }
    else
    {
        return Redirect::to('access');
    }
});

Route::get('logout', function() {
    Auth::logout();
    return Redirect::to('/');
});

Route::get('shared/{id}', function($id)
{
    $sharedSave = SharedSave::whereShareCode($id)->first()->savedGame;
    if($sharedSave)
    {
        $sharedSave->decode();
        return View::make('shared')->with('save', $sharedSave);
    }
    else
    {
        App::abort(404);
    }
});

/*
| End Public Routes
*/






/**
 * View Composers
 */

View::composer('mysaves', function($view)
{
//    $user = Auth::user();
//    $view->with('saveCount',$user->saves()->count());
//
//    // FIXME: This uses two SQL queries to find the latest save date when one would do.
//    // TODO: Use one query to grab all saves and then pick out the newest one.
//
//    if($c = $user->latestSave()) {
//
//        $view->with('latestSaveDate', $c->created_at->diffForHumans());
//    }
//    else {
//        $view->with('latestSaveDate', 'None');
//    }
//
//    $view->with('saves', $user->saves()->orderBy('created_at', 'desc')->paginate(30));

});

View::composer('about', function($view)
{
    $view->with('userCount', User::all()->count());
    $view->with('saveCount', Save::all()->count());
});
// ---
// End View Composers
// ---