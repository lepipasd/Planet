<?php

require_once(LIB_PATH.DS.'database.php');

class PaymentMethod extends DatabaseObject
{
    protected static $table_name = "payment_method";
    protected static $primary_key = "payment_method_id";
    // atributes one for each column
    public $payment_method_id;
    public $payment_method_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO payment_method (payment_method_name) VALUES (?) RETURNING payment_method_id";
        $options = array($this->payment_method_name);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE payment_method SET payment_method_name = ? WHERE payment_method_id = ? RETURNING payment_method_id";
        $options = array($this->payment_method_name, $this->payment_method_id);

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
        return isset($this->payment_method_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return array of payment_method objects or false
     */
    public static function find_array_of_payment_method()
    {
        $sql  = "SELECT c.payment_method_id, c.payment_method_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= 'ORDER BY c.payment_method_name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of payment_method ids or false
     */
    public static function find_array_of_payment_method_ids()
    {
        $result = [];
        $sql  = "SELECT payment_method_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $payment_method) {
            $result[] = $payment_method->payment_method_id;
        }

        return !empty($result) ? $result : false;
    }
}
