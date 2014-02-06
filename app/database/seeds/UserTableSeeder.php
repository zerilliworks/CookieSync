<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 10/29/13
// Time: 3:11 AM
// For: CookieSync

class UserTableSeeder extends DatabaseSeeder {

    public function run()
    {
        $goodUser = array('name' => 'jqtest',
                          'password' => Hash::make('sensiblepassword'),
                          'created_at' => \Carbon\Carbon::create(2013, 5, 13, 14, 35, 0),
                          'updated_at' => \Carbon\Carbon::create(2013, 9, 22, 11, 5, 0));

        DB::table('users')->insert($goodUser);

        DB::table('users')->insert(array(
                                        'name' => 'test1',
                                        'password' => Hash::make('testing'),
                                        'created_at' => \Carbon\Carbon::create(2013, 5, 13, 14, 35, 0),
                                        'updated_at' => \Carbon\Carbon::create(2013, 9, 22, 11, 5, 0)));

        DB::table('users')->insert(array(
                                        'name' => 'test2',
                                        'password' => Hash::make('testing'),
                                        'created_at' => \Carbon\Carbon::create(2013, 5, 14, 19, 45, 0),
                                        'updated_at' => \Carbon\Carbon::create(2013, 10, 22, 11, 5, 0)));

        DB::table('users')->insert(array(
                                        'name' => 'test3',
                                        'password' => Hash::make('testing'),
                                        'created_at' => \Carbon\Carbon::create(2013, 5, 15, 8, 15, 0),
                                        'updated_at' => \Carbon\Carbon::create(2013, 11, 22, 11, 5, 0)));
    }

}