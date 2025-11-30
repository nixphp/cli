<?php

declare(strict_types=1);

namespace NixPHP\CLI;

use NixPHP\CLI\Support\CommandRegistry;
use function NixPHP\app;

function command(): CommandRegistry
{
    return app()->container()->get(CommandRegistry::class);
}