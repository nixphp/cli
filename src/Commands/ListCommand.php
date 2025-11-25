<?php

declare(strict_types=1);

namespace NixPHP\CLI\Commands;

use NixPHP\CLI\Core\AbstractCommand;
use NixPHP\CLI\Core\Input;
use NixPHP\CLI\Core\Output;
use NixPHP\CLI\Exception\ConsoleException;

class ListCommand extends AbstractCommand
{
    public const string NAME = 'command:list';

    private array $commands;

    public function __construct(array $commands = [])
    {
        parent::__construct();
        $this->commands = $commands;
    }

    protected function configure(): void
    {
        $this->setTitle('Registered commands')
            ->setDescription('List all registered commands.');
    }

    public function run(Input $input, Output $output): int
    {
        $commands = [];
        $longestChars = 0;

        foreach ($this->commands as $command) {

            if ($command === 'NixPHP\Cli\Commands\ListCommand') {
                continue;
            }

            if (!defined($command . '::NAME') || !is_string(constant($command . '::NAME'))) {
                throw new ConsoleException('Command class "' . $command . '" must define a static NAME constant.');
            }


            $commandName = $command::NAME;
            $commandNameLength = strlen($commandName);

            if ($commandNameLength > $longestChars) {
                $longestChars = $commandNameLength;
            }

            $commands[$commandName] = $this->getCommandInfo($command);

        }

        ksort($commands);
        $firstColumnLength = $longestChars + 10;

        foreach ($commands as $commandName => $commandInfo) {

            $output->writeLine($commandInfo['title'], 'headline');
            $output->writeLine(str_pad($commandName, $firstColumnLength, ' . ') . $commandInfo['description']);
            $output->writeEmptyLine();

        }

        return self::SUCCESS;
    }

    /**
     * @param string $command
     * @return array
     */
    private function getCommandInfo(string $command): array
    {
        /** @var AbstractCommand $instance */
        $instance = new $command();

        return [
            'title'           => $instance->getTitle(),
            'description'     => $instance->getDescription(),
            'inputDefinition' => $instance->getDefinition()
        ];
    }
}