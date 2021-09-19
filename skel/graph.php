<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Graphs;

use Apex\App\Base\Web\Render\Graph;
use Apex\App\Base\Web\Utils\GraphUtils;
use Apex\App\Interfaces\Opus\GraphInterface;

/**
 * Graph - ~alias~
 */
class ~alias.title~ extends Graph implements GraphInterface
{

    /**
     * The type of graph.  Supported values are: bar, line, pie
     */
    public string $type = 'line';

    /**
     * The labels of the X and Y axis
     */
    public string $label_yaxis = 'Date';
    public string $label_xaxis = 'Users';

    #[Inject(GraphUtils::class)]
    protected GraphUtils $graph_utils;


    /**
     * Get graph data
     */
    public function getData(array $attr = []):void
    {

    }

}

