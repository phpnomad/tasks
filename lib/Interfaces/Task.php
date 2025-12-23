<?php

namespace PHPNomad\Tasks\Interfaces;

use PHPNomad\Tasks\Exceptions\TaskCreateFailedException;

interface Task
{
    public static function getId(): string;

    /**
     * Convert the task into a JSON-safe payload that can be reconstructed later using fromPayload().
     *
     * @return array
     */
    public function toPayload(): array;

    /**
     * @param array $payload
     * @return static
     * @throws TaskCreateFailedException
     */
    public static function fromPayload(array $payload): static;
}
