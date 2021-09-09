<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Ajax;

use Apex\App\Base\Web\Ajax;
use Apex\App\Interfaces\Opus\AjaxInterface;

/**
 * Ajax - ~alias~
 */
class ~alias.title~ extends Ajax implements AjaxInterface
{

    /**
     * Process AJAX function
     */
    public function process():void
    {

        // Send alert
        $this->alert("This is an AJAX function.");

    }

}


