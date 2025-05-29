<?php

namespace NixPHP\Cli\Core;

use NixPHP\Cli\Exception\ConsoleException;
use NixPHP\Cli\Support\CommandRegistry;

class Console
{

    private CommandRegistry $registry;

    public function __construct(CommandRegistry $commandRegistry)
    {
        $this->registry = $commandRegistry;
    }
    
    public function run(array $parameters): void
    {
        // The first argument is the bin/console command itself
        array_shift($parameters);

        $commandName = array_shift($parameters);
        $commandArgs = [];

        if (empty($commandName)) {
            $commandName = 'command:list';
            $commandArgs[] = $this->registry->getAll();
        }

        $command = $this->registry->get($commandName);

        if (null === $command) {
            throw new ConsoleException(
                sprintf('Command %s not found', $commandName)
            );
        }

        try {
            /** @var AbstractCommand $object */
            $object = new $command(...$commandArgs);
            $definition = $object->getDefinition();

            $input = new Input($parameters, $definition);
            $output = new Output();

            if ($object->getTitle()) {
                $output->drawStroke(strlen($object->getTitle()) + 3, '-');
                $output->writeLine(sprintf(' %s', $object->getTitle()), 'title');
                $output->drawStroke(strlen($object->getTitle()) + 3, '-');
                $output->writeEmptyLine();
            }

            $object->run($input, $output);

        } catch (ConsoleException $e) {
            print $e->getMessage();
            print PHP_EOL;
        }

        print PHP_EOL;
    }

}