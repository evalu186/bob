<?php

function includeIfExists($file)
{
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
}

if ((!$loader = includeIfExists(__DIR__.'/../vendor/autoload.php')) && (!$loader = includeIfExists(__DIR__.'/../../../autoload.php'))) {
    die('You must set up the project dependencies, run the following commands:'.PHP_EOL.
        'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
        'php composer.phar install'.PHP_EOL);
}

require_once(__DIR__ . '/../lib/Bob.php');

// This is the top-level application instance, which holds all
// tasks and contains the logic for running them.
Bob::$application = new \Bob\Application;

exit(Bob::$application->run());
