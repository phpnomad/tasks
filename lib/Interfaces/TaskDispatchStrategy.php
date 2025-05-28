<?php

namespace PHPNomad\Tasks\Interfaces;


/**
 * A strategy that defines how to dispatch tasks for execution.
 * Could enqueue them (Redis), schedule them (WordPress), or run immediately.
 */
interface TaskDispatchStrategy
{
    /**
     * Dispatches a task to be handled asynchronously or immediately.
     *
     * @param object $task A task value object
     */
    public function dispatch(object $task): void;
}
