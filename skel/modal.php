<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Modals;

use Apex\Svc\{App, View};
use Apex\App\Interfaces\Opus\ModalInterface;

/**
 * Modal - ~alias~
 */
class ~alias.title~ implements ModalInterface
{

    /**
     * Display modal
     *
     * This function will be called when the modal is opened, and allows you to 
     * assign any necessary variables to the view to display within the modal.
     */
    public function show(View $view, App $app):void
    {

    }

    /**
     * Submit
     * 
     * This function is executed upon the modal being submitted.
     */
    public function submit(App $app):void
    {

    }

}


