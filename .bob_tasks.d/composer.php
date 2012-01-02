<?php

namespace Bob;

// Your package's name. Used to identify it on Packagist.org
function getName()
{
    return 'chh/bob';
}

// Should return the package's version.
//
// An idea would be to fetch the latest tag from the repo 
// in your favourite VCS.
function getVersion()
{
}

// Should return an array of authors.
// Each individual author should be an array of `name`, 
// `email` and optionally a `homepage`
//
// By default this parses an `AUTHORS.txt` file in the root
// of the project which is formatted with an author name on
// each line. For example:
//
//     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
//     John Doe <john@example.com>
//
function getAuthors()
{
    $authorsFile = 'AUTHORS.txt';

    if (!file_exists($authorsFile)) {
        return array();
    }

    $authors = array();

    foreach (new \SplFileObject($authorsFile) as $line) {
        if (preg_match('/^(.+) <(.+)>$/', $line, $matches)) {
            $authors[] = array(
                'name' => $matches[1],
                'email' => $matches[2]
            );
        }
    }

    return $authors;
}

function getExecutables()
{
    $binDir = 'bin';

    if (!is_dir($binDir)) {
        return array();
    }

    $executables = array();

    foreach (new \DirectoryIterator($binDir) as $file) {
        if ($file->isFile() and $file->isExecutable()) {
            $logicalPath = substr($file->getRealpath(), strlen($_SERVER['PWD']) + 1);
            $executables[] = $logicalPath;
        }
    }

    return $executables;
}

desc('Generates the composer.json from the composer_spec.php');
task('composer.json', array('composer_spec.php'), function($task) {
    $NAME = getName();
    $AUTHORS = getAuthors();
    $EXECUTABLES = getExecutables();
    $VERSION = getVersion();

    $pkg = include($task->prerequisites[0]);

    if (!is_array($pkg)) {
        println('Error: composer_spec.php MUST return an array');
        exit(1);
    }

    $jsonOptions = 0;

    if (defined('JSON_PRETTY_PRINT')) {
        $jsonOptions |= JSON_PRETTY_PRINT;
    }

    if (defined("JSON_UNESCAPED_SLASHES")) {
        $jsonOptions |= JSON_UNESCAPED_SLASHES;
    }

    $json = json_encode($pkg, $jsonOptions);

    println('Writing composer.json');
    @file_put_contents($task->name, $json);
});