<?php

declare(strict_types=1);

use NixPHP\CLI\Commands\ListCommand;
use NixPHP\CLI\Commands\RouteDebugCommand;
use NixPHP\CLI\Support\CommandRegistry;
use function NixPHP\CLI\command;
use function NixPHP\app;

app()->container()->set(CommandRegistry::class, function() {
    $commandRegistry = new CommandRegistry();
    $commandRegistry->add(ListCommand::class);
    $commandRegistry->add(RouteDebugCommand::class);
    return $commandRegistry;
});
