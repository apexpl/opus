<?php
declare(strict_types = 1);

namespace Apex\Opus\Helpers;

use Apex\Db\Interfaces\DbInterface;
use Apex\Opus\Builders\AbstractBuilder;

/**
 * Doctrine Helper
 */
class DoctrineHelper extends AbstractBuilder
{

    /**
     * Constructor
     */
    public function __construct(
        private DbInterface $db
     ) {

    }

    /**
     * Generate Doctrine annotations
     */
    public function generateAnnotations(string $table_name, string $entity_dir = ''):array
    {

        // Get columns
        $columns = $this->db->getColumnDetails($table_name);
        $foreign_keys = $this->db->getForeignKeys($table_name);

        // Go through props
        foreach ($columns as $alias => $vars) { 
            $attributes = [];

            // Check for primary column
            if ($vars['is_primary'] === true) { 
                $attributes[] = '@Id';
            }

            // Get column attribute
            $attributes[] = $this->generateColumnAttribute($vars);

            // Check for foreign key
            if (isset($foreign_keys[$alias]) && $class_name = $this->tableToClassName($foreign_keys[$alias]['table'], $entity_dir)) { 
                $type = $this->applyFilter($foreign_keys[$alias]['type'], 'camel');
                $attributes[] = '@' . $type . '(targetEntity="' . $class_name . '", cascade={"all"}, fetch="EAGER")';
            }

            // Auto increment
            if ($vars['is_auto_increment'] === true) { 
                $attributes[] = '@GeneratedValue';
            }

            // Add to property
            $annotations[$alias] = "/** " . implode(' ', $attributes) . ' */';
        }

        // Return
        return $annotations;
    }

    /**
     * Generate Doctrine column attribute
     */
    private function generateColumnAttribute(array $vars):string
    {

        // Get column type
        $col_type = $vars['type'];
        $type = match(true) { 
            $col_type == 'tinyint(1)' => 'boolean', 
            $col_type == 'boolean' => 'boolean', 
            (preg_match("/^(decimal|numeric)/", $col_type) ? true : false) => 'decimal',
            (preg_match("/^(datetime|timestamp)/", $col_type) ? true : false) => 'datetime', 
            (preg_match("/^date/", $col_type) ? true : false) => 'date', 
            (preg_match("/^time/", $col_type) ? true : false) => 'time', 
            (preg_match("/^(\w*?)int$/", $col_type) ? true : false) => 'integer', 
            default => 'string'
        };
        $attr = 'type="' . $type . '"';

        // Add length
        if ($type == 'string' && (int) $vars['length'] > 0) {
            $attr .= ', length="' . $vars['length'] . '"';
        } elseif ($type == 'decimal' && preg_match("/^(\d+?)\,(\d+)/", $vars['length'], $m)) { 
            $attr .= ', precision="' . $m[1] . '", scale="' . $m[2] . '"';
        }

        // Check for unique
        if ($vars['is_unique'] === true) { 
            $attr .= ', unique=true';
        }

        // Nullable
        if ($vars['allow_null'] === true) { 
            $attr .= ', nullable=true';
        }

        // Return
        return '@Column(' . $attr . ')';
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

            // Check
            $code = file_get_contents("$entity_dir/$file");
            if (!str_contains($code, '@Entity')) { 
                continue;
            } elseif (!preg_match("/\@Table\(name=\"(.+?)\"/", $code, $tm)) { 
                continue;
            } elseif ($tm[1] != $table_name) { 
                continue;
            }

            // Set class name
            $class_name = preg_replace("/\.php$/", "", $file);
            break;
        }

        // Return
        return $class_name;
    }

}


