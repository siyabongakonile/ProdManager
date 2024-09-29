<?php
declare(strict_types = 1);

namespace App\Cron;

abstract class BaseCron{
    abstract public function run();

    protected function log($message){
        $cronLogPath = PATH_ROOT . DIR_SEP . "cron.log";
        $fhandle = fopen($cronLogPath, 'a');
        fwrite($fhandle, $message . "\r\n");
        fclose($fhandle);
    }
}