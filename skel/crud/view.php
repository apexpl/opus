<?php
declare(strict_types = 1);

namespace Views~parent_namespace~;

use Apex\Svc\{View, App};
use App\~package~\Controllers\~controller_name~;

/**
 * Render the template.
 */
class ~class_name~
{

    /**
     * Post
     */
    public function post(View $view, App $app, ~controller_name~ $controller):void
    {

        // Create ~alias_single.lower~
        if ($app->getAction() == 'create') {

            // Create
            if (!$obj = $controller->create()) {
                $view->addCallout("Unable to create new ~model_name~");
            } else {
                $view->addCallout("Successfully created new ~alias_single.phrase~");
        }

        // Update ~alias_single.lower~
        } elseif ($app->getAction() == 'update') {

        // Get model
            if (!$obj = $controller->get($app->post('~alias_single.lower~_id'))) {
                throw new \Exception("No record exists with the id#, " . $app->post('~alias_single.lower~_id'));
            }

            // Update record
            $controller->update($app->post('~alias_single.lower~_id'));

            // Add callout
            $view->addCallout("Successfully updated ~alias_single.phrase~");
        }

    }

}


