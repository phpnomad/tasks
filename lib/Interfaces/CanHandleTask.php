<?php

namespace PHPNomad\Tasks\Interfaces;

/**
 * @template T of Task
 */
interface CanHandleTask
{
    /**
     * Runs the task logic.
     *
     * @param T $task The resolved task instance.
     */
    public function handle(Task $task): void;
}
