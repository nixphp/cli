<?php

namespace NixPHP\Cli\Core;

use NixPHP\Cli\Exception\ConsoleException;
use function NixPHP\app;

class Console
{

    private array $commands;

    public function handle($parameters): void
    {
        try {
            $this->registerCoreCommands();
            $this->registerCustomCommands();

            $this->run($parameters);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
    
    private function run(array $parameters): void
    {
        // The first argument is the bin/console command itself
        array_shift($parameters);

        $commandName = array_shift($parameters);
        $commandArgs = [];

        if (empty($commandName)) {
            $commandName = 'command:list';
            $commandArgs[] = $this->commands;
        }

        if (false === isset($this->commands[$commandName])) {
            throw new ConsoleException(
                sprintf('Command %s not found', $commandName)
            );
        }

        $command = $this->commands[$commandName];

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

    private function registerCoreCommands(): void
    {
        $coreCommandsPath = realpath(__DIR__ . '/../Commands');
        $namespacePath    = 'NixPHP\Cli\Commands';

        /** @var AbstractCommand[] $commands */
        $commands = $this->loadCommandsFromDirectory($coreCommandsPath, $namespacePath);

        foreach ($commands as $command) {
            $this->commands[$command::NAME] = $command;
        }
    }

    private function registerCustomCommands(): void
    {
        $customCommands = realpath(app()->getBasePath() . '/app/Commands');

        if (false === $customCommands) {
            return;
        }

        $namespacePath    = 'App\Commands';
        $customCommands = $this->loadCommandsFromDirectory($customCommands, $namespacePath);

        /** @var AbstractCommand[] $commands */
        foreach ($customCommands as $command) {
            $this->commands[$command::NAME] = $command;
        }
    }

    /**
     * @param string $directory
     * @param string $namespacePath
     * @return array
     */
    private function loadCommandsFromDirectory(string $directory, string $namespacePath): array
    {
        $contents = array_diff(scandir($directory), ['.', '..', 'AbstractCommand.php']);

        $files = array_filter(
            $contents,
            static function ($file) use ($directory) {
                return !is_dir($directory . '/' . $file);
            }
        );

        return array_map(static function ($command) use ($namespacePath) {
            $className = substr($command, 0, -4);
            return sprintf('\%s\%s', $namespacePath, $className);
        }, $files);
    }
}