<?php

use NixPHP\Cli\Commands\ListCommand;
use NixPHP\Cli\Support\CommandRegistry;
use function NixPHP\app;

app()->container()->set('commandRegistry', function() {
    $commandRegistry = new CommandRegistry();
    $commandRegistry->add(ListCommand::class);
    return $commandRegistry;
});
