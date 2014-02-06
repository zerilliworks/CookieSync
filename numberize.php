<?php /////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
// Created by armand
// Date: 12/2/13
// Time: 10:49 PM
// For: CookieSync

if(!array_key_exists(1, $argv))
{
    echo "Please specify an input file." . PHP_EOL;
    exit;
}

$counter = 0;
$filename = __DIR__ . '/' . $argv[1];
$file = fopen($filename,'r+b');

if(!$file)
{
    echo "Couldn't read that file." . PHP_EOL;
    exit;
}

$contents = fread($file, filesize($filename));

while(preg_match('/\[n\]/', $contents) === 1)
{
    echo '.';
    $contents = preg_replace('/\[n\]/', $counter, $contents, 1);
    $counter++;
}

fwrite($file, $contents);
fclose($file);

echo PHP_EOL . "Replaced $counter items." . PHP_EOL;