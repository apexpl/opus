<?php
declare(strict_types = 1);

namespace Apex\Opus\Builders\Components;

use Apex\Opus\Opus;

/**
 * Package
 */
class Package
{

    /**
 * Constructor
     */
    public function __construct(
        private Opus $opus
    ) { 

    }

    /**
     * Create
     */
    public function create(array $vars):void
    {

        // Initialize
        $db = $this->opus->db;
        $access = $vars['access'] ?? 'public';
        $repo_id = $vars['repo_id'] ?? 0;
        $author = $vars['author'] ?? '';
        $alias = strtolower($vars['alias']);

        // Insert into database
        $db->insert('internal_packages', [
            'access' => $access, 
            'repo_id' => $repo_id, 
            'author' => $author, 
            'alias' => $alias, 
            'name' => $alias
        ]);

    }

    /**
     * Delete
     */
    public function delete(array $vars):void
    {

    }


}


 

