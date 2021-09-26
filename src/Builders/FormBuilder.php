<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders;

use Apex\Opus\Opus;
use Apex\Db\Interfaces\DbInterface;

/**
 * Form builder
 */
class FormBuilder extends AbstractBuilder
{

    /**
     * Constructor
     */
    public function __construct(
        protected Opus $opus,
        protected DbInterface $db
    ) {

    }

    /**
     * Get code
     */
    public function getCode(string $dbtable = ''):string
    {

        // Get default code
        if ($dbtable == '') { 
            return $this->getDefaultCode();
        }

        // Get columns
        $columns = $this->db->getColumnDetails($dbtable);

        // Go through columns
        $code = "    \$form_fields = [\n";
        foreach ($columns as $alias => $vars) {

            // Skip, if needed
            if ($vars['is_primary'] === true || $vars['is_auto_increment'] === true) {
                continue;
            }

            // Get field type
            $col_type = $vars['type'];
            $field = match(true) {
                $col_type == 'tinyint(1)' => 'boolean', 
                $col_type == 'boolean' => 'boolean', 
                (preg_match("/^(decimal|numeric)/", $col_type) ? true : false) => 'amount',
                (preg_match("/^(datetime|timestamp)/", $col_type) ? true : false) => 'date', 
                default => 'textbox'
            };
            if ($field == 'date') { continue; }

            // Get default
            $def = $vars['default'] ?? '';
            $def = match(true) { 
                ($field == 'boolean' && in_array($def, ['1','t','true'])) ? true : false => 'true', 
                ($field == 'boolean' && in_array($def, ['0','f','false'])) ? true : false => 'false', 
                ($def == '' && $vars['allow_null'] === true) ? true : false => '',
                default => "'" . $def . "'"
            };

        // Add to code
            $code .= "            '$alias' => \$builder->$field()";
            if ($vars['allow_null'] === false) {
                $code .= "->required()";
            }

            // Add value, if needed
            if ($def != '') {
                $code .= "->value($def)";
            }
            $code .= ",\n";
        }

        // Add submit button
        $name = preg_match("/^.+_(.+)$/", $dbtable, $m) ? $m[1] : $dbtable;
        $name = $this->applyFilter($name, 'title');
        $code .= "            'submit' => \$builder->createOrUpdateButton('$name', \$attr)\n";

        // Finish code, and return
        $code .= "        ];\n";
        return $code;
    }

    /**
     * Get default code
     */
    private function getDefaultCode():string
    {

        // Set code
        $code = "        \$form_fields = [\n";
        $code .= "            'is_active' => \$builder->boolean(),\n";
        $code .= "            'full_name' => \$builder->textbox()->required()->placeholder('Sample...'),\n";
        $code .= "            'submit' => \$builder->createOrUpdateButton('~alias.title~', $attr)\n";
        $code .= "        ];\n";

        // Return
        return $code;
    }

}


