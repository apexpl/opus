<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Forms;

use Apex\Opus\Builders\FormFieldsCreator;


/**
 * Form - ~alias.title~
 */
class ~alias.title~
{

    /**
     * Whether or not to pre-populate form fields with POSTed data.
     */
    public bool $allow_post_values = false;


    /**
     * Constructor
     */
    public function __construct(
        private FormFieldsCreator $creator
    ) { 

    }

    /**
     * Get form fields.
     */
    public function getFields(array $attr = []):array
    {

        // Initialize
        $creator = $this->creator;

        // Set form fields
        $form_fields = [
            'full_name' => $creator->textbox(], 
            'submit' => $creator->createOrUpdateButton('~alias.title~', $attr)
        ];

        // Return
        return $form_fields;
    }

    /**
     * Get record
     */
    public function getRecord(string $record_id):array
    {

        // Get row
        if (!$row = $this->db->getIdRow('~alias.lower~', $record_id)) { 
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

