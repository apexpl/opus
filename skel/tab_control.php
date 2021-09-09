<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\TabControls;

use Apex\App\Interfaces\Opus\TabControlInterface;

/**
 * Tab Control - ~alias~
 */
class ~alias.title~ implements TabControlInterface
{

    // Tab pages
    public array $tab_pages = [
        'some_page' => 'Name of Page'
        ];

    /**
     * Constructor
     *
     * All attributes within the <s:function> tag are passed to this method during instantiation, 
     * and here you can define various properties (ie. userid, type, et al) to be used in below methods.
     */
    public function __construct(
        private array $attr 
    ) { 

    }

    /**
     * Process the tab control
     *
     * This method will be called every time this tab control is displayed, allowing you to 
     * perform any necessary actions to populate the tab control, or process 
     * any submitted forms within it.
     */
    public function process():void
    {

    }

}



