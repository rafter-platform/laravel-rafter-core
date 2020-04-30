<?php

namespace Rafter;

use Symfony\Component\Console\Output\Output;

/**
 * Sends a console output to a buffer, which is flushed and streamed as a HTTP response.
 */
class StreamingOutput extends Output
{
    /**
     * {@inheritdoc}
     */
    protected function doWrite(string $message, bool $newline)
    {
        echo $message;

        if ($newline) {
            echo PHP_EOL;
        }

        ob_flush();
        flush();
    }
}
