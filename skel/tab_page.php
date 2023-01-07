<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\TabControls\~parent_package.title~_~parent_alias.title~;

use Apex\Svc\{App, View};
use Apex\App\Interfaces\Opus\TabPageInterface;

/**
 * Tab Page - ~alias~
 */
class ~alias.title~ implements TabPageInterface
{

    #[Inject(App::class)]
    private App $app;

    #[Inject(View::class)]
    private View $view;

    /**
     * Class name of the parent tab control.
     * @var string
     */
    public string $parent_class = \App\~parent_package.title~\Opus\TabControls\~parent_alias.title~::class;

    /**
     * The display name of the tab page displayed within the web browser.
     */
    public string $name = '~alias.phrase~';

    /**
     * Position of the tab page.  
     *
     * Can be 'bottom', 'top', 'after ALIAS', or 'before ALIAS'.
     */
    public string $position = 'bottom';


    /**
     * Process the tab control
     *
     * This method will be called every time this tab control is displayed, allowing you to 
     * perform any necessary actions to populate the tab control, or process 
     * any submitted forms within it.
     */
    public function process(array $attr = []):void
    {

    }

}



