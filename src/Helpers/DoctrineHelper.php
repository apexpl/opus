<?php
declare(strict_types = 1);

namespace Apex\Opus\Helpers;

use Apex\Db\Interfaces\DbInterface;
use Apex\Opus\Builders\AbstractBuilder;
use Doctrine\ORM\Mapping\Table;

/**
 * Doctrine Helper
 */
class DoctrineHelper
{

    // Properties
    private static array $tables = [];

    /**
     * Generate attributes
     */
    public static function generateAttributes(DbInterface $db, string $table_name, string $entity_dir, string $filename = ''):array
    {

        // Get columns
        $columns = $db->getColumnDetails($table_name);
        $foreign_keys = $db->getForeignKeys($table_name);
        $ref_foreign_keys = $db->getReferencedForeignKeys($table_name);

        // Scan entity directory
        self::$tables[$table_name] = $filename;
        self::scanEntityDir($entity_dir);

        // Go through props
        $attributes = [];
        foreach ($columns as $alias => $vars) { 
            $attr = '';

            // Check for primary column
            if ($vars['is_primary'] === true) { 
                $attr .= "    #[ORM\\Id]\n";
            }

            // Get column attribute
            $attr .= self::generateColumnAttribute($vars);

            // Check for foreign key
            if (isset($foreign_keys[$alias]) && isset(self::$tables[$foreign_keys[$alias]['table']])) {
                $target = self::$tables[$foreign_keys[$alias]['table']];
                $attr .= "    #[ManyToOne(targetEntity: \"$target\",  cascade: \"all\")]\n";
            }

            // Go through referenced foreign keys
            foreach ($ref_foreign_keys as $ref_key => $ref_vars) {
                list($table_name, $column) = explode('.', $ref_key, 2);
                if (isset(self::$tables[$table_name])) {
                $target = self::$tables[$table_name];
                    $attr .= "    #[OneToMany(targetEntity: \"$target\",  cascade: \"all\")]\n";

                }
            }

            // Auto increment
            if ($vars['is_auto_increment'] === true) { 
                $attr .= "    #[ORM\GeneratedValue]\n";
            }

            // Add to property
            $attributes[$alias] = rtrim($attr);
        }

        // Return
        return $attributes;
    }

    /**
     * Generate Doctrine column attribute
     */
    private static  function generateColumnAttribute(array $vars):string
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

        // Start attributes
    $attr = "type: '$type'";

        // Add length
        if ($type == 'string' && (int) $vars['length'] > 0) {
            $attr .= ", length: $vars[length]";
        } elseif ($type == 'decimal' && preg_match("/^(\d+?)\,(\d+)/", $vars['length'], $m)) { 
            $attr .= ", precision: $m[1], scale: $m[2]";
        }

        // Check for unique
        if ($vars['is_unique'] === true) { 
            $attr .= ", unique: true";
        }

        // Nullable
        if ($vars['allow_null'] === true) { 
            $attr .= ", nullable: true";
        }

        // Return
        return "    #[ORM\\Column($attr)]\n";
    }

    /**
     * Table to class name
     */
    private static function scanEntityDir(string $entity_dir):void
    {

        // Check if dir exists
        if (!is_dir($entity_dir)) {
            return;
        }

        // Initialize
        $files = scandir($entity_dir);

        // Go through files
        foreach ($files as $file) { 

            // Skip, if not .php file
            if (!str_ends_with($file, '.php')) { 
                continue;
            }

            // Get namespace
            $filename = str_replace(SITE_PATH, "", "$entity_dir/$file");
            list ($namespace, $class_name) = self::pathToNamespace($filename);
            $fqdn = $namespace . "\\" . $class_name;

            // Check class exists
            if (!class_exists($fqdn)) {
                continue;
            }

            // Get reflection class
            $obj = new \ReflectionClass($fqdn);
            $attributes = $obj->getAttributes();

            // Go through attributes, look for table name
            foreach ($attributes as $attr) {

                if ($attr->getName() != Table::class) {
                    continue;
                }

                $args = $attr->getArguments();
                if (!isset($args['name'])) {
                    continue;
                }
                self::$tables[$args['name']] = $class_name;
            }
        }

    }

    /**
     * Path to Namespace
     */
    private static function pathToNamespace(string $filename):array
    {

        // Trim excess
        $filename = preg_replace("/^src\//", "", trim($filename, '/'));
        $filename = preg_replace("/\.php$/", "", $filename);

        // Get names
        $parts = explode("/", $filename);
        $class_name = array_pop($parts);
        $namespace = "App\\" . implode("\\", $parts);

        // Return
        return [$namespace, $class_name];
    }

}


