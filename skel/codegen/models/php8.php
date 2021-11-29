<?php
declare(strict_types=1);

namespace ~namespace~;

use Apex\App\Base\Model\BaseModel;~use_declarations~
use DateTime;

/**
 * ~class_name~ Model
 */
class ~class_name~ extends BaseModel
{

    /**
     * Database table
     *
     * @var string
     */
    protected static string $dbtable = '~dbtable~';

    /**
     * Constructor
     */
    public function __construct(
<properties>
        protected ~null~~type~ $~name~~default~,
</properties>
    ) { 

    }
<relations_one>
    /**
     * Get ~get_phrase~
     */
    public function ~method_name~():~short_name~
    {
        $obj = ~short_name~::whereId($this->~name~);
        return $obj;
    }
</relations_one><relations_many>
    /**
     * Get ~get_phrase~
     */
    public function ~method_name~(string $sort_by = '', string $sort_dir = '', int $limit = 0, int $offset = 0):ModelIterator
    {
        $result = $this->getChildren('~foreign_key~', ~short_name~::class, $sort_by, $sort_dir, $limit, $offset);
        return $result;
    }
</relations_many>
<properties>
    /**
     * ~get_phrase~
     */
    public function ~get_method_name~():~null~~type~
    {
        return $this->~name~;
    }

</properties>
<properties>
    /**
     * ~set_phrase~
     */
    public function ~set_method_name~(~type~ $~name~):void
    {
        $this->~name~ = $~name~;
    }

</properties>

}



