# phpnomad/tasks

[![Latest Version](https://img.shields.io/packagist/v/phpnomad/tasks.svg)](https://packagist.org/packages/phpnomad/tasks)
[![Total Downloads](https://img.shields.io/packagist/dt/phpnomad/tasks.svg)](https://packagist.org/packages/phpnomad/tasks)
[![PHP Version](https://img.shields.io/packagist/php-v/phpnomad/tasks.svg)](https://packagist.org/packages/phpnomad/tasks)
[![License](https://img.shields.io/packagist/l/phpnomad/tasks.svg)](https://packagist.org/packages/phpnomad/tasks)

`phpnomad/tasks` provides interfaces for declaring background and scheduled tasks in PHP applications. A task is a serializable value object with a stable id and a `toPayload()` / `fromPayload()` round trip, which means you can dispatch one now and let a worker, a queue, or the platform's scheduler reconstruct it and run it later.

The dispatching strategy is pluggable. A concrete implementation decides whether a task runs immediately, lands on a Redis queue for a background worker to pick up, or gets registered with the host platform's scheduler (like WordPress cron). Your task classes and their handlers stay the same either way. This package is interfaces only and has zero runtime dependencies. For a working queue, also install a concrete implementation. The recommended one is [`phpnomad/redis-task-integration`](https://packagist.org/packages/phpnomad/redis-task-integration), a Redis-backed worker queue. The package is used in production by [Siren](https://sirenaffiliates.com) and other PHPNomad applications.

## Installation

```bash
composer require phpnomad/tasks
```

For a working queue, also install the Redis integration:

```bash
composer require phpnomad/redis-task-integration
```

## Quick Start

Define a task. It needs a stable identifier and a payload that can survive a trip through JSON.

```php
use PHPNomad\Tasks\Exceptions\TaskCreateFailedException;
use PHPNomad\Tasks\Interfaces\Task;

class SendWelcomeEmail implements Task
{
    public function __construct(
        public readonly int $userId
    ) {}

    public static function getId(): string
    {
        return 'user.send_welcome_email';
    }

    public function toPayload(): array
    {
        return ['userId' => $this->userId];
    }

    /**
     * @throws TaskCreateFailedException
     */
    public static function fromPayload(array $payload): static
    {
        return new static($payload['userId']);
    }
}
```

Write a handler that runs the task. The `@implements` tag lets your static analyzer narrow `$task` to the concrete class.

```php
use PHPNomad\Tasks\Interfaces\CanHandleTask;
use PHPNomad\Tasks\Interfaces\Task;

/**
 * @implements CanHandleTask<SendWelcomeEmail>
 */
class SendWelcomeEmailHandler implements CanHandleTask
{
    public function __construct(
        protected UserDatastore $users,
        protected EmailService $email
    ) {}

    public function handle(Task $task): void
    {
        $user = $this->users->find($task->userId);

        $this->email->send($user->getEmail(), 'Welcome!');
    }
}
```

Register the task-to-handler mapping on an initializer so the bootstrapper can wire it up.

```php
use PHPNomad\Tasks\Interfaces\HasTaskHandlers;

class MyAppInitializer implements HasTaskHandlers
{
    public function getTaskHandlers(): array
    {
        return [
            SendWelcomeEmail::class => SendWelcomeEmailHandler::class,
        ];
    }
}
```

Dispatch the task from wherever the work is triggered. Inject `TaskStrategy` and call `dispatch()`.

```php
use PHPNomad\Tasks\Interfaces\TaskStrategy;

class UserService
{
    public function __construct(
        protected UserDatastore $users,
        protected TaskStrategy $tasks
    ) {}

    public function createUser(string $email): User
    {
        $user = $this->users->create(['email' => $email]);

        $this->tasks->dispatch(new SendWelcomeEmail($user->getId()));

        return $user;
    }
}
```

The strategy chosen at bootstrap decides whether the task runs immediately, lands on a queue for a worker to pick up, or gets registered with the host scheduler. `SendWelcomeEmail` and `SendWelcomeEmailHandler` do not care which.

## Key Concepts

- `Task`: serializable value object identified by a static `getId()` string with a round-trip payload
- `TaskStrategy`: the dispatcher interface responsible for running, enqueueing, or scheduling a task
- `CanHandleTask`: the contract a handler implements to process a task
- `HasTaskHandlers`: initializer interface modules implement to register their task-to-handler mappings
- `IsIdempotent`: optional marker for tasks that must not double-run, with a deterministic key and a TTL
- `IdempotencyStore`: contract for the backing store that suppresses replays and concurrent double-runs

## Documentation

Full documentation for PHPNomad, including the bootstrapping guide and the broader framework reference, lives at [phpnomad.com](https://phpnomad.com).

## License

MIT. See [LICENSE.txt](LICENSE.txt).
