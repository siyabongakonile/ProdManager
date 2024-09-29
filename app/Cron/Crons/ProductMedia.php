<?php
declare(strict_types = 1);

namespace App\Cron\Crons;

class ProductMedia extends \App\Cron\BaseCron{
    public function run(){
        $this->log("Running ProductMedia cron class");
        // Delete all temporary uploaded media.
    }
}