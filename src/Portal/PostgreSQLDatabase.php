<?php
namespace Portal;
// Database Constants
defined('DB_SERVER') ? null : define('DB_SERVER', 'localhost');
defined('PGUSER')     ? null : define('PGUSER', 'leas');
defined('PGPASS')     ? null : define('PGPASS', 'metricos1979!');
defined('DATABASE')  ? null : define('DATABASE', 'planet');

class PostgreSQLDatabase
{
    private $connection;
    private static $_instance; //The single instance

    public static function getInstance() {
        if(!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone() { }

    public function getConnection() {
        return $this->_connection;
    }
    

    public function __construct()
    {
        $this->open_connection();
    }

    public function open_connection()
    {
        try {
            $this->connection = new \PDO('pgsql:host='.DB_SERVER.';port=5432;dbname=' . DATABASE, PGUSER, PGPASS, array(\PDO::ATTR_PERSISTENT => true));

            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function close_connection()
    {
        if (isset($this->connection)) {
            $this->connection = null;
        }
    }

    public function query($sql, $array_options=null)
    {
        try {
            $sth = $this->connection->prepare($sql);
            $sth->setFetchMode(\PDO::FETCH_ASSOC);
            $sth->execute($array_options);

            return $sth;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function num_rows($sth)
    {
        return $sth->rowCount();
    }
}
//$db = new PostgreSQLDatabase();
