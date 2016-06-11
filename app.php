#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();
$client = new GuzzleHttp\Client();

$euro2016 = new \Euro2016\Command\FixturesCommand($client);
$euro2016->setTimeZone(exec('date +%Z'));

$application->add($euro2016);
$application->run();
