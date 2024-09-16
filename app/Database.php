<?php
declare(strict_types = 1);

namespace App;

/**
 * Use singleton for the database object.
 */
class Database{
    /**
     * The mysqli database object.
     */
    static private ?\mysqli $database = null;

    /**
     * The database object.
     */
    static private ?Database $db = null;

    /**
     * The database username.
     */
    private string $username;

    /**
     * The database password.
     */
    private string $password;

    /**
     * The database name.
     */
    private string $dbName;

    private function __construct(){
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->dbName   = DB_NAME;

        Database::$database = new \mysqli(
            DB_HOSTNAME,
            DB_USERNAME,
            DB_PASSWORD,
            DB_NAME
        );
    }

    /**
     * Get a database object.
     * 
     * @return Database The database object
     */
    static public function getInstance(): Database{
        if(Database::$db != null) 
            return Database::$db;
        return new Database();
    }

    /**
     * Wrapper for the database connection object.
     */
    public function query(string $sql, int $result_mode = MYSQLI_STORE_RESULT){
        return Database::$database->query($sql, $result_mode);
    }

    /**
     * Query wrapper that return a boolean on success or failure.
     *
     * @param string $sql The query to execute
     * @param int $result_mode
     * @return boolean Return true if the query was a success or false otherwise
     */
    public function queryWithBool(string $sql, int $result_mode = MYSQLI_STORE_RESULT): bool{
        if($this->query($sql, $result_mode) === false){
            return false;
        }
        return true;
    }

    /**
     * Get last inset id
     * 
     * @return int|string
     */
    public function getInsertId(): int|string{
        return Database::$database->insert_id;
    }

    /**
     * Convert php boolean value to sql value as string.
     * 
     * @param bool $value The value to be converted
     * @return string The string value of the boolean for sql
     */
    public function convertBool(bool $value): string{
        return ($value)? 'true': 'false';
    }

    public function __call(string $name, array $args){
        return Database::$database->{$name}(...$args);
    }
}
?>