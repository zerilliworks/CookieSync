<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/26/13
// Time: 11:06 PM
// For: CookieSync

class UserModelTest extends TestCase {

    public function testcanCreateUser()
    {
        $user = new User();

        $user->name = 'GoodUser';
        $user->password = Hash::make('GoodPassword');
        $user->save();

        $this->assertNotNull($user->id);
        $this->assertEquals('GoodUser', $user->name);
        $this->assertNotEquals('GoodPassword', $user->password);
    }


}