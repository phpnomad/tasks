<?php

namespace PHPNomad\Tasks\Interfaces;


use PHPNomad\Tasks\Exceptions\TaskException;

interface IdempotencyStore
{
    /**
     * Acquire a short lock to prevent concurrent double-run.
     * Returns true if you acquired the lock.
     * @throws TaskException
     */
    public function acquire(IsIdempotent $task, int $lockTtlSeconds): bool;

    /**
     * Mark as done (suppress replays).
     * @throws TaskException
     * */
    public function markDone(IsIdempotent $task, int $doneTtlSeconds): void;

    /**
     * Check if already done.
     * @throws TaskException
     */
    public function isDone(IsIdempotent $task): bool;

    /**
     * Release lock (on failure).
     * @throws TaskException
     */
    public function release(IsIdempotent $task): void;
}
