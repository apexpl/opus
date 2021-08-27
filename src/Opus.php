<?php
declare(strict_types = 1);

namespace Apex\Opus;

use Apex\Opus\Builders\{Builder, Remover, ModelBuilder, ClassBuilder};
use Apex\Db\Interfaces\DbInterface;
use Apex\Container\Di;
use Symfony\Component\String\UnicodeString;

/**
 * Central class for Opus, a code generation utility for CRUD, 
 * models, templates, and more.
 */
class Opus
{

    // Properties
    public array $inflection_opt = [
        'single' => '', 
        'plural' => ''
    ];

    /**
     * Constructor
     */
    public function __construct(
        public DbInterface $db
    ) { 
        Di::set(__CLASS__, $this);
    }

    /**
     * Build
     */
    public function build(string $comp_type, string $rootdir, array $vars):array
    {
        $builder = Di::make(Builder::class);
        return $builder->build($comp_type, $rootdir, $vars);
    }

    /**
     * Remove
     */
    public function remove(string $comp_type, string $rootdir, array $vars):array
    {
        $remover = Di::make(Remover::class);
        return $remover->remove($comp_type, $rootdir, $vars);
    }

    /**
     * Build model
     */
    public function buildModel(string $filename, string $rootdir, string $dbtable, string $type = 'php8', bool $with_magic = false):array
    {
        $builder = Di::make(ModelBuilder::class);
        return $builder->build($filename, $rootdir, $dbtable, $type, $with_magic);
    }

    /**
     * Build class
     */
    public function buildClass(string $type, string $filename, string $item_class, string $rootdir):string
    {
        $builder = Di::make(ClassBuilder::class);
        return $builder->build($type, $filename, $item_class, $rootdir);
    } 


}


