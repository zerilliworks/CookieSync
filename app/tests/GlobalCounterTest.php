<?php
//////////////////////////////////////////////////////////////////////
// Created by zerilliworks
// Date: 6/14/14
// Time: 11:36 PM
// For: CookieSync


use CookieSync\Stat\GlobalCookieCounter;

class GlobalCounterTest extends TestCase {

    protected $counterObject;
    protected $saveCollection;
    protected $cookieCount = 1005;

    public function tearDown()
    {
        Mockery::close();
    }

    public function setUp()
    {
        parent::setUp();

        $mockSaveCollection = new \Illuminate\Database\Eloquent\Collection();

        foreach(range(1, 10) as $r) {
            $fakeSave = Mockery::mock('Save');
            $fakeSave->shouldReceive('noCache->cookies')->andReturn("$this->cookieCount");
            $mockSaveCollection->push($fakeSave);
        }

        $mockGameCollection = new \Illuminate\Database\Eloquent\Collection();

        foreach(range(1, 10) as $r) {
            $fakeGame = Mockery::mock('Game');
            $fakeGame->shouldReceive('latestSave->noCache->cookies')->andReturn("$this->cookieCount");
            $mockGameCollection->push($fakeGame);
        }

        $mockSave = Mockery::mock('Save');
        $mockSave->shouldReceive('skip->take->get')->andReturn($mockSaveCollection);
        $mockSave->shouldReceive('count')->andReturn(10);

        $mockGame = Mockery::mock('Game');
        $mockGame->shouldReceive('skip->take->get')->andReturn($mockGameCollection);
        $mockGame->shouldReceive('count')->andReturn(10);

        $this->counterObject = new GlobalCookieCounter($mockGame, $mockSave);
    }

    public function testCountingAllSaves()
    {
        $result = $this->counterObject->calculateEverySave();

        $this->assertNotEmpty($result);
        $this->assertEquals($this->cookieCount * 10, intval($result));
    }

    public function testCountingAllGames()
    {
        $result = $this->counterObject->calculateGameCookies();

        $this->assertNotEmpty($result);
        $this->assertEquals($this->cookieCount * 10, intval($result));
    }

}
 