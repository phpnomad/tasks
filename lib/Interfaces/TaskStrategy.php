<?php

namespace PHPNomad\Tasks\Interfaces;


/**
 * A strategy that defines how to dispatch tasks for execution.
 * Could enqueue them (Redis), schedule them (WordPress), or run immediately.
 */
interface TaskStrategy
{
    /**
     * Dispatches a task to be handled asynchronously or immediately.
     *
     * @param object $task A task value object
     * @throws @throws TaskDispatchFailedException
     */
    public function dispatch(object $task): void;

    /**
     * Attaches a task to the specified handler.
     *
     * @param string $taskClass
     * @param callable $handler
     * @return void
     */
    public function attach(string $taskClass, callable $handler): void;
}
