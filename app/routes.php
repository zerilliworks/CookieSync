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

Route::post('access', function() {
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
    // If this succeeds, the existing user will be logged in.
    // If it fails, a new user will automatically be created and
    // logged in.
    if(!Auth::attempt($creds))
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
    return Redirect::intended('mysaves');
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

    /**
     * Delete a save: requires a form submission with CSRF tokens, to avoid someone
     * illegitimately spamming nuke requests.
     */
    Route::post('mysaves/nuke/{id}', array('before' => 'csrf', function($id) {


        // FIXME: Silly, we've already authenticated! Just retreive the save through the user model.

        /*// Locate the save
        $s = Save::find($id);

        // Does this save actually belong to you?
        if(!$s->user->id === Auth::user()->id)
        {
            // Whoah, someone's trying to delete a save that isn't theirs!
            App::abort(500);
            return;

        } // Otherwise, continue.*/

        $s = Auth::user()->saves->find($id);

        // Do the deletion and redirect to dashboard.
        $s->delete();
        return Redirect::to('mysaves')->with('info', View::make('partials.undelete')->render());
    }));


    /**
     * View a save: fetch the saved game by ID and display it
     */
    Route::get('mysaves/{id}', array('before' => 'auth', function($id) {
        $thisSave = Auth::user()->saves()->whereId($id)->first();
        $thisSave->decode();
        return View::make('singlesave')
               ->with('save', $thisSave)
               ->with('cookiesBaked', $thisSave->cookies(true))
               ->with('allTimeCookies', $thisSave->allTimeCookies());
    }));




    /**
     * View all saves: Show a list of all saved games for the current user.
     */
    Route::get('mysaves', function() {
        return View::make('mysaves');
    });


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

        // CUT THE TIES
        Auth::logout();

        $victim->delete();

        Session::flash('goodbye', 'yes');
        return Redirect::to('goodbye');
    }));

    Route::post('mysaves/undelete', array('before' => 'csrf', function()
    {
        $lazarusSave = Auth::user()->saves()->onlyTrashed()->orderBy('deleted_at', 'desc')->first();

        if($lazarusSave)
        {
            $lazarusSave->restore();
            return Redirect::to('mysaves')->with('success', 'Save data restored!');
        }
        else
        {
            App::abort(500);
        }
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
    $user = Auth::user();
    $view->with('saveCount',$user->saves()->count());

    if($c = $user->latestSave()) {

        $view->with('latestSaveDate', $c->updated_at->diffForHumans());
    }
    else {
        $view->with('latestSaveDate', 'None');
    }

    $view->with('saves', $user->saves()->orderBy('created_at', 'desc')->get());

});

View::composer('about', function($view)
{
    $view->with('userCount', User::all()->count());
    $view->with('saveCount', Save::all()->count());
});
// ---
// End View Composers
// ---