<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Cli;

use Apex\App\Cli\{Cli, CliHelpScreen};
use Apex\App\Interfaces\Opus\CliCommandInterface;

/**
 * CLI Command -- ./apex ~package.lower~ ~alias.lower~
 */
class ~alias.title~ implements CliCommandInterface
{

    /**
     * Process
     */
    public function process(Cli $cli, array $args):void
    {

        // Get CLI arguments
        $opt = $cli->getArgs(['myflag1', 'myflag2']);

    }

    /**
     * Help
     */
    public function help(Cli $cli):CliHelpScreen
    {

        $help = new CliHelpScreen(
            title: '~alias.phrase~',
            usage: '~package.lower~ ~alias.lower~',
            description: 'Description of the command here'
        );

        // Add parameters
        $help->addParam('param1', 'Description of parameter.');

        // Add optional flags
        $help->addFlag('--some-flag', 'Description of flag.');

        // Add example
        $help->addExample('./apex ~package.lower~ ~alias.lower~ <param1> [--flat1=...]');

        // Return
        return $help;
    }

}


