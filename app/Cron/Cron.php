<?php 
declare(strict_types = 1);

namespace App\Cron;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

use \App\Utils;

class Cron{
    public function run(){
        $cronDir = __DIR__ . DIR_SEP . 'Crons';
        $files = Utils::getFilesFromDir($cronDir);
        /** @var array<BaseCron> $cronObjects */
        $cronObjects = [];
        foreach($files as $file){
            $class = "\\App\\Cron\\Crons\\" . substr($file, 0, strlen($file) - 4);
            if(!class_exists($class))
                continue;

            $classObject = new $class;
            if($classObject instanceof BaseCron)
                $cronObjects[] = new $class();
        }

        foreach($cronObjects as $cronObject)
            $cronObject->run();
    }
}

(new Cron())->run();