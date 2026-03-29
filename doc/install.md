# Installing Spipu Core Bundle

[back](./README.md)

## Requirements

- PHP 8.1+ with `ext-sodium`
- Symfony 6.4+

## Installation

```bash
composer require spipu/core-bundle
```

## Configuration

### 1. Register the bundle

In `config/bundles.php`:

```php
return [
    // ...
    Spipu\CoreBundle\SpipuCoreBundle::class => ['all' => true],
];
```

### 2. Configure the encryptor

Generate a sodium key pair:

```bash
php bin/console spipu:encryptor:generate-key-pair
```

Add the output to your `.env.local`:

```env
APP_ENCRYPTOR_KEY_PAIR=<generated_value>
```

Then set the `spipu.core.encryptor.key_pair` DI parameter in your `config/services.yaml`:

```yaml
parameters:
    spipu.core.encryptor.key_pair: '%env(APP_ENCRYPTOR_KEY_PAIR)%'
```

> **Note:** There is no `spipu_core:` bundle extension config. The encryptor reads
> the plain DI parameter `spipu.core.encryptor.key_pair` directly.

### 3. Configure AsynchronousCommand (optional)

The bundle pre-wires `AsynchronousCommand` with `%kernel.project_dir%` and `%kernel.logs_dir%`.
Override only the optional arguments you need to change:

```yaml
# config/services.yaml
services:
    Spipu\CoreBundle\Service\AsynchronousCommand:
        arguments:
            $phpBin: 'php8.1'           # default: 'php'
            $logFilename: 'async.log'   # default: 'asynchronous-command.log'
```

### 4. Configure Mail (optional)

The `MailManager` service sends Twig-templated emails via the Symfony Mailer. Configure Mailer as usual:

```yaml
# config/packages/mailer.yaml
framework:
    mailer:
        dsn: '%env(MAILER_DSN)%'
```

## Available Commands

| Command | Description |
|---------|-------------|
| `spipu:encryptor:generate-key-pair` | Generate a new sodium key pair for the encryptor |
| `spipu:assets:install` | Publish bundle assets to `public/` |
| `spipu:fixtures:load` | Load fixtures into the database |
| `spipu:fixtures:remove` | Remove fixture data from the database |

[back](./README.md)
