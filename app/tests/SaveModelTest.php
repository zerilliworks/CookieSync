<?php
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 10/29/13
// Time: 11:54 AM
// For: CookieSync


class SaveModelTest extends TestCase {

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
        $save = new Save();
        $save->user_id = 1;

        $save->save_data = $this->exampleData;
        $save->save();

        $this->assertTrue($save->decode());

        // Test properties
        $this->assertNotEmpty($save->gameData);
        $this->assertArrayHasKey('game_version', $save->gameData);
        $this->assertArrayHasKey('date_started', $save->gameData);
        $this->assertArrayHasKey('date_saved', $save->gameData);
        $this->assertArrayHasKey('banked_cookies', $save->gameData);
        $this->assertArrayHasKey('alltime_cookies', $save->gameData);
        $this->assertArrayHasKey('buildings.cursors', $save->gameData);
        $this->assertArrayHasKey('buildings.grandmas', $save->gameData);
        $this->assertArrayHasKey('buildings.farms', $save->gameData);
        $this->assertArrayHasKey('buildings.factories', $save->gameData);
        $this->assertArrayHasKey('buildings.mines', $save->gameData);
        $this->assertArrayHasKey('buildings.shipments', $save->gameData);
        $this->assertArrayHasKey('buildings.labs', $save->gameData);
        $this->assertArrayHasKey('buildings.portals', $save->gameData);
        $this->assertArrayHasKey('buildings.time_machines', $save->gameData);
        $this->assertArrayHasKey('buildings.condensers', $save->gameData);

        // Test that all the numbers line up
        $this->assertEquals('1.0393', $save->gameData['game_version']);

        $this->assertInstanceOf('Carbon\\Carbon', $save->gameData['date_started'    ]);
        $this->assertInstanceOf('Carbon\\Carbon', $save->gameData['date_saved'      ]);

        $this->assertEquals('2013-11-23 19:40:01', $save->gameData['date_started'   ]);
        $this->assertEquals('2013-12-05 02:55:25', $save->gameData['date_saved'     ]);

        $this->assertEquals('25583991634810068', $save->gameData['banked_cookies'   ] );
        $this->assertEquals('74785401907208880', $save->gameData['alltime_cookies'  ] );

        $this->assertEquals('221', $save->gameData['buildings.cursors'       ] );
        $this->assertEquals('185', $save->gameData['buildings.grandmas'      ] );
        $this->assertEquals('161', $save->gameData['buildings.farms'         ] );
        $this->assertEquals('150', $save->gameData['buildings.factories'     ] );
        $this->assertEquals('140', $save->gameData['buildings.mines'         ] );
        $this->assertEquals('130', $save->gameData['buildings.shipments'     ] );
        $this->assertEquals('120', $save->gameData['buildings.labs'          ] );
        $this->assertEquals('115', $save->gameData['buildings.portals'       ] );
        $this->assertEquals('101', $save->gameData['buildings.time_machines' ] );
        $this->assertEquals('100', $save->gameData['buildings.condensers'    ] );

    }

    public function testRejectsInvalidSave()
    {
        $save = new Save();
        $save->save_data = "not a cookie clicker save";

        $this->assertFalse($save->decode());
    }

    public function testRejectsCorruptedSave()
    {
        // Remove a few characters from the exampleData
        $idx = floor(rand(1, strlen($this->exampleData)));
        $corruptData = substr($this->exampleData, 0, $idx) . substr($this->exampleData, $idx + floor(rand(1, 6)));

        $save = new Save();
        $save->save_data = $corruptData;

        $this->assertFalse($save->decode());
    }

    public function testRejectsEmptySave()
    {
        $save = new Save();
        $save->save_data = "";

        $this->assertFalse($save->decode());
    }

    public function testCanReadUpgrades()
    {
        $save = new Save();
        $save->save_data = $this->exampleData;
        $save->decode();

        $this->assertNotEmpty($save->gameData['upgrades.binary']);
        $this->assertThat($save->gameData['upgrades.binary'], $this->isType('string'));
        $this->assertNotEmpty($save->gameData['upgrades']);
        $this->assertThat($save->gameData['upgrades'], $this->isType('array'));
    }

    public function testCanReadAchievements()
    {

    }

}
