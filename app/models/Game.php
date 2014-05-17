<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 11/21/13
// Time: 11:23 PM
// For: CookieSync

use Illuminate\Support\Contracts\ArrayableInterface;
use \Illuminate\Support\Contracts\JsonableInterface;

class Game extends Eloquent implements JsonableInterface, ArrayableInterface {
    protected $fillable = array('user_id', 'date_started', 'date_saved', 'name', 'cookie_history');
    protected $softDelete = true;

    public static function boot()
    {
        parent::boot();

        static::deleting(function($model)
        {
            Event::fire('cookiesync.gamedeleted', array($model));
        });
    }

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function saves()
    {
        return $this->hasMany('Save')->orderBy('created_at', 'desc');
    }

    public function latestSave()
    {
        return $this->saves[0];
    }

    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    public function toArray()
    {

    }
}