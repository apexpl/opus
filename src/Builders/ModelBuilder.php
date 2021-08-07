<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

use Apex\Opus\Helpers\DatabaseHelper;


/**
 * Model builder
 */
class ModelBuilder extends AbstractBuilder
{

    /**
     * Constructor
     */
    public function __construct(
        private DatabaseHelper $db_helper
    ) { 

    }

    /**
     * Build
     */
    public function build(string $filename, string $rootdir, string $dbtable, string $type = 'php8', bool $with_magic = false):string
    {

        // Get namespace
        list($namespace, $class_name) = $this->pathToNamespace($filename);

        // Get properties
        $props = [];
        if ($dbtable != '') { 
            $props = $this->db_helper->tableToProperties($dbtable, dirname("$rootdir/$filename"));
        }

        // Get code
        if ($with_magic === true && in_array($type, ['php7', 'php8'])) { 
            $type .= '-magic';
        }
        $code = file_get_contents(__DIR__ . '/../../skel/codegen/models/' . $type . '.php');

        // Apply properties
        $code = $this->applyProperties($code, $props);

        // Basic replace
        $replace = [
            '~namespace~' => $namespace, 
            '~class_name~' => $class_name, 
            '~dbtable~' => $dbtable, 
            '~magic_class~' => $with_magic === true ? 'MagicModel' : 'BaseModel'
        ];
        $code = strtr($code, $replace);

        // Save file and return
        file_put_contents("$rootdir/$filename", $code);
        return $filename;
    }

    /**
     * Apply properties
     */
    private function applyProperties(string $code, array $props):string
    {

        // Go through property tags
        preg_match_all("/<properties>\n(.*?)<\/properties>/s", $code, $code_match, PREG_SET_ORDER);
        foreach ($code_match as $match) { 

            $final_code = '';
            foreach ($props as $alias => $prop) { 
                $prop['set_method_name'] = $this->applyFilter('set_' . $alias, 'camel');
                $prop['get_phrase'] = $this->applyFilter('get_' . $alias, 'phrase');
                $prop['set_phrase'] = $this->applyFilter('set_' . $alias, 'phrase');
                $prop['default'] = $prop['default'] != '' ? " = $prop[default]" : '';

                // Get get method name
                if ($prop['type'] == 'bool' && preg_match("/^(is_|has_|can_)/", $alias)) { 
                    $prop['get_method_name'] = $this->applyFilter($alias, 'camel');
                } else { 
                    $prop['get_method_name'] = $this->applyFilter('get_' . $alias, 'camel');
                }

                // Replace tmp code
                $tmp_code = $match[1];
                foreach ($prop as $key => $value) { 
                    $tmp_code = str_replace("~$key~", $value, $tmp_code);
                }
                $final_code .= $tmp_code;
            }

            // Finish up
            $final_code = preg_replace("/\,[\s\n\\r]*$/", "", $final_code);
            $code = str_replace($match[0], $final_code, $code);
        }

        // Return
        return $code;
    }

}


