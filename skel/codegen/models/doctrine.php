<?php
declare(strict_types = 1);

namespace ~namespace~;

use Doctrine\ORM\Mapping\{Entity, Table, Column, Id, GeneratedValue, OneToOne, OneToMany, ManyToOne, ManyToMany};
use DateTime;

/**
 * @Entity
 * @Table(name="~dbtable~")
 */
class ~class_name~
{

<properties>
    ~doctrine_annotation~
    private ~null~~type~ $~name~~default~;

</properties>

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


