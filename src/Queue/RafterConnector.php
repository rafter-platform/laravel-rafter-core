<?php

namespace Rafter\Queue;

use Google\Cloud\Tasks\V2\CloudTasksClient;
use Illuminate\Queue\Connectors\ConnectorInterface;

class RafterConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new RafterQueue(
            new CloudTasksClient(),
            $config['queue'],
            $config['project_id'],
            $config['region']
        );
    }
}
