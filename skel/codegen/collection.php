<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\App\Base\DataTypes\BaseCollection;
use ~model_class~;

/**
 * ~class_name~ Collection
 */
class ~class_name~ extends BaseCollection
{

    /**
     * Item class name.  Only instances of this class will be allowed as items of this collection. 
     */
    protected static string $item_class = ~model_name~::class;

    /**
     * Constructor
     */
    public function __construct{
        array $items = []
    ) { 
        parent::__construct($items);
    }

}

