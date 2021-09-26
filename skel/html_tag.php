<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Tags;

use Apex\Syrus\Parser\StackElement;
use Apex\Syrus\Interfaces\TagInterface;

/**
 * Renders a specific template tag.  Please see developer documentation for details.
 */
class t_~alias.lower~ implements TagInterface
{

    /**
     * Render
     */
    public function render(string $html, StackElement $e):string
    {

        // Get html
        $html = 'Tag Contents';

        // Return
        return $html;
    }

}



