<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 11/21/13
// Time: 11:15 PM
// For: CookieSync

namespace CookieSync\Contracts;

interface GameDataContract {

    public function data($key);
    public function isGrandmapocalypse();
    public function decode();
    public function decodeWith($data);

    public function cookies();
    public function allTimeCookies();

    public function buildingStats($name = null);

}