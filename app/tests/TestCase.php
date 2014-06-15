<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

	/**
	 * Creates the application.
	 *
	 * @return Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

        return require __DIR__.'/../../bootstrap/start.php';
	}

    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('migrate', ['--package' => 'cartalyst/attributes']);
        $this->seed();
        User::create(['name' => 'jqtest', 'password' => Hash::make('password')]);
    }

    public function tearDown()
    {
        Mockery::close();
    }

}
