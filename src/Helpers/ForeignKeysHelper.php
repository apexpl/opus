<?php
declare(strict_types = 1);

namespace Apex\Opus\Helpers;

use Apex\App\Cli\Cli;
use Apex\App\Cli\Helpers\OpusHelper;
use Apex\Container\Di;
use Apex\App\Base\Model\BaseModel;
use Apex\Opus\Builders\{AbstractBuilder, ModelBuilder};
use Apex\Db\Interfaces\DbInterface;

/**
 * Foreign Keys
 */
class ForeignKeysHelper extends AbstractBuilder
{

    // Properties
    private string $use_declarations = '';
    public array $queue = [];

    /**
     * Constructor
     */
    public function __construct(
        private DbInterface $db,
        private OpusHelper $opus_helper,
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
        $this->queue = [];

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
     * Apply referenced foreign keys
     */
    private function applyReferencedForeignKeys(string $code, string $dbtable, string $dirname, bool $with_magic, array $props):string
    {

        // Check for code tag
        if (!preg_match("/<relations_many>(.*?)<\/relations_many>/si", $code, $match)) { 
            return $code;
        }

        // Get keys
        $keys = $this->db->getReferencedForeignKeys($dbtable);

        // Go through keys
        $final_code = '';
        foreach ($keys as $foreign_key => $vars) { 


        }

        // Return
        return str_replace($match[0], $final_code, $code);
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

        // Initialize
        $class_name = null;
        $files = scandir($entity_dir);

        // Go through files
        foreach ($files as $file) { 

            // Skip, if not .php file
            if (!str_ends_with($file, '.php')) { 
                continue;
            }
            $tmp_file = str_replace(SITE_PATH, '', "$entity_dir/$file");

            // Load object
            $class_name = $this->opus_helper->pathToNamespace($tmp_file);
            $obj = new \ReflectionClass($class_name);

            // Skip, if not model
            if ($obj->getExtensionName() != BaseModel::class) { 
                continue;
            } elseif (!$prop = $obj->getProperty('dbtable')) { 
                continue;
            } elseif ($prop->getValue() != $table_name) { 
                continue;
            }

            $class_name = $obj->getNamespaceName();
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

        // Confirm creation
        if (!$this->cli->getConfirm("A foreign key relationship with the table '$table_name' was found, but no model class was found.  Would you like to generate one?")) { 
            return null;
        }

        // Get class name
        $filename = $this->applyFilter($table_name, 'single');
        $filename = $this->applyFilter($filename, 'title') . '.php';
        $filename = str_replace(SITE_PATH, '', "$entity_dir/$filename");

        // Build the model
        //$builder = Di::make(ModelBuilder::class);
        //$builder->build($filename, SITE_PATH, $table_name, 'php8', $with_magic);

        // Add to queue
        $this->queue[$table_name] = $filename;

        // Get class name, and return
        $class_name = $this->opus_helper->pathToNamespace($filename);
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
            '~get_phrase~' => $this->applyFilter($alias, 'phrase'),
            '~short_name~' => $short_name,
            '~name~' => $alias,
            '~foreign_key~' => $foreign_key
        ];
        $name = rtrim(rtrim($alias, 'id'), '_');

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


