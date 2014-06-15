<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/29/13
// Time: 11:54 AM
// For: CookieSync


class SaveModelTest extends TestCase {

    protected $save;

    private $exampleData = "MS4wMzkzfHwxMzg1MjM1NjAxMjY1O05hTjsxMzg2MjEyMTI1MDM4fDExMTExMXwyNTU4Mzk5MTYzNDgxMDA2ODs3NDc4
    NTQwMTkwNzIwODg4MDsxNzA3MTs3Mzk7MzQ1MTcwNDAyMDc2MjY1MS41OzY4Njk7LTE7LTE7Mjk5NjE4MTQ2NTUzODExNDA7MzsxOzA7MDstMTsxOzEx
    OTs1MjM2Mzc2NzkxODQwNTQ5OzI2NXwyMjEsMjIyLDYxMDY2NjEwMDM5MDcsMDsxODUsMTg1LDExNjM3NzY0MjYzMzk2LDA7MTYxLDE2MSwzOTkyMTcz
    MDY1LDA7MTUwLDE1MCw5NDcwNzM5NTk0LDA7MTQwLDE0MCwzMTAyNjY3MzA3NSwwOzEzMCwxMzAsNzU4ODIzMTcwODYsMDsxMjAsMTIwLDIzMjk2Mzg4
    NjY3MSwwOzExNSwxMTUsMzcwMjA5MDM3Mjc4NCwwOzEwMSwxMDEsNDMzMzg2MDY5Nzk2NzYsMDsxMDAsMTAwLDIyODA5MTMzMTc3NjEwNSwwO3w0NTAz
    NTk5NjI3MzcwNDk1OzQ1MDM1OTk2MjczNzA0OTU7MzM3NzY5OTcyMDUyNzg3MTs0NTAzNTczNzU1NDYwMDk1OzI4MTQ3NDk3NjU1MzM2OTU7MTY1MzU2
    MjQwNDE1fDQ1MDM1OTk2MjczNzA0OTU7MjI2MDQ5MjI4ODUyNDI4NzsxNTM1!END!";

    public function setUp()
    {
        parent::setUp();
        $this->save = new Save();
        $this->save->noCache();
        $this->be(User::whereName('jqtest')->first());
    }

    public function testCanCreateSave()
    {
        $save = new Save();
        $save->user_id = 1;
        $save->save_data = $this->exampleData;
        $this->assertTrue($save->save());
    }

    public function testCanDecodeSave()
    {
        $this->save->user_id = 1;

        $this->save->save_data = $this->exampleData;
        $this->save->save();

        $this->assertInstanceOf('Save', $this->save->decode());

        // Test properties
        $this->assertNotEmpty($this->save->gameData);
        $this->assertArrayHasKey('game_version', $this->save->gameData);
        $this->assertArrayHasKey('date_started', $this->save->gameData);
        $this->assertArrayHasKey('date_saved', $this->save->gameData);
        $this->assertArrayHasKey('banked_cookies', $this->save->gameData);
        $this->assertArrayHasKey('alltime_cookies', $this->save->gameData);
        $this->assertArrayHasKey('cursors', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('grandmas', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('farms', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('factories', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('mines', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('shipments', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('labs', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('portals', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('time_machines', $this->save->gameData['buildings']);
        $this->assertArrayHasKey('condensers', $this->save->gameData['buildings']);

        // Test that all the numbers line up
        $this->assertEquals('1.0393', $this->save->gameData['game_version']);

        $this->assertInstanceOf('Carbon\\Carbon', $this->save->gameData['date_started'    ]);
        $this->assertInstanceOf('Carbon\\Carbon', $this->save->gameData['date_saved'      ]);

        $this->assertEquals('2013-11-23 19:40:01', $this->save->gameData['date_started'   ]);
        $this->assertEquals('2013-12-05 02:55:25', $this->save->gameData['date_saved'     ]);

        $this->assertEquals('25583991634810068', $this->save->gameData['banked_cookies'   ] );
        $this->assertEquals('74785401907208880', $this->save->gameData['alltime_cookies'  ] );

        $this->assertEquals('221', $this->save->gameData['buildings']['cursors'       ] );
        $this->assertEquals('185', $this->save->gameData['buildings']['grandmas'      ] );
        $this->assertEquals('161', $this->save->gameData['buildings']['farms'         ] );
        $this->assertEquals('150', $this->save->gameData['buildings']['factories'     ] );
        $this->assertEquals('140', $this->save->gameData['buildings']['mines'         ] );
        $this->assertEquals('130', $this->save->gameData['buildings']['shipments'     ] );
        $this->assertEquals('120', $this->save->gameData['buildings']['labs'          ] );
        $this->assertEquals('115', $this->save->gameData['buildings']['portals'       ] );
        $this->assertEquals('101', $this->save->gameData['buildings']['time_machines' ] );
        $this->assertEquals('100', $this->save->gameData['buildings']['condensers'    ] );

    }


    /**
     * @covers Save::decode
     * @expectedException CookieSync\Errors\DecodingFailedException
     * @expectedException ErrorException
     */
    public function testRejectsInvalidSave()
    {
        $this->save->save_data = "not a cookie clicker save";
        $this->save->decode();
    }

    /**
     * @covers Save::decode
     * @expectedException CookieSync\Errors\DecodingFailedException
     * @expectedException ErrorException
     */
    public function testRejectsCorruptedSave()
    {
        // Remove a few characters from the exampleData
        $idx = floor(rand(1, strlen($this->exampleData)));
        $corruptData = substr($this->exampleData, 0, $idx) . substr($this->exampleData, $idx + floor(rand(1, 6)));

        $this->save->save_data = $corruptData;

        $this->save->decode();
    }

    /**
     * @covers Save::decode
     * @expectedException CookieSync\Errors\DecodingFailedException
     * @expectedException ErrorException
     */
    public function testRejectsEmptySave()
    {
        $this->save->save_data = "";

        $this->save->decode();
    }

    public function testCanReadUpgrades()
    {
        $this->save->save_data = $this->exampleData;
        $this->save->decode();

        $this->assertNotEmpty($this->save->gameData['upgrades.binary']);
        $this->assertThat($this->save->gameData['upgrades.binary'], $this->isType('string'));
        $this->assertNotEmpty($this->save->gameData['upgrades']);
        $this->assertThat($this->save->gameData['upgrades'], $this->isType('array'));
    }

    public function testCanReadAchievements()
    {

    }

}
