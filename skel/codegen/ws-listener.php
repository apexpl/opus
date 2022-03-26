<?php
declare(strict_types=1);

namespace ~namespace~;

use Amp\Websocket\Client\Connection;
use Amp\Websocket\Message;
use function Amp\delay;
use function Amp\Websocket\Client\connect;

/**
 * Websocket listener
 */
class ~class_name~ {

    /**
     * Websocket url to connect to
     */
    private string $websocket_url = 'ws://localhost:1773';

    /**
     * Run listener
     */
    public function listen()
    {

        Amp\Loop::run(function () {

            /** @var Client\Connection $connection */
            $connection = yield connect($this->websocket_url);

            // Say hello
            yield $connection->send("Hello!");

            // Listen for messages
            $i = 0;
            while ($message = yield $connection->receive()) {
                /** @var Message $message */
                $payload = yield $message->buffer();
                $this->handle($payload);

                // Close connection, if goodbye
                if ($payload === "Goodbye!") {
                    $connection->close();
                    break;
                }

                // Pause the coroutine for 1 second.
                yield delay(1000);

                if ($i < 3) {
                    yield $connection->send("Ping: " . ++$i);
                } else {
                    yield $connection->send("Goodbye!");
                }
            }
        });

    }

    /**
     * Handle a received message
     */
    public function handle(string $payload):void
    {
        printf("Received: %s\n", $payload);
    }


}


