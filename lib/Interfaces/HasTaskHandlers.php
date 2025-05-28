<?php

namespace PHPNomad\Tasks\Interfaces;

/**
 * An initializer interface for registering task handlers during bootstrap.
 */
interface HasTaskHandlers
{
    /**
     * Returns an array of handler class fully qualified names or instances to register.
     *
     * @return array<CanHandleTask>
     */
    public function getTaskHandlers(): array;
}
