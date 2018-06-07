<?php
namespace Portal;
use Portal\PostgreSQLDatabase;


//global $db;
//$db = new PostgreSQLDatabase();
//$db =& $database;

class DatabaseObject
{

    // Common Database Methods
    protected static $table_name;
    protected static $primary_key;

    protected static $foreign_key;

    //private static getDb() {
    //    if ($db == null) {
    //        $db = new PostgreSQLDatabase(); 
    //    }
    //    return $db;
    // }
    //$database = new PostgreSQLDatabase();
    //public $db =& $database;
    //const DB = new PostgreSQLDatabase(); 
    //protected static $db = PostgreSQLDatabase::getInstance();

    private static function getDbConnection() {
        $db = PostgreSQLDatabase::getInstance();
        return $db;
    //    if( !isset(static::$db) ) {
    //        static::$db = new PostgreSQLDatabase();
    //    }
    //    return static::$db;
    }

    /**
     * find_by_sql, executes an sql statement.
     *
     * @param string $sql
     * @param string $sqloptions
     * @return array of objects (each row of the table is an object, and each column name is an attribute name)
     */
    public static function find_by_sql($sql="", $sqloptions=null)
    {
        // global $db;
        //$db = PostgreSQLDatabase::getInstance();
        
        $sth = static::getDbConnection()->query($sql, $sqloptions);
        //$sth = static::$db->query($sql, $sqloptions);

        $object_array = array();
    
        while ($row = $sth->fetch()) {
            $object_array[] = static::instantiate($row);
        }

        return $object_array;
    }

    /**
     * find_all, retrieve all records from a table.
     *
     * @return array of objects (all rows of the table)
     */
    public static function find_all()
    {
        $sql = "SELECT * FROM " . static::$table_name;

        $sth = static::find_by_sql($sql);

        return $sth;
    }

    /**
     * find_by_primary_key,
     * retrieves a single row of the table based on a primary key as an object,
     * each column name is an attribute name
     * @param string $pk, the actual value of the primary key
     * @return an object
     */
    public static function find_by_primary_key($pk)
    {
        $sql = "SELECT * FROM " . static::$table_name . " WHERE " . static::$primary_key . " = ? LIMIT 1";
        $options = array($pk);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**
     * find_by_foreign_key,
     * retrieves the rows of the table based on a foreign key as an array of objects,
     * each column name is an attribute name
     * @param string $fk, the actual value of the foreign key
     * @return an array of objects
     */
    public static function find_by_foreign_key($fk)
    {
        $sql  = "SELECT *  FROM " . static::$table_name . " WHERE " . static::$foreign_key . " = ? ORDER BY ";
        $sql .= static::$primary_key;
        $options = array($fk);

        $sth = static::find_by_sql($sql, $options);

        return $sth;
    }

    public function delete()
    {
        $sql  = "DELETE FROM ";
        $sql .= static::$table_name;
        $sql .= " WHERE ";
        $sql .= static::$primary_key;
        $sql .= " = ? ";
        $sql .= "RETURNING ";
        $sql .= static::$primary_key;
        $options = array($this->{static::$primary_key});

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**
     * instantiate,
     * instantiate the object with the values of the table records
     * each column name is an attribute name
     * @param an array of rows from the table
     * @return an array of objects
     */
    private static function instantiate($record)
    {
        $object = new static;

        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }

        return $object;
    }

    private function has_attribute($attribute)
    {

        // get_object_vars returns an associative array with all attributes
        // (incl. private ones) as the keys and their current values as the value
        $object_vars = get_object_vars($this);

        return array_key_exists($attribute, $object_vars);
    }
}
