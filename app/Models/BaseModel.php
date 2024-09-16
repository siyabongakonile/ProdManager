<?php
declare(strict_types = 1);

namespace App\Models;

use App\Database;

class BaseModel{
    /**
     * The database connection that this object.
     */
    protected Database $database;

    /**
     * Initialize the database object.
     */
    public function __construct(Database $db){
        $this->database = $db;
    }
}