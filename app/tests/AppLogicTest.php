<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/29/13
// Time: 12:16 PM
// For: CookieSync

class AppLogicTest extends TestCase {

    public function assertTestEnvironment()
    {
        $this->assertEqual('testing', App::environment());
    }

    public function testGuestRootRedrectsToAccess()
    {
        Auth::logout();
        $response = $this->call('GET', 'cookiesync');
        $this->assertRedirectedTo('cookiesync/access');
    }

    public function testLogin()
    {
        $response = $this->call('POST', 'cookiesync/access/login', array('username' => 'jqtest',
                                           'password' => 'password',
                                           '_token' => csrf_token()));

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('cookiesync/mysaves');
    }
}