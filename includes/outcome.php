<?php

require_once(LIB_PATH.DS.'database.php');

class Outcome extends DatabaseObject
{
    protected static $table_name = "outcome";
    protected static $primary_key = "outcome_id";
    // atributes one for each column
    public $outcome_id;
    public $outcome_name;
    public $outcome_price;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO outcome (outcome_name, outcome_price) VALUES (?) RETURNING outcome_id";
        $options = array($this->outcome_name, $this->outcome_price);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE outcome SET outcome_name = ?, outcome_price = ? WHERE outcome_id = ? RETURNING outcome_id";
        $options = array($this->outcome_name, $this->outcome_price, $this->outcome_id);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function save()
    {
        // A new record won't have licenceid
        return isset($this->outcome_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return array of income_record objects
     */
    public static function find_array_of_outcome()
    {
        $sql  = "SELECT c.outcome_id, c.outcome_name, c.outcome_price FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= 'ORDER BY c.outcome_name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of income ids or false
     */
    public static function find_array_of_outcome_ids()
    {
        $result = [];
        $sql  = "SELECT outcome_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $outcome) {
            $result[] = $outcome->outcome_id;
        }

        return !empty($result) ? $result : false;
    }
    
}

