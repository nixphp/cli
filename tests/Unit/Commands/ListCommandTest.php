<?php

namespace Tests\Unit\Commands;

use NixPHP\Cli\Commands\ListCommand;
use NixPHP\Cli\Core\AbstractCommand;
use NixPHP\Cli\Core\Input;
use NixPHP\Cli\Core\Output;
use NixPHP\Cli\Exception\ConsoleException;
use Tests\NixPHPTestCase;

class TestCommand1 extends AbstractCommand
{
    public const string NAME = 'test:command1';
    
    protected function configure(): void
    {
        $this->setTitle('Test Command 1')
            ->setDescription('Test command 1 description');
    }
    
    public function run(Input $input, Output $output): int
    {
        return self::SUCCESS;
    }
}

class TestCommand2 extends AbstractCommand
{
    public const string NAME = 'test:command2';
    
    protected function configure(): void
    {
        $this->setTitle('Test Command 2')
            ->setDescription('Test command 2 description');
    }
    
    public function run(Input $input, Output $output): int
    {
        return self::SUCCESS;
    }
}

class InvalidCommand
{
    // Diese Klasse hat absichtlich keine NAME-Konstante
    public function someMethod()
    {
        return true;
    }
}

class ListCommandTest extends NixPHPTestCase
{
    private ListCommand $command;
    private array $commands;

    protected function setUp(): void
    {
        $this->commands = [
            TestCommand1::class,
            TestCommand2::class,
            'NixPHP\Cli\Commands\ListCommand',
        ];
        
        $this->command = new ListCommand($this->commands);
    }

    public function testConfigureSetsCorrectTitleAndDescription(): void
    {
        $this->assertSame('Registered commands', $this->command->getTitle());
        $this->assertSame('List all registered commands.', $this->command->getDescription());
    }

    public function testRunSkipsListCommandItself(): void
    {
        $output = $this->createMock(Output::class);
        $input = $this->createMock(Input::class);
        
        // Wir erwarten, dass writeLine für beide Testkommandos aufgerufen wird (2 mal pro Kommando)
        $output->expects($this->exactly(4))
            ->method('writeLine');
        
        // Wir erwarten, dass writeEmptyLine für beide Testkommandos aufgerufen wird
        $output->expects($this->exactly(2))
            ->method('writeEmptyLine');
        
        $result = $this->command->run($input, $output);
        
        $this->assertSame(0, $result);
    }

    public function testRunThrowsExceptionForInvalidCommandClass(): void
    {
        $this->expectException(ConsoleException::class);
        
        // Mock eine Klasse, die keine NAME-Konstante hat
        $invalidCommands = [
            InvalidCommand::class,
        ];
        
        $command = new ListCommand($invalidCommands);
        $input = $this->createMock(Input::class);
        $output = $this->createMock(Output::class);
        
        $command->run($input, $output);
    }
}
