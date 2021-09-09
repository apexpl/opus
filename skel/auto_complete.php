<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\AutoCompletes;

use Apex\App\Interfaces\Opus\AutoCompleteInterface;

/**
 * Auto Complete - ~alias~
 */
class ~alias.title~ implements AutoCompleteInterface
{

    /**
     * Constructor
     *
     * All attributes within the <s:function> tag are passed to this method during instantiation, 
     * and here you can define various properties (ie. userid, type, et al) to be used in below methods.
     */
    public function __construct(
        private array $attr 
    ) { 

    }

    /**
     * Search options
     *
     * @return Associative array of options to display within the auto-complete list.
     */
    public function search(string $term):array
    {

        // Initialize
        $options = [
            'id1' => 'First Item',
            'id2' => 'Second Item'
        ];

        // Return
        return $options;
    }

}


