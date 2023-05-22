<?php
declare(strict_types=1);

namespace App\~package~\Controllers;

use Apex\Svc\App;
use ~model_namespace~\~model_name~;
use Apex\App\Base\Model\ModelIterator;

/**
 * ~alias~ Controller
 */
class ~model_name~Controller
{

    #[Inject(App::class)]
    private App $app;

    /**
     * Create
     */
    public function create(array $post = []): ?~model_name~
    {

        // Gather POST variables, if needed
        if (count($post) == 0) {
            $post = $this->app->getAllPost();
        }

        // Insert record
        $obj = ~model_name~::insert([
~insert_code~
        ]);

        // Return
        return $obj;
    }

    /**
     * Update
     */
    public function update(int | string $record_id, array $post = []):  ?~model_name~
    {

        // Get post, if needed
        if (count($post) == 0) {
            $post = $this->app->getAllPost();
        }

        // Load model, if needed
        if (!$obj = ~model_name~::whereId($record_id)) {
            throw new \Exception("No ~model_name~ with record ID# $record_id exists");
        }

        // Update record
        $obj->save([
~update_code~
        ]);

        // Return
        return $obj;
    }

    /**
     * Get
     */
    public function get(int | string $record_id): ?~model_name~
    {

        // Get model
        if (!$obj = ~model_name~::whereId($record_id)) {
            return null;
        }

        // Return
        return $obj;
    }

    /**
     * List
     */
    public function list(string $sort_by = 'id', string $sort_dir = 'asc'): ModelIterator
    {
        $res = ~model_name~::all($sort_by, $sort_dir);
        return $res;
    }

    /**
     * Delete
     */
    public function delete(int | string $record_id): bool
    {

        // Get record
        if (!$obj = ~model_name~::whereId($record_id)) {
            return false;
        }

        // Delete
        $obj->delete();
        return true;
    }

    /**
     * Purge
     */
    public function pruge(): void
    {
        ~model_name~::purge();
    }

}


