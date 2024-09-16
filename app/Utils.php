<?php
declare(strict_types = 1);

namespace App;

class Utils{
    static public function addDirSepToPath(string $path): string{
        if($path[-1] == DIRECTORY_SEPARATOR) return $path;
        return $path . DIRECTORY_SEPARATOR;
    }

    static public function firstLetterToLower(string $str): string{
        return \strtolower($str[0]) . substr($str, 1);
    }

    /**
     * Delete the given directory and its contents.
     *
     * @param string $dir
     * @return bool Returns true if the directory was deleted or false otherwise
     */
    static public function deleteDirectory(string $dir): bool{
        if (!is_dir($dir)) {
            return false;
        }
    
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    Utils::deleteDirectory($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
        
        return true;
    }
}