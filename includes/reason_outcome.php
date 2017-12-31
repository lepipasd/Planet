<?php

require_once(LIB_PATH.DS.'database.php');

class ReasonOutcome extends DatabaseObject
{
    protected static $table_name = "reason_outcome";
    protected static $primary_key = "reason_outcome_id";
    // atributes one for each column
    public $reason_outcome_id;
    public $reason_outcome_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO reason_outcome (reason_outcome_name) VALUES (?) RETURNING reason_outcome_id";
        $options = array($this->reason_outcome_name);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE reason_outcome SET reason_outcome_name = ? WHERE reason_outcome_id = ? RETURNING reason_outcome_id";
        $options = array($this->reason_outcome_name, $this->reason_outcome_id);

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
        return isset($this->reason_outcome_id) ? $this->update() : $this->create();
    }
}
