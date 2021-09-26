<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

use Apex\Opus\Opus;
use Apex\App\Cli\Cli;
use Apex\Opus\Exceptions\{OpusYamlException, OpusComponentNotExistsException, OpusParamNotExistsException};
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\String\Inflector\EnglishInflector;

/**
 * Abstract builder
 */
class AbstractBuilder
{

    // Properties
    protected Opus $opus;
    protected Cli $cli;

    /**
     * Get component definition
     */
    public function getComponentDefinition(string $comp_type):?array
    {

        // Load router file'
        try {
            $yaml = Yaml::parseFile(__DIR__ . '/../../config/opus.yml');
        } catch (ParseException $e) { 
            throw new OpusYamlException("Unable to parse opus.yml file, error: " . $e->getMessage());
        }

        // Check for comp type
        if (!isset($yaml[$comp_type])) { 
            throw new OpusComponentNotExistsException("Component does not exist within YAML configuration, $comp_type");
        }

        // Return
        return $yaml[$comp_type];
    }

    /**
     * Convert case
     */
    public function convertString(string $string, array $vars):string
    {

        // GO through merge vas
        $replace = [];
        preg_match_all("/~(.+?)~/", $string, $var_match, PREG_SET_ORDER);
        foreach ($var_match as $m) { 

            // Check if already done
            if (isset($replace[$m[0]])) { 
                continue;
            }

            // Initialize
            $filters = explode('.', $m[1]);
            $alias = array_shift($filters);

            // Check alias
            if (!isset($vars[$alias])) { 
                continue;
                //throw new OpusParamNotExistsException("The parameter does not exist while converting string, $alias, string: $string");
            }
            $word = $vars[$alias];

            // Apply filters
            foreach ($filters as $filter) { 
                $word = $this->applyFilter($word, $filter);
            }
            $replace[$m[0]] = $word;
        }

        // Convert, and return
        return strtr($string, $replace);
    }

    /**
     * Apply filter
     */
    public function applyFilter(string $word, string $filter):string
    {

        // Init
        $word = new UnicodeString($word);

        // Apply simple filters
        $new_word = match($filter) { 
            'camel' => $word->camel(), 
            'title' => $word->camel()->title(), 
            'lower' => strtolower(preg_replace("/(.)([A-Z][a-z])/", '$1_$2', (string) $word)), 
            'upper' => strtoupper(preg_replace("/(.)([A-Z][a-z])/", '$1_$2', (string) $word->camel())), 
            'phrase' => ucwords(strtolower(preg_replace("/(.)([A-Z][a-z])/", '$1 $2', (string) $word->camel()))), 
            default => ''
        };

        // Return if ok
        if ($new_word != '') { 
            return (string) $new_word;
        }

        // Single
        if ($filter == 'single' || $filter == 'plural') { 

            // Check if already chosen
            if (isset($this->opus->injection_opt[$filter][$word])) { 
                return $this->opus->injection_opt[$filter][$word];
            }

            // Run inflector
            $inflector = new EnglishInflector();
            $method = $filter == 'single' ? 'singularize' : 'pluralize';
            $results = $inflector->$method((string) $word);

            // Check for one result
            if (is_array($results) && count($results) == 1) { 
                return preg_replace("/ss$/", "", $results[0]);
            } elseif (!is_array($results)) { 
                return preg_replace("/ss$/", "", $results);
            }

            // Create options
            list($x, $options) = [1, []];
            foreach ($results as $r) { 
                $options[(string) $x] = $r;
            $x++; }

            // Ask which option
            $opt = $this->cli->getOption("Multiple options to $method the word '$word' were found.  Please select one:", $options, (string) array_keys($options)[0]);
            //$this->opus->injection_opt[$filter][$word] = $options[$opt];
            return (string) $options[$opt];
        }

        // If here, invalid filter
        throw new OpusInvalidFilterException("Invalid filter '$filter' when trying to pplay to word '$word'");
    }

    /**
     * Path to namespace
     */
    public function pathToNamespace(string $filename):array
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


