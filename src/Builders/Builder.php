<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

use Apex\Opus\Opus;
use Apex\Opus\Builders\FormBuilder;
use Apex\App\Cli\Cli;
use Symfony\Component\Process\Process;

/**
 * Component builder
 */
class Builder extends AbstractBuilder
{

    /**
     * Constructor
     */
    public function __construct(
        Opus $opus, 
        Cli $cli,
        private FormBuilder $form_builder
    ) { 
        $this->opus = $opus;
        $this->cli = $cli;
    }

    /**
     * Build
     */
    public function build(string $comp_type, string $rootdir, array $vars):array
    {

        // Get form code, if needed
        if ($comp_type == 'form') {
            $dbtable = $vars['dbtable'] ?? '';
            $vars['form_code'] = $this->form_builder->getCode($dbtable);
        }

        // Get component
        $def = $this->getComponentDefinition($comp_type);

        // Execute commands
        $cmds = $def['commands'] ?? [];
        $this->execCommands($cmds, $rootdir, $vars);

    // System copy
    $sys_copy = $def['system_copy'] ?? [];
    $this->systemCopy($sys_copy, $rootdir, $vars);

        // Create directories
        $dirs = $def['dirs'] ?? [];
        $dirs = $this->createDirs($dirs, $rootdir, $vars);

        // Create files
        $files = $def['files'] ?? [];
        $files = $this->createFiles($files, $rootdir, $vars);

        // Return
        return [$dirs, $files];
    }

    /**
     * Execute commands
     */
    private function execCommands(array $cmds, string $rootdir, array $vars):void
    {

        // Go through commands
        foreach ($cmds as $cmd) { 

            // Get comment
            if (preg_match("/\"(.+?)\"/", $cmd, $match)) { 
                $vars['comment'] = $match[1];
                $cmd = str_replace($match[0], '~comment~', $cmd);
            }

            // Convert string
            $cmd = $this->convertString($cmd, $vars);
            $args = explode(' ', $cmd);

            // Run process
            $process = new Process($args);
            $process->setWorkingDirectory($rootdir);
            $process->run();
        }
    }

    /**
     * System copy
     */
    private function systemCopy(array $sys_copy, string $rootdir, array $vars):void
    {

        // Copy over
        foreach ($sys_copy as $source => $dest) { 

        // Format source
            $source_dir = realpath(__DIR__ . '/../../skel/' . $source);

            // Format dest
            $dest = ltrim($dest, '/');
            $dest = $this->convertString($dest, $vars);

            // copy
            system("cp -R $source_dir $rootdir/$dest");
        }

    }

    /**
     * Create direcories
     */
    private function createDirs(array $dirs, string $rootdir, array $vars):array
    {

        // Go through dirs
        $created = [];
        foreach ($dirs as $dir) { 

            // Convert
            $dir = $this->convertString($dir, $vars);
            $dir = ltrim($dir, '/');
            $created[] = $dir;

            // Skip, if needed
            if (is_dir("$rootdir/$dir")) { 
                continue;
            }

            // Create dir
            mkdir("$rootdir/$dir", 0755, true);
        }

        // Return
        return $created;
    }

    /**
     * Create files
     */
    private function createFiles(array $files, string $rootdir, array $vars):array
    {

        // Copy files
        $created = [];
        foreach ($files as $source => $dest) { 

            // Get code
            $source = ltrim($source, '/');
            $code = file_get_contents(__DIR__ . '/../../skel/' . $source);
            $code = $this->convertString($code, $vars);

            // Save file
            $dest = ltrim($dest, '/');
            $dest = $this->convertString($dest, $vars);
            $created[] = $dest;

            // Create directory, if needed
            if (!is_dir(dirname("$rootdir/$dest"))) { 
                mkdir(dirname("$rootdir/$dest"), 0755, true);
            }

            // Save file
            file_put_contents("$rootdir/$dest", $code);
        }

        // Return
        return $created;
    }

}

