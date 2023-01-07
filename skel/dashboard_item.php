<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\DashboardItems;

use Apex\App\Interfaces\Opus\DashboardItemInterface;

/**
 * Dashboard item - ~alias~
 */
class ~alias.title~ implements DashboardItemInterface
{

    /**
     * The type of dashboard item.
     * Can be either:  top, right, tab
     */
    public string $type = 'right';

    /**
     * The area this dashboard item is available.  
     * Generally will only ever be either 'admin' or 'members'.
     */
    public string $area = 'admin';

    /**
     * Whether or not to activate this dashboard item upon initial installation
     */
    public bool $is_default = false;

    /**
     * The title of the dashboard item.
     */
    public string $title = 'Total Users';

    /**
     * The div id and panel class name of the dashboard item.
     * Only applicable for items with $type of 'top'.
     */
    public string $divid = 'members-online';
    public string $panel_class = 'panel bg-teal-400';


    /**
     * Render the dashboard item.
     *
     * @return The HTML contents of the dashboard item.
     */
    public function render():string
    {

        // Set html
        $html = 'Item Contents';

        // Return
        return $html;
    }

}


