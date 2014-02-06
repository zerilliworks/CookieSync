<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/29/13
// Time: 12:16 PM
// For: CookieSync

class AppLogicTest extends TestCase {

    public function testGuestRootRedrectsToAccess()
    {
        Auth::logout();
        $response = $this->call('GET', '/');
        $this->assertRedirectedTo('access');
    }

    public function testLogin()
    {
        $response = $this->call('POST', 'access/login', array('username' => 'jqtest',
                                           'password' => 'sensiblepassword',
                                           '_token' => csrf_token()));

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('mysaves');
    }
}