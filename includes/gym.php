<?php

require_once(LIB_PATH.DS.'database.php');

class Gym extends DatabaseObject
{
    protected static $table_name = "gym";
    protected static $primary_key = "gym_id";
    // not a foreign, use it in order to check if user's input exists in database
    protected static $foreign_key = "upper(gym_name)";
    // atributes one for each column
    public $gym_id;
    public $gym_name;
    public $address;
    public $contact_person;
    public $email;
    public $telephone;
    // atributes from loginuser class
    public $login_user_id;
    public $username;
    public $name;
    public $surname;
    // atributes from customer class
    public $id;
    // attributes from outcome_report class
    public $datetime;
    public $outcome;

    public $income_agreed;
    public $income_paied;

    public $number_of_gyms;


    /**//**
     * Description
     * @return integer or false
     */
    public function save()
    {
        // A new record won't have licenceid
        return isset($this->gym_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql = "INSERT INTO gym (gym_name, address, contact_person, email, telephone) VALUES (?, ?, ?, ?, ?) RETURNING gym_id";
        $options = array($this->gym_name, $this->address, $this->contact_person, $this->email, $this->telephone);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql = "UPDATE gym SET gym_name = ?, address = ?, contact_person = ?, email = ?,
        telephone = ? WHERE gym_id = ? RETURNING gym_id";
        $options = array($this->gym_name, $this->address, $this->contact_person, $this->email,
            $this->telephone, $this->gym_id);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return array of user objects or false
     */
    public static function find_users_by_gym_id($gym_id)
    {
        $sql  = "SELECT g.gym_name, c.username, c.name, c.surname, c.login_user_id FROM ";
        $sql .= "login_user c INNER JOIN gym g ON c.gym_id = g.gym_id ";
        $sql .= "AND c.gym_id = ?";
        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return array of customer objects or false
     */
    public static function find_customers_by_gym_id($gym_id)
    {
        $sql  = "SELECT g.gym_name, c.name, c.id FROM ";
        $sql .= "customer c INNER JOIN gym g ON c.gym_id = g.gym_id ";
        $sql .= "AND c.gym_id = ?";
        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? $sth : false;
    }

    /**//**
     * Description
     * @return generator of gym objects
     */
    public static function find_gyms_for_select()
    {
        global $db;

        $sql  = "SELECT c.gym_id, c.gym_name FROM ";
        $sql .= self::$table_name;
        $sql .= " AS c ";
        $sql .= 'ORDER BY c.gym_name collate "C"';
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $gym) {
            yield $gym;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_outcome_by_gym()
    {

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS outcome FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'GROUP BY g.gym_name';

        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $outcome_by_gym) {
            yield $outcome_by_gym;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_income_by_gym()
    {

        $sql  = 'SELECT g.gym_name, SUM(r.price_agreed) AS income_agreed, ';
        $sql .= 'SUM(r.price_paied) AS income_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'GROUP BY g.gym_name';

        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $outcome_by_gym) {
            yield $outcome_by_gym;
        }
    }

    /**//**
     * Description
     * @return integer number of gyms with a specified name
     */
    public static function find_gyms_by_name($gym_name)
    {
        $sql  = 'SELECT COUNT(gym_name) AS number_of_gyms FROM ';
        $sql .= self::$table_name;
        $sql .= ' WHERE gym_name = ? ';

        $options = array($gym_name);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_gyms : false;
    }

    /**//**
     * Description
     * @return array of all gym ids or fasle
     */
    public static function find_array_of_gym_ids()
    {
        $result = [];
        $sql  = "SELECT gym_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $gym) {
            $result[] = $gym->gym_id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_gym_input_fields()
    {
        $passed_validation_tests = true;
        $regex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        $check_email_exists = has_presence($this->email);
        $check_email = has_format_matching($this->email, $regex);
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        if (!$check_email_exists or !$check_email) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Email: ";
            $msg .= h($this->email);
            $msg .= " cannot be blank and must have a proper format.";
            $msg .= "</li>";
        }
        // $check_apy_regex = has_format_matching($apy, '/\A\d\Z/');
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

        $msg .=  "</ul>";

        if ($passed_validation_tests) {
            return "";
        } else {
            return $msg;
        }
    }
}

