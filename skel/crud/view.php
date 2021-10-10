<?php
declare(strict_types = 1);

namespace Views~parent_namespace~;

use Apex\Svc\{View, App};
use ~model_namespace~\~model_class_name~;

/**
 * Render the template.
 */
class ~class_name~
{

    /**
     * Render
     */
    public function render(View $view, App $app):void
    {

        // Create ~alias_single.lower~
        if ($app->getAction() == 'create') {

            $obj = ~model_class_name~::insert([
~insert_code~
            ]);

            // Callout
            $view->addCallout("Successfully created new ~alias_single.phrase~");

        // Update ~alias_single.lower~
        } elseif ($app->getAction() == 'update') {

        // Get model
            if (!$obj = ~model_class_name~::whereId($app->post('~alias_single.lower~_id'))) {
                throw new \Exception("No record exists with the id#, " . $app->post('~alias_single.lower~_id'));
            }

            // Update record
            $obj->save([
~update_code~
            ]);

            // Add callout
            $view->addCallout("Successfully updated ~alias_single.phrase~");
        }

    }

}


