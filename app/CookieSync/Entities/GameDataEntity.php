<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 11/5/13
// Time: 6:04 PM
// For: CookieSync

namespace CookieSync\Entities;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class GameDataEntity implements JsonableInterface, ArrayableInterface {

    // Properties

    protected $cookiesBaked;
    protected $cookiesMadeAllTime;

    public function __construct(Save $data)
    {

    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        // TODO: Implement toJson() method.
    }}