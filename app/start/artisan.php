<?php

/*
|--------------------------------------------------------------------------
| Register The Artisan Commands
|--------------------------------------------------------------------------
|
| Each available Artisan command must be registered with the console so
| that it is available to be called. We'll register every command so
| the console gets access to each of the command object instances.
|
*/

Artisan::add(new \CookieSync\Commands\FixGamesCommand);
Artisan::add(new \CookieSync\Commands\SetupCommand);
Artisan::add(new \CookieSync\Commands\MigrateBakeryEpochCommand());

// Artisan::add(new \CookieSync\Commands\DumpRawSavesCommand);
// Artisan::add(new \CookieSync\Commands\LoadRawSavesCommand);
