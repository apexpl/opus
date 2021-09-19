<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Forms;

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

    /**
     * Get form fields.
     */
    public function getFields(array $attr = []):array
    {

        // Initialize
        $builder = $this->builder;

        // Set form fields
        $form_fields = [
            'is_active' => $builder->boolean(),
            'full_name' => $builder->textbox()->required()->placeholder('Sample...'),
            'submit' => $builder->createOrUpdateButton('~alias.title~', $attr)
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

