<?php

require_once(LIB_PATH.DS.'database.php');

class Provider extends DatabaseObject
{
    protected static $table_name = "provider";
    protected static $primary_key = "provider_id";
    protected static $foreign_key = "gym_id";
    // atributes one for each column
    public $provider_id;
    public $provider_name;
    public $gym_id;
    public $telephone;
    // atributes from gym class
    public $gym_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO provider (provider_name, telephone, gym_id) VALUES (?, ?, ?) RETURNING provider_id";
        $options = array($this->provider_name, $this->telephone, $this->gym_id);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE provider SET provider_name = ?, telephone = ?, gym_id = ? ";
        $sql .= "WHERE provider_id = ? RETURNING provider_id";
        $options = array($this->provider_name, $this->telephone, $this->gym_id, $this->provider_id);

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
        return isset($this->provider_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return provider object or false
     */
    public static function find_provider_full_details_by_id($provider_id)
    {
        $sql  = "SELECT c.provider_id, c.provider_name, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g ON c.gym_id = g.gym_id ";
        $sql .= "AND c.provider_id = ? ";
        $sql .= "LIMIT 1";
        $options = array($provider_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of provider objects
     */
    public static function find_providers_full_details()
    {
        $sql  = "SELECT c.provider_id, c.provider_name, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g on c.gym_id = g.gym_id ";
        $sql .= 'ORDER BY c.provider_name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $provider) {
            yield $provider;
        }
    }

    /**//**
     * Description
     * @return generator of provider objects
     */
    public static function find_providers_by_name($name)
    {
        $sql  = "SELECT c.provider_id, c.provider_name, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g on c.gym_id = g.gym_id ";
        $sql .= "AND upper(c.provider_name) = ? ";
        $sql .= 'ORDER BY c.provider_name collate "C"';
        $options = array($name);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $provider) {
            yield $provider;
        }
    }

    /**//**
     * Description
     * @return array of provider objects or false
     */
    public static function find_array_of_providers_by_name($name)
    {
        $sql  = "SELECT c.provider_id, c.provider_name, c.gym_id, c.telephone, g.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= "INNER JOIN gym g on c.gym_id = g.gym_id ";
        $sql .= "WHERE upper(c.provider_name) = ? ";
        $sql .= "ORDER BY c.provider_name";
        $options = array($name);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of all provider ids or fasle
     */
    public static function find_array_of_providers_ids()
    {
        $result = [];
        $sql  = "SELECT provider_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $provider) {
            $result[] = $provider->provider_id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_provider_input_fields()
    {
        $passed_validation_tests = true;
        $check_provider_name = has_presence($this->provider_name);
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        if (!$check_provider_name) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Provider name cannot be blank.";
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
            $msg .= $this->telephone;
            $msg .= " cannot be blank and must consists of 10 digits.";
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

