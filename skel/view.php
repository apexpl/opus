<?php
declare(strict_types = 1);

namespace Views~parent_namespace~;

use Apex\Svc\{View, App};

/**
 * Render the template.  All methods below are optional, may be 
 * removed as desired, and all methods support method based dependency injection.
 */
class ~alias.lower~
{

    /**
     * Render.  This method will be called with every view 
 * regardless of the HTTP method.
     */
    public function render(View $view, App $app):void
    {

    }

    /**
     * Get.  Will only be called when the HTTP method is GET.
     */
    public function get(View $view, App $app):void
    {

    }

    /**
     * Post.  Will only be called when the HTTP method is POST.
     */
    public function post(View $view, App $app):void
    {

    }

}


