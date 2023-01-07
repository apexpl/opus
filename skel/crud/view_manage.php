<?php
declare(strict_types = 1);

namespace Views~parent_namespace~;

use Apex\Svc\{View, App};
use ~model_namespace~\~model_class_name~;

/**
 * Render the template.
 */
class ~class_name~_manage
{

    /**
     * Render
     */
    public function render(View $view, App $app):void
    {

        // Get record id
        $record_id = $app->get('~alias_single.lower~_id');
        $view->assign('~alias_single.lower~_id', $record_id);
    }

}


