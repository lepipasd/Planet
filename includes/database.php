<?php
require_once(LIB_PATH.DS."config.php");

class PostgreSQLDatabase
{
    private $connection;

    public function __construct()
    {
        $this->open_connection();
    }

    public function open_connection()
    {
        try {
            $this->connection = new PDO('pgsql:host='.DB_SERVER.';port=5432;dbname=' . DATABASE, PGUSER, PGPASS, array(PDO::ATTR_PERSISTENT => true));

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
            $sth->setFetchMode(PDO::FETCH_ASSOC);
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

$database = new PostgreSQLDatabase();
$db =& $database;
