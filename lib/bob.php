<?php

namespace Bob;

function printLn($line)
{
    echo "[bob] $line\n";
}

// Renders a PHP template
function template($file, $vars = array())
{
    if (!file_exists($file)) {
        throw \InvalidArgumentException(sprintf(
            'File %s does not exist.', $file
        ));
    }

    $template = function($__file, $__vars) {
        foreach ($__vars as $var => $value) {
            $$var = $value;
        }
        unset($__vars, $var, $value);

        ob_start();
        include($__file);
        $rendered = ob_get_clean();

        return $rendered;
    };

    return $template($file, $vars);
}

class Application
{
    var $tasks = array();
    var $descriptions = array();
    var $argv = array();

    var $definitionPath;

    function task($name, $callback)
    {
        $this->tasks[$name] = $callback;
        return $this;
    }

    function desc($text)
    {
        $this->descriptions[count($this->tasks)] = $text;
        return $this;
    }

    function loadDefinition()
    {
        if (!$this->definitionPath) {
            $this->definitionPath = getcwd();
        }

        $definition = "{$this->definitionPath}/bob_config.php";

        if (!file_exists($definition)) {
            throw new \Exception(sprintf(
                'Error: No bob_config.php found in %s', $definition)
            );
        }

        include $definition;
    }

    function execute($name)
    {
        if (!isset($this->tasks[$name])) {
            throw new \Exception(sprintf('Task "%s" not found.', $name));
        }

        $task = $this->tasks[$name];
        return call_user_func($task, $this);
    }
}