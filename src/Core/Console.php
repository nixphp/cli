<?php

declare(strict_types=1);

namespace NixPHP\CLI\Core;

use NixPHP\CLI\Commands\ListCommand;
use NixPHP\CLI\Exception\ConsoleException;
use NixPHP\CLI\Support\CommandRegistry;
use function NixPHP\app;

class Console
{
    /**
     * @param CommandRegistry $registry
     */
    public function __construct(
        private readonly CommandRegistry $registry
    ) {
    }

    /**
     * @param array $parameters
     *
     * @return void
     */
    public function run(array $parameters): void
    {
        // The first argument is the bin/console command itself
        array_shift($parameters);

        $commandName = array_shift($parameters);

        if (empty($commandName)) {
            $commandName = 'command:list';
        }

        $commandClass = $this->registry->get($commandName);

        try {

            if (null === $commandClass) {
                throw new ConsoleException(
                    sprintf('Command "%s" not found', $commandName)
                );
            }

            if ($commandClass === ListCommand::class || $commandName === ListCommand::NAME) {
                $object = new ListCommand();
                $object->setCommands($this->registry->all());
            } else {
                /** @var AbstractCommand $object */
                $object = app()->container()->make($commandClass);
            }

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

        } catch (\Exception $e) {
            print PHP_EOL;
            print $e->getMessage();
            print PHP_EOL;
        }

        print PHP_EOL;
    }

}