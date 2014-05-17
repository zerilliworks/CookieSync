<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 4/9/14
// Time: 10:20 PM
// For: CookieSync

class Leader extends Eloquent {
  protected $fillable = [];
  protected $table = 'leaderboard';

  public function user()
  {
    return $this->belongsTo('User');
  }

  public function getUserNameAttribute()
  {
    return $this->user->name;
  }
}