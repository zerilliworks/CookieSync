<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/30/13
// Time: 8:49 PM
// For: CookieSync

class SaveNote extends Eloquent {



    public function user()
    {
        return $this->belongsTo('User');
    }

    public function savedGame()
    {
        return $this->belongsTo('Save');
    }

}