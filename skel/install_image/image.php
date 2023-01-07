<?php
declare(strict_types = 1);

namespace Images\~alias.title~;

use Apex\Svc\{App, Db};
use redis;

/**
 * ~alias.title~ Installation Image
 */
class image
{

    /**
     * Install the image
     *
     * This method is executed after all packages have been installed.  
     * Use this method to make any desired changes to the system and its 
     * configuration to complete the installation of this image.
     */
    public function install(App $app, Db $db, redis $redis):void
    {

        // Execute install.sql file
        $db->executeSqlFile(__DIR__ . '/install.sql');
    }

}


