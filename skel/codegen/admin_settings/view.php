<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\Svc\{View, App};

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

        // Check for form submission
        if ($app->getAction() != 'settings') { 
            return;
        }

        // Set config vars
        $vars = [
            ~php_vars~
        ];

        // Update config vars
        foreach ($vars as $var) { 
            $app->setConfigVar('~package.lower~.' . $var, $app->post($var));
        }

        // Callout
        $view->addCallout("Successfully updated the ~package.phrase~ settings.");
    }

}


