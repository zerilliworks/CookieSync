<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 11/21/13
// Time: 11:23 PM
// For: CookieSync

class Game extends Eloquent {
    protected $fillable = array('user_id', 'date_started', 'date_saved', 'name', 'cookie_history');

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function saves()
    {
        return $this->hasMany('Save');
    }

    public function latestSave()
    {
        return $this->saves()->orderBy('saved_at', 'desc')->first();
    }
}