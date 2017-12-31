<?php

require_once(LIB_PATH.DS.'database.php');

class LoginUser extends DatabaseObject
{
    protected static $table_name = "login_user";
    protected static $primary_key = "login_user_id";
    protected static $foreign_key = "gym_id";
    // atributes one for each column
    public $login_user_id;
    public $username;
    public $password;
    public $name;
    public $surname;
    public $role_id;
    public $email;
    public $gym_id;
    // atributes from gym class
    public $gym_name;
    public $role_name;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        $sql  = "INSERT INTO login_user (username, password, name, surname, role_id, email, gym_id) VALUES ";
        $sql .= "(?, ?, ?, ?, ?, ?, ?) RETURNING login_user_id";
        $options = array($this->username, $this->password, $this->name,
            $this->surname, $this->role_id, $this->email, $this->gym_id);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql  = "UPDATE login_user SET username = ?, password = ?, name = ?, ";
        $sql .= "surname = ?, role_id = ?, email = ?, gym_id = ? WHERE login_user_id = ? ";
        $sql .= "RETURNING login_user_id";
        $options = array($this->username, $this->password, $this->name, $this->surname,
            $this->role_id, $this->email, $this->gym_id, $this->login_user_id);

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
        return isset($this->login_user_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return user object or false
     */
    public static function authenticate($username = "", $password = "")
    {
        $sql = "SELECT * FROM login_user WHERE username = ? AND password = ? LIMIT 1";
        $options = array($username, $password);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of user objects
     */
    public static function find_users()
    {
        $sql  = "SELECT u.login_user_id, u.username, u.password, u.name, ";
        $sql .= "u.surname, u.role_id, r.role_name, u.email, u.gym_id, g.gym_name ";
        $sql .= "FROM login_user u inner join role r on u.role_id = r.role_id ";
        $sql .= "INNER JOIN gym g on u.gym_id = g.gym_id ";
        $sql .= "group by g.gym_name, u.login_user_id, r.role_name ";
        $sql .= "order by g.gym_name, u.login_user_id, r.role_name";
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $user) {
            yield $user;
        }
    }

    /**//**
     * Description
     * @return generator of user objects
     */
    public static function find_users_no_pwd()
    {
        $sql  = "SELECT u.login_user_id, u.username, u.name, ";
        $sql .= "u.surname, u.role_id, r.role_name, u.email, u.gym_id, g.gym_name ";
        $sql .= "FROM login_user u inner join role r on u.role_id = r.role_id ";
        $sql .= "INNER JOIN gym g on u.gym_id = g.gym_id ";
        $sql .= "group by g.gym_name, u.login_user_id, r.role_name ";
        $sql .= "order by g.gym_name, u.login_user_id, r.role_name";
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $user) {
            yield $user;
        }
    }

    /**//**
     * Description
     * @return generator of user objects
     */
    public  static function find_users_by_role($gym_id)
    {
        $sql  = "SELECT u.login_user_id, u.username, u.name, ";
        $sql .= "u.surname, u.role_id, r.role_name, u.email, u.gym_id, g.gym_name ";
        $sql .= "FROM login_user u inner join role r on u.role_id = r.role_id ";
        $sql .= "INNER JOIN gym g on u.gym_id = g.gym_id ";
        $sql .= "WHERE u.gym_id = ? ";
        $sql .= "GROUP BY g.gym_name, u.login_user_id, r.role_name ";
        $sql .= "ORDER BY g.gym_name, u.login_user_id, r.role_name";
        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $user) {
            yield $user;
        }
    }

    /**//**
     * Description
     * @return user object or false
     */
    public static function find_user_by_username($username)
    {
        $sql  = "SELECT u.login_user_id, u.username, u.password, u.name, ";
        $sql .= "u.surname, u.role_id, r.role_name, u.email, u.gym_id, g.gym_name ";
        $sql .= "FROM login_user u INNER JOIN role r on u.role_id = r.role_id ";
        $sql .= "INNER JOIN gym g ON u.gym_id = g.gym_id ";
        $sql .= "WHERE lower(u.username) = ? LIMIT 1";
        $options = array($username);

        $sth = self::find_by_sql($sql, $options);

        

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return user object or false
     */
    public static function find_user_by_id($login_user_id)
    {
        $sql  = "SELECT u.login_user_id, u.username, u.password, u.name, ";
        $sql .= "u.surname, u.role_id, r.role_name, u.email, u.gym_id, g.gym_name ";
        $sql .= "FROM login_user u inner join role r on u.role_id = r.role_id ";
        $sql .= "INNER JOIN gym g ON u.gym_id = g.gym_id ";
        $sql .= "WHERE u.login_user_id = ? LIMIT 1";
        $options = array($login_user_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

     /**//**
     * Description
     * @return array of all login_user ids or fasle
     */
    public static function find_array_of_login_user_ids()
    {
        $result = [];
        $sql  = "SELECT login_user_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $login_user) {
            $result[] = $login_user->login_user_id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_user_input_fields()
    {
        $passed_validation_tests = true;
        $check_username = has_presence($this->username);
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        if (!$check_username) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Username cannot be blank.";
            $msg .= "</li>";
        }

        $check_name = has_presence($this->name);
        
        if (!$check_name) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Name cannot be blank.";
            $msg .= "</li>";
        }

        $check_surname = has_presence($this->surname);
        
        if (!$check_surname) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Surname cannot be blank.";
            $msg .= "</li>";
        }

        $regex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        $check_email_exists = has_presence($this->email);
        $check_email = has_format_matching($this->email, $regex);
        
        if (!$check_email_exists or !$check_email) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Email: ";
            $msg .= h($this->email);
            $msg .= " cannot be blank and must have a proper format.";
            $msg .= "</li>";
        }
        // $check_apy_regex = Form::has_format_matching($apy, '/\A\d\Z/');
        if ($this->role_id == 4) {
            $permited_role_ids = [1, 2, 4];
        } else {
            $permited_role_ids = [1, 2];
        }
        $check_role_id = has_presence($this->role_id);
        $check_role_id_inclusion = has_inclusion_in($this->role_id, $permited_role_ids);
        if (!$check_role_id or !$check_role_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Role ID: ";
            $msg .= h($this->telephone);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }

        $check_gym_id = has_presence($this->gym_id);
        $allowed_gym_ids = Gym::find_array_of_gym_ids();
        $check_gym_id_inclusion = has_inclusion_in($this->gym_id, $allowed_gym_ids);
        if (!$check_gym_id or !$check_gym_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Gym ID: ";
            $msg .= h($this->gym_id);
            $msg .= " cannot be blank and must be a valid choice.";
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

