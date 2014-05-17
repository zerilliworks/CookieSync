<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/13/14
// Time: 11:33 PM
// For: CookieSync

use Carbon\Carbon;

class ExternalController extends BaseController {

    public function asyncPostSaveData()
    {
        $data = Input::get('d');
        $token = Input::get('t');

        $referenceToken = DB::table('external_tokens')->whereToken($token)->first();

        // Ensure that the reference token is within the expiration limit.
        // If not, BALEETED!
        if($referenceToken && $referenceToken->expires->gt(Carbon::now()->subMinutes(5)))
        {
            // so if it's good, let's store the save and swap tokens.

            Auth::onceUsingId($referenceToken->user_id);                            // Login for this request only
            Auth::user()->saves()->save(new Save(array('save_data' => $data)));     // Insert the save

            $newToken = $this->generateToken();                                     // New token
            DB::table('external_tokens')->whereToken($token)->update([              // Update stored token
                                                   'token' => $newToken,
                                                   'expires' => Carbon::now()->addMinutes(5)
                                                 ]);

            return Response::make($newToken, 201);                                  // Tell the client we're good

        } else {
            App::abort(401);
        }
    }

    public function redirectToCookieClicker()
    {
        $externalToken = $this->generateToken();

        DB::table('external_tokens')->insert([
            'user_id' => Auth::user()->id,
            'token' => $externalToken,
            'expires' => Carbon::now()->addMinutes(5)
                                             ]);

        return Redirect::to("http://orteil.dashnet.org/cookieclicker?cs_token=$externalToken");
    }

    private function generateToken()
    {
        return str_random(38);
    }

} 
