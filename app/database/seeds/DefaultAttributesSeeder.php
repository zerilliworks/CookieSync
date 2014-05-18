<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 5/17/14
// Time: 10:16 PM
// For: CookieSync

use Cartalyst\Attributes\Attribute;

class DefaultAttributesSeeder extends Seeder {

    public function run()
    {
        Attribute::create([
            'slug' => 'preferred_pagination_length'
                          ]);
    }

} 
