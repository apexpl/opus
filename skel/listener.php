<?php
declare(strict_types = 1);

namespace App\~package.title~\Listeners;

use Apex\App\Interfaces\ListenerInterface;
use Apex\Cluster\Interfaces\{MessageRequestInterface, FeHandlerInterface};

/**
 * Listener - ~alias~
 */
class ~alias.title~ implements ListenerInterface
{

    /**
     * Routing key for which the listener accepts messages for.
     */
    public static string $routing_key = '~routing_key~';


    /**
     * Example method
     */
    public function example(MessageRequestInterface $msg, FeHandlerInterface $handler)
    {

    }

}

