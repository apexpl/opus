<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

/**
 * Class builder
 */
class ClassBuilder extends AbstractBuilder
{

    /**
     * Build
     */
    public function build(string $type, string $filename, string $item_class, string $rootdir = ''):string
    {

        // Get namespace
        list($namespace, $class_name) = $this->pathToNamespace($filename);

        // Parse model class
        $parts = explode("\\", $item_class);
        $model_name = count($parts) > 0 ? array_pop($parts) : 'array';

        // Get code
        $code = file_get_contents(__DIR__ . '/../../skel/codegen/' . $type . '.php');

        // Basic replace
        $replace = [
            '~namespace~' => $namespace, 
            '~class_name~' => $class_name, 
            '~model_class~' => $item_class,
            '~model_name~' => $model_name
        ];
        $code = strtr($code, $replace);

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


