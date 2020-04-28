<?php

namespace Rafter;

class Rafter
{
    const QUEUE_ROUTE = '/_rafter/queue/work';
    const SCHEDULE_ROUTE = '/_rafter/schedule/run';
    const COMMAND_ROUTE = '/_rafter/command/run';

    /**
     * Get the URL to the queue worker
     *
     * @return string
     */
    public static function queueWorkerUrl()
    {
        return $_ENV['RAFTER_WORKER_URL'] . static::QUEUE_ROUTE;
    }
}
