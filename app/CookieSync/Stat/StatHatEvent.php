<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 11/21/13
// Time: 10:24 PM
// For: CookieSync

namespace CookieSync\Stat;
use Illuminate\Support\Facades\Config;

class StatHatEvent {
    public $stat;
    public $count;
    public $type = 'value';

    public function count($value)
    {
        $this->type = 'count';
        return $this;
    }

    public function value($value)
    {
        $this->type = 'value';
        return $this;
    }

    public function statName($name)
    {
        $this->stat = $name;
        return $this;
    }

    public function fire()
    {
        $this->count++;
        return $this;
    }

    public function flush()
    {
        if($this->type = 'count')
        {
            stathat_ez_count(Config::get('stathat.ezkey'), $this->stat, $this->count);
        }
        else
        {
            stathat_ez_value(Config::get('stathat.ezkey'), $this->stat, $this->count);
        }
    }
}