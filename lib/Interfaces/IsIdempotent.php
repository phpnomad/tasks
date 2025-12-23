<?php

namespace PHPNomad\Tasks\Interfaces;


interface IsIdempotent
{
    /**
     * A stable key that represents the same logical work.
     * Must be deterministic across retries/duplicates.
     */
    public function idempotencyKey(): string;

    /**
     * How long to suppress replays (seconds).
     * Choose based on the business side effect.
     */
    public function idempotencyTtlSeconds(): int;
}