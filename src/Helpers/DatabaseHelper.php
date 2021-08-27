<?php
declare(strict_types = 1);

namespace Apex\Opus\Helpers;

use Apex\Db\Interfaces\DbInterface;
use Apex\Opus\Helpers\{DoctrineHelper, ForeignKeysHelper};

/**
 * Database handler class.
 */
class DatabaseHelper
{

    /**
     * Constructor
     */
    public function __construct(
        private DbInterface $db, 
        private DoctrineHelper $doctrine_helper
    ) { 

    }

    /**
     * Database table to PHP data types.
     */
    public function tableToProperties(string $table_name, string $entity_dir = ''):array
    {

        // Get column
        $columns = $this->db->getColumnDetails($table_name);
        $annotations = $this->doctrine_helper->generateAnnotations($table_name, $entity_dir);

        // Go through columns
        $props = [];
        $has_optional = false;
        foreach ($columns as $alias => $vars) { 

            // Get php data type
            $col_type = $vars['type'];
            $type = match(true) { 
                $col_type == 'tinyint(1)' => 'bool', 
                $col_type == 'boolean' => 'bool', 
                (preg_match("/^(decimal|numeric)/", $col_type) ? true : false) => 'float',
                (preg_match("/^(datetime|timestamp)/", $col_type) ? true : false) => 'DateTime', 
                (preg_match("/^(\w*?)int$/", $col_type) ? true : false) => 'int', 
                default => 'string'
            };

            // Get default
            $def = $vars['default'] ?? '';
            $def = match(true) { 
                ($type == 'bool' && in_array($def, ['1','t','true'])) ? true : false => 'true', 
                ($type == 'bool' && in_array($def, ['0','f','false'])) ? true : false => 'false', 
                ($type == 'DateTime' && strtolower($def) == 'current_timestamp') ? true : false => 'null', 
                ($def == '' && $vars['allow_null'] === true) ? true : false => 'null',
                ($type == 'string' && $def != '') ? true : false => "'" . $def . "'", 
                default => $def
            };

            // Check for non-blank default
            if ($def != '') { 
                $has_optional = true;
            }

            // Check default again
            if ($has_optional === true && $def == '') { 
                $def = match($type) { 
                    'bool' => 'true',
                    'int' => '0',
                    'float' => '0.00',
                    'string' => "''",
                    default => 'null'
                };
            }

            // Set props
            $props[$alias] = [
                'name' => $alias, 
                'type' => $type, 
                'null' => ($vars['allow_null'] === true || $def == 'null') ? '?' : '', 
                'default' => $def,
                'doctrine_annotation' => $annotations[$alias] ?? ''
            ];

        }



        // Return
        return $props;
    }


}

