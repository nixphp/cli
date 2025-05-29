<?php

use NixPHP\Cli\Commands\ListCommand;
use NixPHP\Cli\Commands\MigrateCommand;
use NixPHP\Cli\Commands\MigrationCreateCommand;
use NixPHP\Cli\Support\CommandRegistry;
use function NixPHP\app;

app()->container()->set('commandRegistry', function() {
    $commandRegistry = new CommandRegistry();
    $commandRegistry->add(ListCommand::class);
    if (app()->hasPlugin('nixphp/database')) {
        $commandRegistry->add(MigrateCommand::class);
        $commandRegistry->add(MigrationCreateCommand::class);
    }
    return $commandRegistry;
});