<?php

require_once(LIB_PATH.DS.'database.php');

class AttractionIncome extends DatabaseObject
{
    protected static $table_name = "attraction_income";
    protected static $primary_key = "attraction_income_id";
    // atributes one for each column
    public $attraction_income_id;
    public $attraction_income_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO attraction_income (attraction_income_name) VALUES (?) RETURNING attraction_income_id";
        $options = array($this->attraction_income_name);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE attraction_income SET attraction_income_name = ? WHERE attraction_income_id = ? RETURNING attraction_income_id";
        $options = array($this->attraction_income_name, $this->attraction_income_id);

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
        return isset($this->attraction_income_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return array of attraction_income objects or false
     */
    public static function find_array_of_attraction_income()
    {
        $sql  = "SELECT c.attraction_income_id, c.attraction_income_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= 'ORDER BY c.attraction_income_name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of attraction_income ids or false
     */
    public static function find_array_of_attraction_income_ids()
    {
        $result = [];
        $sql  = "SELECT attraction_income_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $attraction_income) {
            $result[] = $attraction_income->attraction_income_id;
        }

        return !empty($result) ? $result : false;
    }
}
