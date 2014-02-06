<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 12/1/13
// Time: 11:15 PM
// For: CookieSync

class UserStat extends Eloquent {
    protected $table = 'user_stats';
    protected $fillable = array('name', 'value');

    public static function get($name)
    {
        $user = Auth::user();

        return $user->stats->whereName($name)->first();
    }

    public function put($name, $value)
    {
        $user = Auth::user();

        $stat = $user->stats->whereName($name)->first();
        if($stat)
        {
            $stat->value = $value;
            return $stat->save() ? $stat->value : false;
        }
        else
        {
            $user->stats->save(new UserStat(array('name' => $name, 'value' => $value)));
        }
    }
}