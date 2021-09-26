<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Forms;

use Apex\Svc\Db;
use Apex\App\Base\Web\Utils\FormBuilder;
use Apex\App\Interfaces\Opus\FormInterface;

/**
 * Form - ~alias.title~
 */
class ~alias.title~ implements FormInterface
{

    /**
     * Whether or not to pre-populate form fields with POSTed data.
     */
    public bool $allow_post_values = false;


    #[Inject(FormBuilder::class)]
    private FormBuilder $builder;

    #[Inject(Db::class)]
    private Db $db;

    /**
     * Get form fields.
     */
    public function getFields(array $attr = []):array
    {

        // Initialize
        $builder = $this->builder;

        // Set form fields
~form_code~
        // Return
        return $form_fields;
    }

    /**
     * Get record
     */
    public function getRecord(string $record_id):array
    {

        // Get row
        if (!$row = $this->db->getIdRow('~dbtable~', $record_id)) { 
            $row = [];
        }

        // Return
        return $row;
    }

    /**
     * Validate
     */
    public function validate(array $attr = []):bool
    {

        // Return
        return true;
    }

}

