<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\App\Base\Model\BaseModel;
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



