<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Cli;

use Apex\App\Cli\Cli;

/**
 * CLI Command -- ./apex ~package.lower~ ~alias.lower~
 */
class ~alias.title~
{

    /**
     * Process
     */
    public function process(Cli $cli):void
    {

        // Get CLI arguments
        list($args, $opt) = $cli->getArgs([]);

    }

    /**
     * Help
     */
    public function help(Cli $cli):void
    {

        $cli->sendHelp(
            '~alias.phrase~',       // Title
            '~package.lower~ ~alias.lower~',       // Usage
            'Description of the command here', 
            [
                'param1' => 'Description of first required paramter.'
            ], [
                '--flag' => 'Description of an optional flag.'
            ], [
                '~package.lower~ ~alias.lower~ example1 example2 --name example'
            ]
        );

    }

}

