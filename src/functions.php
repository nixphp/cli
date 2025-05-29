<?php

use NixPHP\Cli\Support\CommandRegistry;
use function NixPHP\app;

function command(): CommandRegistry
{
    return app()->container()->get('command-registry');
}