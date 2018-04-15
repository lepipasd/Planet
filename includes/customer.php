<?php

require_once(LIB_PATH.DS.'database.php');

class Customer extends DatabaseObject
{
    protected static $table_name = "customer";
    protected static $primary_key = "id";
    protected static $foreign_key = "gym_id";
    // atributes one for each column
    public $id;
    public $name;
    public $barcode;
    public $gym_id;
    public $telephone;
    public $image_path=Null;
    // atributes from gym class
    public $gym_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO customer (name, gym_id, telephone, barcode, image_path) VALUES (?, ?, ?, ?, ?) RETURNING id";
        $options = array($this->name, $this->gym_id, $this->telephone,
                         $this->barcode, $this->image_path);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql  = "UPDATE customer SET name = ?, gym_id = ?, telephone = ?, ";
        $sql .= "barcode = ?, image_path = ? WHERE id = ? RETURNING id";
        $options = array($this->name, $this->gym_id, $this->telephone,
                         $this->barcode, $this->image_path, $this->id);

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
        return isset($this->id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return customer object or false
     */
    public static function find_customer_full_details_by_id($id)
    {
        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g ON c.gym_id = g.gym_id ";
        $sql .= "AND c.id = ? ";
        $sql .= "LIMIT 1";
        $options = array($id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return customer object or false
     */
    public static function check_cms_id($cms_id)
    {
        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.gym_id, c.telephone, c.cms_id, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g ON c.gym_id = g.gym_id ";
        $sql .= "AND c.cms_id = ? ";
        $sql .= "LIMIT 1";
        $options = array($cms_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of customer objects
     */
    public static function find_customers_full_details()
    {
        global $db;

        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g on c.gym_id = g.gym_id ";
        $sql .= 'ORDER BY c.name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $user) {
            yield $user;
        }
    }

    /**//**
     * Description
     * @return generator of customer objects
     */
    public static function find_customers_enumeration()
    {
        // global $db;

        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.telephone FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        // $sql .= 'ORDER BY c.name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $user) {
            yield $user;
        }
    }

    /**//**
     * Description
     * @return generator of customer objects
     */
    public static function find_customers_enumeration_by_term($term)
    {

        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.telephone FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        // $sql .= "WHERE c.name ILIKE '%" . $term . "%' ";
        $sql .= "WHERE c.name ILIKE '%' || ? || '%' ";
        // $sql .= 'ORDER BY c.name collate "C"';
        $options = array($term);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $user) {
            yield $user;
        }
    }

    /**//**
     * Description
     * @return generator of customer objects
     */
    public static function find_customers_by_name($name)
    {
        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g on c.gym_id = g.gym_id ";
        $sql .= "AND upper(c.name) = ? ";
        $sql .= 'ORDER BY c.name collate "C"';
        $options = array($name);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $customer) {
            yield $customer;
        }
    }

    /**//**
     * Description
     * @return array of customer objects or false
     */
    public static function find_array_of_customers_by_name($name)
    {
        $sql  = "SELECT c.id, c.name, c.barcode, c.image_path, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g on c.gym_id = g.gym_id ";
        $sql .= "WHERE upper(c.name) = ? ";
        $sql .= 'ORDER BY c.name collate "C"';
        $options = array($name);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of all customer ids or fasle
     */
    public static function find_array_of_customers_ids()
    {
        $result = [];
        $sql  = "SELECT id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $customer) {
            $result[] = $customer->id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_customer_input_fields()
    {
        $passed_validation_tests = true;
        $check_name = has_presence($this->name);
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        if (!$check_name) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Customer name cannot be blank.";
            $msg .= "</li>";
        }
        // $check_apy_regex = Form::has_format_matching($apy, '/\A\d\Z/');
        $check_telephone = has_presence($this->telephone);
        $check_telephone_numeric = has_number($this->telephone);
        $check_telephone_length = has_length($this->telephone, ['exact' => 10]);
        if (!$check_telephone or !$check_telephone_numeric or !$check_telephone_length) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Telephone: ";
            $msg .= h($this->telephone);
            $msg .= " cannot be blank and must consists of 10 digits.";
            $msg .= "</li>";
        }

        $check_barcode = has_presence($this->barcode);
        $check_barcode_numeric = has_number($this->barcode);
        $check_barcode_length = has_length($this->barcode, ['exact' => 6]);
        if (!$check_barcode or !$check_barcode_numeric or !$check_barcode_length) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Barcode: ";
            $msg .= h($this->barcode);
            $msg .= " cannot be blank and must consists of 6 digits.";
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
