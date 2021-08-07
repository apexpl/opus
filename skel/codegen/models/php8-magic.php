<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\App\Base\Model\~magic_class~;
use DateTime;

/**
 * ~class_name~ Model
 */
class ~class_name~ extends ~magic_class~
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

}

