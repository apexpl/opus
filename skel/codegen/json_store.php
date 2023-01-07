<?php
declare(strict_types = 1);

namespace ~namespace~;

use ~model_class~;
use Apex\Db\Mapper\{FromInstance, ToInstance};

/**
 * ~class_name~ Store
 */
class ~class_name~ implements Countable
{

    /**
     * The file / directory where all entities are saved.
     */
    private string $entity_dir = '';


    /**
     * Get
     */
    public function get(string $alias):?~model_name~
    {

        $filename = rtrim($this->entity_dir, '/') . '/' . $alias . '.json';
        if (!file_exists($filename)) { 
            return null;
        }

        // Load
        if (!$vars = json_decode(file_get_contents($filename), true)) { 
            throw new \Exception("Unable to decode JSON within file, $filename.  Error: " . json_last_error());
        }

        // Map object, return
        $obj = ToInstance(~model_name~::class, $vars);
        return $obj;
    }

    /**
     * List
     */
    public function list():array
    {

        // Check directory exists
        if (!is_dir($this->entity_dir)) { 
            return [];
        }

        // GO through dir
        $aliases = [];
        $files = scandir($this->entity_dir);
        foreach ($files as $file) { 
            if (!preg_match("/^(.+?)\.json$/", $file, $m)) { 
                continue;
            }
            $aliases[] = $m[1];
        }

        // Return
        return $aliases;
    }

    /**
     * Delete
     */
    public function delete(string $alias):bool
    {

        $filename = rtrim($this->entity_dir, '/') . '/' . $alias . '.json';
        if (!file_exists($filename)) { 
            return false;
        }

        // Delete
        unlink($filename);
        return true;
    }

    /**
     * Save
     */
    public function save(~model_name~ $item):void
    {

        // Map object
        $vars = $this->map($obj);
        $primary_col = $this->getPrimaryColumn($vars);

        // Save file
        $filename = rtrim($this->entity_dir, '/') . '/' . $vars[$primary_column] . '.json';
        file_put_contents($filename, json_encode($vars, JSON_PRETTY_PRINT));
    }

    /**
     * Purge
     */
    public function purge():void
    {

        // Check directory
        if (!is_dir($this->entity_dir)) { 
            return;
        }

        // Delete files
        $files = scandir($this->entity_dir);
        foreach ($files as $file) { 
            if (!str_ends_with($file, '.json')) { 
                continue;
            }
            unlink(rtrim($this->entity_dir, '/') . '/' . $file);
        }

    }

    /**
     * Count
     */
    public function count():int
    {
        $aliases = $this->list();
        return count($aliases);
    }

    /**
     * Map object
     */
    public function map(~model_name~ $obj):array
    {

        if (method_exists($obj, 'jsonSerialize')) { 
            $vars = $obj->jsonSerialize();
        } elseif (method_exists($obj, 'toArray')) { 
            $vars = $obj->toArray();
        } else { 
            $vars = FromInstance::map($obj);
        }

        // Return
        return $vars;
    }

    /**
     * Get primary column
     */
    public function getPrimaryColumn(array $vars):string
    {

        // Check property
        if (isset($this->primary_column)) { 
            return $this->primary_column;
        }

        // Check columns
        $primary_col = null;
        foreach (['alias','id','uuid','name'] as $col) { 
            if (array_key_exists($vars, $col)) { 
                continue;
            }
            $primary_col = $col;
            break;
        }

        // Check we found a column
        if ($primary_col === null) { 
            throw new \InvalidArgumentException("Unable to determine primary column for the class " . __CLASS__ . ".  Please add a 'primary_column' property to the class.");
        }

        // return
        return $primary_col;
    }

}


