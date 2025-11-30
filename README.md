<div style="text-align: center;" align="center">

![Logo](https://nixphp.github.io/docs/assets/nixphp-logo-small-square.png)

[![NixPHP CLI Plugin](https://github.com/nixphp/cli/actions/workflows/php.yml/badge.svg)](https://github.com/nixphp/cli/actions/workflows/php.yml)

</div>

[← Back to NixPHP](https://github.com/nixphp/framework)

---

# nixphp/cli

> **A minimal, developer-friendly command-line interface for your NixPHP application.**

This plugin gives you a clean CLI system with colored output, argument parsing, and auto-discovered commands. All without external dependencies.

> 🧩 Part of the official NixPHP plugin collection. Install it if you want powerful CLI tools for development, deployment, and automation.

---

## 📦 Features

✅ Adds `vendor/bin/nix` as your app’s command-line entry point
✅ Auto-discovers commands in `app/Commands/`
✅ Supports arguments, options, and interactive input
✅ Prints colored output for better UX
✅ Fully extensible – build your own tools and workflows

---

## 📥 Installation

```bash
composer require nixphp/cli
```

This will create `vendor/bin/nix`, your CLI gateway.

---

## 🚀 Usage

### 🔍 Run a command

```bash
vendor/bin/nix your:command
```

Commands are discovered automatically if placed in your app’s `app/Commands/` directory.

```bash
vendor/bin/nix
```

If you call the helper without arguments, it prints all available CLI commands.

---

### 🛠️ Create a custom command

To create your own CLI command, add a class in the `app/Commands/` folder:

```php
namespace App\Commands;

use NixPHP\CLI\Core\AbstractCommand;
use NixPHP\CLI\Core\Input;
use NixPHP\CLI\Core\Output;

class HelloCommand extends AbstractCommand
{
    public const NAME = 'hello:say';

    protected function configure(): void
    {
        $this->setTitle('Say Hello');
        $this->addArgument('name');
    }

    public function run(Input $input, Output $output): int
    {
        $name = $input->getArgument('name');
        $output->writeLine("Hello, {$name}!", 'ok');
        return static::SUCCESS;
    }
}
```

No registration needed — as long as the class resides in `app/Commands/`, it will be picked up automatically.

Then run:

```bash
vendor/bin/nix hello:say John
```

---

## 🎨 Colored output

Use `$output->writeLine()` to print messages with color support:

| Type         | Appearance              |
| ------------ | ----------------------- |
| `'ok'`       | ✅ Green                 |
| `'error'`    | ❌ Red                   |
| `'warning'`  | ⚠️ Yellow               |
| `'title'`    | 💡 Light green on black |
| `'headline'` | 📢 Light blue on black  |

You can also draw horizontal lines:

```php
$output->drawStroke(30);
```

---

## 🧪 Interactive input

You can prompt the user:

```php
$name = $input->ask('What is your name?');
```

---

## 📁 File structure

A typical CLI setup might look like this:

```text
app/
└── Commands/
    └── HelloCommand.php

vendor/
└── bin/
    └── nix

bootstrap.php
```

---

## ✅ Requirements

* `nixphp/framework` >= 0.1.0
* PHP >= 8.3

---

## 📄 License

MIT License.