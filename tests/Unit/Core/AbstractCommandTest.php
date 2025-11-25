<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use NixPHP\Cli\Core\AbstractCommand;
use NixPHP\Cli\Core\Input;
use NixPHP\Cli\Core\Output;
use Tests\NixPHPTestCase;

class AbstractCommandTest extends NixPHPTestCase
{
    private TestCommand $command;

    protected function setUp(): void
    {
        $this->command = new TestCommand();
    }

    public function testConstructorCallsConfigure(): void
    {
        $this->assertTrue($this->command->configureWasCalled);
    }

    public function testAddArgumentRequired(): void
    {
        $this->command->addTestArgument('test', false);
        $definition = $this->command->getDefinition();
        
        $this->assertArrayHasKey('arguments', $definition);
        $this->assertArrayHasKey('test', $definition['arguments']);
        $this->assertSame('required', $definition['arguments']['test']);
    }

    public function testAddArgumentOptional(): void
    {
        $this->command->addTestArgument('test', true);
        $definition = $this->command->getDefinition();
        
        $this->assertArrayHasKey('arguments', $definition);
        $this->assertArrayHasKey('test', $definition['arguments']);
        $this->assertSame('optional', $definition['arguments']['test']);
    }

    public function testAddOptionWithoutValue(): void
    {
        $this->command->addTestOption('test');
        $definition = $this->command->getDefinition();
        
        $this->assertArrayHasKey('options', $definition);
        $this->assertArrayHasKey('test', $definition['options']);
        $this->assertSame('flag', $definition['options']['test']);
    }

    public function testAddOptionWithShortcut(): void
    {
        $this->command->addTestOption('test', 't');
        $definition = $this->command->getDefinition();
        
        $this->assertArrayHasKey('options', $definition);
        $this->assertArrayHasKey('test', $definition['options']);
        $this->assertArrayHasKey('t', $definition['options']);
        $this->assertSame('flag', $definition['options']['test']);
        $this->assertSame('flag', $definition['options']['t']);
    }

    public function testAddOptionWithValue(): void
    {
        $this->command->addTestOption('test', '', true);
        $definition = $this->command->getDefinition();
        
        $this->assertArrayHasKey('options', $definition);
        $this->assertArrayHasKey('test', $definition['options']);
        $this->assertSame('value', $definition['options']['test']);
    }

    public function testShowHelp(): void
    {
        $output = $this->createMock(Output::class);
        
        $output->expects($this->atLeastOnce())
            ->method('writeLine');
        
        $this->command->addTestArgument('arg1', false);
        $this->command->addTestArgument('arg2', true);
        $this->command->addTestOption('option1');
        $this->command->addTestOption('option2', 'o', true);

        $this->command->showHelp($output);
    }
}

class TestCommand extends AbstractCommand
{
    public bool $configureWasCalled = false;

    public const NAME = 'test:command';

    protected function configure(): void
    {
        $this->configureWasCalled = true;
        $this->setTitle('Test Command')
            ->setDescription('Test command description');
    }

    public function run(Input $input, Output $output): int
    {
        return self::SUCCESS;
    }

    // Hilfsmethoden für Tests
    public function addTestArgument(string $name, bool $optional = false): self
    {
        return $this->addArgument($name, $optional);
    }

    public function addTestOption(string $name, ?string $shortcut = '', bool $expectsValue = false): self
    {
        return $this->addOption($name, $shortcut, $expectsValue);
    }
}
