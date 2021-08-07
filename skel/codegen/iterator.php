<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\App\Base\DataTypes\BaseIterator;
use ~model_class~;

/**
 * ~class_name~ Collection
 */
class ~class_name~ extends BaseIterator
{

    /**
     * Item class name.  Only instances of this class will be allowed as items of this collection. 
     *
     * @var string
     */
    protected static string $item_class = ~item_class~;

    /**
     * Constructor
     */
    public function __construct(
        array $items
    ) { 
        parent::__construct($items);
        }

}

