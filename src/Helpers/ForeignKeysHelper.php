<?php
declare(strict_types = 1);

namespace Apex\Opus\Helpers;

use Apex\App\Cli\Cli;
use Apex\App\Cli\Helpers\OpusHelper;
use Apex\Container\Di;
use Apex\App\Base\Model\BaseModel;
use Apex\Opus\Builders\{AbstractBuilder, ModelBuilder};
use Apex\Db\Interfaces\DbInterface;
use redis;

/**
 * Foreign Keys
 */
class ForeignKeysHelper extends AbstractBuilder
{

    // Properties
    private string $use_declarations = '';
    public static array $queue = [];
    public static array $tbl_created = [];
    public static array $tbl_skipped = [];

    /**
     * Constructor
     */
    public function __construct(
        private DbInterface $db,
        private OpusHelper $opus_helper,
        private redis $redis,
        Cli $cli
    ) { 
        $this->cli = $cli;
    }

    /**
     * Apply foreign keys
     */
    public function apply(string $code, string $dbtable, string $dirname, bool $with_magic, array $props):string
    {

        // Initialize
        $this->use_declarations = '';
        ForeignKeysHelper::$queue = [];

        // Get keys
        $keys = $this->getForeignKeys($dbtable, $dirname, $with_magic, $props);
        $referenced_keys = $this->getReferencedForeignKeys($dbtable, $dirname, $with_magic, $props);

        // Apply relationships
        $code = $this->applyRelationships($code, $keys, $referenced_keys);
        $code = str_replace('~use_declarations~', $this->use_declarations, $code);

        // Return
        return $code;
    }

    /**
     * Get foreign keys
     */
    private function getForeignKeys(string $dbtable, string $dirname, bool $with_magic, array $props):array
    {

        // Get foreign keys
        $keys = $this->db->getForeignKeys($dbtable);

        // Go though keys
        foreach ($keys as $column => $vars) { 

            // Check for class name
            if (!$class_name = $this->tableToClassName($vars['table'], $dirname)) { 

                // Check for creation of new class
                if (!$class_name = $this->generateClass($vars['table'], $dirname, $with_magic)) {
                    unset($keys[$column]);
                    continue;
                }
            }

            // Add to results
            $keys[$column]['class_name'] = $class_name;
        }

        // Return
        return $keys;
    }

    /**
     * Get referenced foreign keys
     */
    private function getReferencedForeignKeys(string $dbtable, string $dirname, bool $with_magic, array $props):array
    {

        // Get foreign keys
        $keys = $this->db->getReferencedForeignKeys($dbtable);

        // Go though keys
        foreach ($keys as $foreign_key => $vars) { 

            // Check for class name
            if (!$class_name = $this->tableToClassName($vars['ref_table'], $dirname)) { 

                // Check for creation of new class
                if (!$class_name = $this->generateClass($vars['ref_table'], $dirname, $with_magic)) { 
                    unset($keys[$foreign_key]);
                    continue;
                }
            }

            // Add to results
            $keys[$foreign_key]['class_name'] = $class_name;
        }

        // Return
        return $keys;
    }

    /**
     * Table to class name
     */
    private function tableToClassName(string $table_name, string $entity_dir):?string
    {

        // Check skipped and created
        if ($table_name == 'armor_users' || in_array($table_name, ForeignKeysHelper::$tbl_skipped)) { 
            return null;
        } elseif (isset(ForeignKeysHelper::$tbl_created[$table_name])) { 
            return ForeignKeysHelper::$tbl_created[$table_name];
        }

        // Initialize
        $class_name = null;
        $classes = $this->redis->smembers("config:interfaces:Apex\\App\\Interfaces\\BaseModelInterface");

        // Go through classes
        foreach ($classes as $class) {

            // Load object
            if (!class_exists($class)) {
                continue;
            }
            $obj = new \ReflectionClass($class);

            // Check class
            if (!$obj->hasProperty('dbtable')) { 
                continue;
            } elseif ($obj->getProperty('dbtable')->getDefaultValue() != $table_name) {
                continue;
            }

            $class_name = $class;
            break;
        }

        // Return
        return $class_name;
    }

    /**
     * Generate class
     */
    private function generateClass(string $table_name, string $entity_dir, bool $with_magic):?string
    {

        // Check skipped and created
        if ($table_name == 'armor_users' || in_array($table_name, ForeignKeysHelper::$tbl_skipped)) { 
            return null;
        } elseif (isset(ForeignKeysHelper::$tbl_created[$table_name])) { 
            return ForeignKeysHelper::$tbl_created[$table_name];
        }

        // Confirm creation
        if (!$this->cli->getConfirm("A foreign key relationship with the table '$table_name' was found, but no model class was found.  Would you like to generate one?", 'y')) { 
            ForeignKeysHelper::$tbl_skipped[] = $table_name;
            return null;
        }

        // Get name
        $name = preg_replace("/^(.+?)_/", "", $table_name);
        $filename = $this->applyFilter($name, 'single');
        $filename = $this->applyFilter($filename, 'title') . '.php';
        $filename = str_replace(SITE_PATH . '/src/', '', "$entity_dir/$filename");

        // Confirm filename
        $this->cli->send("Please enter the filepath relative to the /src/ directory where you would like the new model class for '$table_name' saved.  Leave blank and press enter to accept the default value provided.\r\n\r\n");
        $filename = $this->cli->getInput("Filepath of Model [$filename]: ", $filename);
        $filename = $this->opus_helper->parseFilename($filename);

        // Add to queue
        ForeignKeysHelper::$queue[$table_name] = $filename;

        // Get class name, and return
        $class_name = $this->opus_helper->pathToNamespace($filename);
        ForeignKeysHelper::$tbl_created[$table_name] = $class_name;
        return $class_name;
    }

    /**
     * Apply foreign keys
     */
    private function applyRelationships(string $code, array $keys, array $referenced_keys):string
    {

        // Check for code tags
        preg_match("/<relations_one>(.*?)<\/relations_one>/si", $code, $one_match);
        preg_match("/<relations_many>(.*?)<\/relations_many>/si", $code, $many_match);

        // Go through keys
        list($one_code, $many_code) = ['', ''];
        foreach ($keys as $alias => $vars) {

            if (str_ends_with($vars['type'], 'many')) {
                $foreign_key = $vars['table'] . '.' . $vars['column'];
                $many_code .= $this->generateCode($many_match[1], $vars['table'], $vars['class_name'], $vars['type'], $foreign_key);
            } else {
                $one_code .= $this->generateCode($one_match[1], $alias, $vars['class_name'], $vars['type']);
            }
        }

        // Go through refereced keys
        foreach ($referenced_keys as $foreign_key => $vars) { 

            if (str_ends_with($vars['type'], 'many')) {
                $many_code .= $this->generateCode($many_match[1], $vars['ref_table'], $vars['class_name'], $vars['type'], $foreign_key);
            } else {
                $one_code .= $this->generateCode($one_match[1], $vars['ref_column'], $vars['class_name'], $vars['type']);
            }
        }

        // Replace code
        $code = str_replace($one_match[0], $one_code, $code);
        $code = str_replace($many_match[0], $many_code, $code);

        // Return
        return $code;
    } 

    /**
     * Generate to one code
     */
    private function generateCode(string $tmp_code, string $alias, string $class_name, string $type, string $foreign_key = ''):string
    {

        // Get short class name
        $parts = explode("\\", $class_name);
        $short_name = array_pop($parts);

        // Initialize replace
        $replace = [
            '~get_phrase~' => $this->applyFilter($short_name, 'phrase'),
            '~short_name~' => $short_name,
            '~name~' => $alias,
            '~foreign_key~' => $foreign_key
        ];
        $name = rtrim(rtrim($alias, 'id'), '_');
        //$name = $short_name;

        // Set variables based on type
        if (str_ends_with($type, 'many')) { 
            $name = $this->applyFilter($name, 'plural');
        } else { 
            $name = $this->applyFilter($name, 'single');
        }
        $replace['~method_name~'] = $this->applyFilter('get_' . $name, 'camel');
        $replace['~method_name~'] = preg_replace("/ss$/", 's', $replace['~method_name~']);

        // Add to use declarations
        $this->use_declarations .= "\nuse " . $class_name . ";";
        if (str_ends_with($type, 'many') && !str_contains($this->use_declarations, 'ModelIterator')) { 
            $this->use_declarations .= "\nuse Apex\\App\\Base\\Model\\ModelIterator;";
        }

        // Return
        return strtr($tmp_code, $replace);
    }

}


