<?php

require_once(LIB_PATH.DS.'database.php');

class Income extends DatabaseObject
{
    protected static $table_name = "income";
    protected static $primary_key = "income_id";
    // atributes one for each column
    public $income_id;
    public $income_name;
    public $income_price;
    public $description;

    public $number_of_incomes;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO income (income_name, income_price, description) ";
        $sql .= "VALUES (?, ?, ?) RETURNING income_id";
        $options = array($this->income_name, $this->income_price, $this->description);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE income SET income_name = ?, income_price = ?, description = ? ";
        $sql .= "WHERE income_id = ? RETURNING income_id";
        $options = array($this->income_name, $this->income_price, $this->description,
            $this->income_id);

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
        return isset($this->income_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return array of income objects or false
     */
    public static function find_array_of_income()
    {
        $sql  = "SELECT c.income_id, c.income_name, c.income_price, c.description FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= 'ORDER BY c.income_name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of income ids or false
     */
    public static function find_array_of_income_ids()
    {
        $result = [];
        $sql  = "SELECT income_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $income) {
            $result[] = $income->income_id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description
     * @return integer number of gyms with a specified name
     */
    public static function find_incomes_by_name($income_name)
    {
        $sql  = 'SELECT COUNT(income_name) AS number_of_incomes FROM ';
        $sql .= self::$table_name;
        $sql .= ' WHERE income_name = ? ';

        $options = array($income_name);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_incomes : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_income_input_fields()
    {
        $passed_validation_tests = true;
        
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        $check_income_name = has_presence($this->income_name);
        $check_price = has_presence($this->price);
        $check_price_numeric = has_number($this->price);
        if (!$check_income_name) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Income name: ";
            $msg .= h($this->income_name);
            $msg .= " cannot be blank.";
            $msg .= "</li>";
        }
        if ($check_price && !$check_price_numeric) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Income price: ";
            $msg .= h($this->price);
            $msg .= " (if exists) must be numeric.";
            $msg .= "</li>";
        }

        $msg .=  "</ul>";

        if ($passed_validation_tests) {
            return "";
        } else {
            return $msg;
        }
    }
}

