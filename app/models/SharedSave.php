<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 10/28/13
// Time: 8:36 PM
// For: CookieSync

class SharedSave extends Eloquent {

    protected $table = 'shares';

    public static function boot()
    {
        parent::boot();

        SharedSave::creating(function($share) {
            $share->share_code = str_random(12).$share->id;
        });
    }

    public function user()
    {
        return $this->saveGame()->user();
    }

    public function savedGame()
    {
        return $this->belongsTo('Save', 'save_id');
    }

}