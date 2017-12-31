<?php

require_once(LIB_PATH.DS.'database.php');

class Shift extends DatabaseObject
{
    protected static $table_name = "shift";
    protected static $primary_key = "shift_id";
    // atributes one for each column
    public $shift_id;
    public $shift_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO shift (shift_name) VALUES (?) RETURNING shift_id";
        $options = array($this->shift_name);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE shift SET shift_name = ? WHERE shift_id = ? RETURNING shift_id";
        $options = array($this->shift_name, $this->shift_id);

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
        return isset($this->shift_id) ? $this->update() : $this->create();
    }
}
