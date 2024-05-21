<?php
namespace PHPNomad\Tasks;

interface CanScheduleTasks
{
    /**
     * @param string $interval
     * @param string $identifier
     * @param callable $callback
     * @return void
     */
    public function runEvery(string $interval, string $identifier, callable $callback): void;
}