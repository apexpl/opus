<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

use Apex\Opus\Opus;
use Apex\App\Cli\Cli;
use Apex\App\Sys\Utils\Io;

/**
 *Remover
 */
class Remover extends AbstractBuilder
{

    /**
     * Constructor
     */
    public function __construct(
        Opus $opus, 
        Cli $cli, 
        private Io $io
    ) { 
        $this->opus = $opus;
        $this->cli = $cli;
    }

    /**
     * Remove
     */
    public function remove(string $comp_type, string $rootdir, array $vars):array
    {

        // Get component
        $def = $this->getComponentDefinition($comp_type);

        // Remove directories
        $dirs = $def['dirs'] ?? [];
        $dirs = $this->removeDirs($dirs, $rootdir, $vars);

        // Remove files
        $files = $def['files'] ?? [];
        $files = $this->removeFiles($files, $rootdir, $vars);

        // Return
        return [$dirs, $files];
    }

    /**
     * Remove dirs
     */
    private function removeDirs(array $dirs, string $rootdir, array $vars):array
    {

        // Go through dirs
        $deleted = [];
        foreach ($dirs as $dir) { 

            // Convert
            $dir = $this->convertString($dir, $vars);
            $dir = ltrim($dir, '/');
            $deleted[] = $dir;

            // Remove dir
            $this->io->removeDir("$rootdir/$dir");
        }

        // Return
        return $deleted;
    }

    /**
     * Remove files
     */
    private function removeFiles(array $files, string $rootdir, $vars):array
    {

        // Go through files
        $deleted = [];
        foreach ($files as $file) { 

            // Convert
            $file = $this->convertString($file, $vars);
            $file = ltrim($file, '/');
        $deleted[] = $file;

            // Remove dir
            $this->io->removeFile("$rootdir/$file", true);
        }

        // Return
        return $deleted;

    }

}


