<?php
declare(strict_types = 1);

namespace ~namespace~;

use Apex\Svc\Db;
use ~model_class~;

/**
 * ~model_name~ Controller
 */
class ~class_name~
{

    /**
     * Create
     */
    public function create(array $values):~model_name~
    {

        // Create model
        $obj = $this->cntr->make(~model_name~::class, $vars);
        $obj->save();

        // Return
        return $obj;
    }

    /**
     * Update
     */
    public function update(int | string $id, array $values):void
    {

        if (null !== ($item = $this->get($id))) { 
            $item->save($values);
        }

    }

    /**
     * List
     */
    public function list():array
    {
        $items = ~model_name~::all();
        return $items;
    }

    /**
     * Get
     */
    public function get(string | int $id):?~model_name~
    {
        $item = ~model_name~::whereId9$id);
        return $item;
    }

    /**
     * Remove
     */
    public function remove(int | string $id):bool
    {

        if (null !== ($item = $this->get(id)) { 
            $item->delete();
            return true;
        }

        return false;
    }

}




