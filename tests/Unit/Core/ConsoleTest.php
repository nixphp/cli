<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use NixPHP\Cli\Core\AbstractCommand;
use NixPHP\Cli\Core\Console;
use NixPHP\Cli\Core\Input;
use NixPHP\Cli\Core\Output;
use NixPHP\Cli\Support\CommandRegistry;
use Tests\NixPHPTestCase;

class TestConsoleCommand extends AbstractCommand
{
    public const NAME = 'test:command';

    protected function configure(): void
    {
        $this->setTitle('Test Command')
            ->setDescription('Test command description');
    }

    public function run(Input $input, Output $output): int
    {
        // Einfacher Testbefehl
        return self::SUCCESS;
    }
}

class ErrorConsoleCommand extends AbstractCommand
{
    public const NAME = 'error:command';

    protected function configure(): void
    {
        // Keine Konfiguration notwendig für Test
    }

    public function run(Input $input, Output $output): int
    {
        throw new \Exception('Test error');
    }
}

class ConsoleTest extends NixPHPTestCase
{
    private CommandRegistry $registry;
    private Console $console;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(CommandRegistry::class);
        $this->console = new Console($this->registry);
    }

    public function testRunWithEmptyCommandCallsListCommand(): void
    {
        // Mock für CommandRegistry vorbereiten
        $this->registry->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $this->registry->expects($this->once())
            ->method('get')
            ->with('command:list')
            ->willReturn(TestConsoleCommand::class);

        // Ausgabe puffern
        ob_start();
        $this->console->run(['script.php']);
        ob_end_clean();
    }

    public function testRunWithCommandName(): void
    {
        // Mock für CommandRegistry vorbereiten
        $this->registry->expects($this->never())
            ->method('getAll');

        $this->registry->expects($this->once())
            ->method('get')
            ->with('test:command')
            ->willReturn(TestConsoleCommand::class);

        // Ausgabe puffern
        ob_start();
        $this->console->run(['script.php', 'test:command']);
        ob_end_clean();
    }

    public function testRunWithListCommandName(): void
    {
        // Mock für CommandRegistry vorbereiten
        $this->registry->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $this->registry->expects($this->once())
            ->method('get')
            ->with('command:list')
            ->willReturn(TestConsoleCommand::class);

        // Ausgabe puffern
        ob_start();
        $this->console->run(['script.php', 'command:list']);
        ob_end_clean();
    }

    public function testRunWithNonExistentCommandShowsError(): void
    {
        // Mock für CommandRegistry vorbereiten
        $this->registry->expects($this->once())
            ->method('get')
            ->with('non:existent')
            ->willReturn(null);

        // Ausgabe puffern
        ob_start();
        $this->console->run(['script.php', 'non:existent']);
        $output = ob_get_clean();

        $this->assertStringContainsString('Command "non:existent" not found', $output);
    }

    public function testRunWithExceptionInCommandShowsError(): void
    {
        // Mock für CommandRegistry vorbereiten
        $this->registry->expects($this->once())
            ->method('get')
            ->with('error:command')
            ->willReturn(ErrorConsoleCommand::class);

        // Ausgabe puffern
        ob_start();
        $this->console->run(['script.php', 'error:command']);
        $output = ob_get_clean();

        $this->assertStringContainsString('Test error', $output);
    }
}
