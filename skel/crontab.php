<?php
declare(strict_types = 1);

namespace App\~package.title~\Opus\Crontabs;

use Apex\App\Interfaces\Opus\CrontabInterface;

/**
 * Crontab job - ~alias~
 */
class ~alias.title~ implements CrontabInterface
{

    /**
     * Whether or not to automatically execute this crontab job.
     */
    public bool $auto_run = true;

    /**
     * How often to run this crontab job.  
     * Must be formatted as the period letter followed by the number.
     *
     * For the period, I = Minute, H = Hour, D = Day, W = Week, M = Month, Q = Quarter, Y = Year
     *
     * For example, I30 = every 30 minutes, H3 = every 3 hours, W2 = every 2 weeks, et al.
     */
    public string $interval = 'H1';

    /**
     * Description of the crontab job
     */
    public string $description = 'Description of the crontab job.';


    /**
     * Process the crontab job
     */
    public function process():void
    {

    }

}


