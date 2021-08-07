<?php
declare(strict_types = 1);

namespace Apex\Opus;

use Apex\Svc\Container;

/**
 * Components
 */
class Components
{

    /**
     * Constructor
     */
    public function __construct(
        private Container $cntr
    ) { 

    }

    /**
     * Load
     */
    public function load(string $type, string $alias, array $params = []):?object
    {

        // Check alias
        if (!preg_match("/^(.+?)\.(.+)$/", $alias, $match)) { 
            return null;
        }

        // Convert case as needed
        $package = fcase($match[1], 'title');
        $alias = fcase($match[2], 'title');

        // Check for class
        $class_name = "\\App\\$package\\Opus\\" . ucwords($type) . "s\\$alias";
        if (!class_exists($class_name)) { 
            return null;
        }

        // Instantiate and return
        $obj = $this->cntr->make($class_name, $params);
        return $obj;
    }

}

