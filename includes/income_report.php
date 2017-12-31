<?php

require_once(LIB_PATH.DS.'database.php');

class IncomeReport extends DatabaseObject
{
    protected static $table_name = "income_report";
    protected static $primary_key = "income_report_id";
    protected static $foreign_key = "login_user_id";
    // atributes one for each column
    public $income_report_id;
    public $alp;
    public $apy;
    public $datetime;
    public $time;
    public $price_agreed;
    public $price_paied;
    public $taxes;
    public $comments;

    public $number_of_records;

    public $gym_id;
    public $login_user_id;
    public $customer_id;
    public $shift_id;
    public $income_id;
    public $registration_type;
    public $registration_name;
    public $payment_method_id;
    public $attraction_id;

    public $gym_name;
    
    public $username;
    
    public $name;
    
    public $shift_name;
    
    public $income_name;
    public $income_price;
    public $number_of_income;
    
    public $payment_method_name;
    
    public $attraction_name;

    // variables for datepicker
    public $start_date;
    public $end_date;

    /**//**
     * Description
     * @return integer or false
     */
    public function create()
    {
        // global $db;
        $sql  = "INSERT INTO income_report (alp, apy, gym_id, login_user_id, customer_id, ";
        $sql .= "shift_id, income_id, registration_type, price_agreed, price_paied, payment_method_id, taxes, ";
        $sql .= "attraction_id, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
        $sql .= "RETURNING income_report_id";
        $options = array($this->alp, $this->apy, $this->gym_id, $this->login_user_id,
            $this->customer_id, $this->shift_id, $this->income_id, $this->registration_type,
            $this->price_agreed, $this->price_paied, $this->payment_method_id,
            $this->taxes, $this->attraction_id, $this->comments);


        $sth = static::find_by_sql($sql, $options);
        // $sth = $db->query($sql, $options);
        // return $sth;

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        // global $db;

        $sql  = "UPDATE income_report SET alp = ?, apy = ?, gym_id = ?, login_user_id = ?, ";
        $sql .= "customer_id = ?, shift_id = ?, income_id = ?,  ";
        $sql .= "registration_type = ?, price_agreed = ?, price_paied = ?, ";
        $sql .= "payment_method_id = ?, taxes = ?, attraction_id = ?, comments = ? ";
        $sql .= "WHERE income_report_id = ? RETURNING income_report_id";
        $options = array($this->alp, $this->apy, $this->gym_id, $this->login_user_id,
            $this->customer_id, $this->shift_id, $this->income_id,
            $this->registration_type,  $this->price_agreed,
            $this->price_paied, $this->payment_method_id, $this->taxes, $this->attraction_id,
            $this->comments, $this->income_report_id);

        $sth = static::find_by_sql($sql, $options);

        // $sth = $db->query($sql, $options);

        // return $sth;

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function save()
    {
        // A new record won't have licenceid
        return isset($this->income_report_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return generator of income_record objects
     */
    public static function find_records_full_details()
    {
        $sql  = "SELECT r.income_report_id, r.alp, r.apy, r.datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.customer_id, c.name, r.shift_id, s.shift_name, ";
        $sql .= "r.income_id, i.income_name, i.income_price, r.price_agreed, ";
        $sql .= "reg.registration_name, r.price_paied, ";
        $sql .= "r.payment_method_id, p.payment_method_name, r.taxes, ";
        $sql .= "r.attraction_id, a.attraction_income_name AS attraction_name, ";
        $sql .= "r.comments FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN customer c on r.customer_id = c.id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN income i on r.income_id = i.income_id ";
        $sql .= "INNER JOIN payment_method p on r.payment_method_id = p.payment_method_id ";
        $sql .= "INNER JOIN attraction_income a on r.attraction_id = a.attraction_income_id ";
        $sql .= "INNER JOIN registration reg on r.registration_type = reg.registration_type ";
        $sql .= 'ORDER BY r.income_report_id';
        $options = array();

        

        $sth = self::find_by_sql($sql);

        foreach ($sth as $record) {
            yield $record;
        }
    }

    /**//**
     * Description
     * @return generator of income_record objects
     */
    public  function find_records_full_details_by_role()
    {
        $sql  = "SELECT r.income_report_id, r.alp, r.apy, r.datetime AT TIME ZONE 'Europe/Athens' AS datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.customer_id, c.name, ";
        $sql .= "reg.registration_name, r.shift_id, s.shift_name, ";
        $sql .= "r.income_id, i.income_name, i.income_price, r.price_agreed, r.price_paied, ";
        $sql .= "r.payment_method_id, p.payment_method_name, r.taxes, ";
        $sql .= "r.attraction_id, a.attraction_income_name AS attraction_name, ";
        $sql .= "r.comments FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN customer c on r.customer_id = c.id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN income i on r.income_id = i.income_id ";
        $sql .= "INNER JOIN payment_method p on r.payment_method_id = p.payment_method_id ";
        $sql .= "INNER JOIN attraction_income a on r.attraction_id = a.attraction_income_id ";
        $sql .= "INNER JOIN registration reg on r.registration_type = reg.registration_type ";
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'ORDER BY c.name collate "C"';

        $options = array($this->datetime, $this->gym_id);
        
        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $record) {
            yield $record;
        }
    }

    /**//**
     * Description
     * @return generator of income_record objects for manager
     */
    public  function find_records_full_details_as_manager()
    {
        $sql  = "SELECT r.income_report_id, r.alp, r.apy, r.datetime AT TIME ZONE 'Europe/Athens' AS datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.customer_id, c.name, ";
        $sql .= "reg.registration_name, r.shift_id, s.shift_name, ";
        $sql .= "r.income_id, i.income_name, i.income_price, r.price_agreed, r.price_paied, ";
        $sql .= "r.payment_method_id, p.payment_method_name, r.taxes, ";
        $sql .= "r.attraction_id, a.attraction_income_name AS attraction_name, ";
        $sql .= "r.comments FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN customer c on r.customer_id = c.id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN income i on r.income_id = i.income_id ";
        $sql .= "INNER JOIN payment_method p on r.payment_method_id = p.payment_method_id ";
        $sql .= "INNER JOIN attraction_income a on r.attraction_id = a.attraction_income_id ";
        $sql .= "INNER JOIN registration reg on r.registration_type = reg.registration_type ";
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'ORDER BY c.name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);
        
        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $record) {
            yield $record;
        }
    }

    /**//**
     * Description
     * @return income_report object or false
     */
    public static function find_income_record_by_id($id)
    {
        $sql  = "SELECT r.income_report_id, r.alp, r.apy, r.datetime, r.time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.customer_id, c.name, ";
        $sql .= "r.registration_type, r.shift_id, s.shift_name, ";
        $sql .= "r.income_id, i.income_name, i.income_price, r.price_agreed, r.price_paied, ";
        $sql .= "r.payment_method_id, p.payment_method_name, r.taxes, ";
        $sql .= "r.attraction_id, a.attraction_income_name AS attraction_name, ";
        $sql .= "r.comments FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN customer c on r.customer_id = c.id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN income i on r.income_id = i.income_id ";
        $sql .= "INNER JOIN payment_method p on r.payment_method_id = p.payment_method_id ";
        $sql .= "INNER JOIN attraction_income a on r.attraction_id = a.attraction_income_id ";
        $sql .= "AND r.income_report_id = ? ";
        $sql .= "LIMIT 1";
        
        $options = array($id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_daily_income_report()
    {
        // global $db;

        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, i.income_name, ';
        $sql .= 'COUNT(i.income_name) AS  number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC';

        $options = array($this->datetime, $this->gym_id);

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql, $options);

        // return $sth;

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_income_report_overview()
    {

        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, i.income_name, ';
        $sql .= 'COUNT(i.income_name) AS  number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 3';

        $options = array($this->datetime, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function sum_income_report_overview()
    {

        $sql  = 'SELECT r.datetime, g.gym_name, ';
        $sql .= 'COUNT(DISTINCT(r.income_id)) AS number_of_income, ';
        $sql .= 'SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name';

        $options = array($this->datetime, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;

        // foreach ($sth as $dailyreport_by_outcome) {
        //     yield $dailyreport_by_outcome;
        // }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function sum_income_report_overview_cash()
    {

        $sql  = 'SELECT r.datetime, g.gym_name, ';
        $sql .= 'SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'AND r.payment_method_id = 1 ';
        $sql .= 'GROUP BY r.datetime, g.gym_name';

        $options = array($this->datetime, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;

        // foreach ($sth as $dailyreport_by_outcome) {
        //     yield $dailyreport_by_outcome;
        // }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function sum_income_report_overview_as_manager()
    {

        $sql  = 'SELECT g.gym_name, COUNT(DISTINCT(r.income_id)) AS number_of_income, ';
        $sql .= 'SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function sum_income_report_overview_as_manager_daterange()
    {

        $sql  = 'SELECT g.gym_name, COUNT(DISTINCT(r.income_id)) AS number_of_income, ';
        $sql .= 'SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function sum_income_report_overview_cash_as_manager()
    {

        $sql  = 'SELECT g.gym_name, COUNT(DISTINCT(r.income_id)) AS number_of_income, ';
        $sql .= 'SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'AND r.payment_method_id = 1 ';
        $sql .= 'GROUP BY g.gym_name';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function sum_income_report_overview_cash_as_manager_daterange()
    {

        $sql  = 'SELECT g.gym_name, COUNT(DISTINCT(r.income_id)) AS number_of_income, ';
        $sql .= 'SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'AND r.payment_method_id = 1 ';
        $sql .= 'GROUP BY g.gym_name';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_income_report_overview_as_manager()
    {

        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, i.income_name, ';
        $sql .= 'COUNT(i.income_name) AS  number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 3';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_income_report_overview_as_manager_daterange()
    {

        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, i.income_name, ';
        $sql .= 'COUNT(i.income_name) AS  number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 5';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime for manager
     */
    public function view_income_report_as_manager()
    {
        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied, ';
        $sql .= 'sum(r.price_agreed) AS price_agreed, i.income_name, ';
        $sql .= 'count(i.income_name) AS  number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY i.income_name collate "C"';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime for manager
     */
    public function view_income_report_datepicker_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, i.income_name, ';
        $sql .= 'COUNT(i.income_name) AS  number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

     /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_daily_income()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->datetime, $this->gym_id);

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql, $options);

        // return $sth;

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by gym name
     */
    public function view_interval_income_as_manager()
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->gym_id);

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql, $options);

        // return $sth;

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by gym name
     */
    public function view_interval_income_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by gym name
     */
    public function view_interval_income_by_payment_method_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'p.payment_method_name ';
        $sql .= 'FROM income_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN payment_method p ON r.payment_method_id = p.payment_method_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, p.payment_method_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by gym name
     */
    public function view_interval_income_by_payment_method_daterange_as_user()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'p.payment_method_name ';
        $sql .= 'FROM income_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN payment_method p ON r.payment_method_id = p.payment_method_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, p.payment_method_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC';

        $options = array($this->datetime, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_income()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array();

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql);

        // return $sth;

        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_income_daterange()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_income_as_manager()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_income_daterange_as_manager()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_summary_income_by_gym()
    {
        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array();


        $sth = self::find_by_sql($sql);


        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_income_by_gym_daterange()
    {
        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date);


        $sth = self::find_by_sql($sql, $options);


        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_summary_income_by_gym_as_manager($gym_id)
    {
        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($gym_id);


        $sth = self::find_by_sql($sql, $options);


        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_income_by_gym_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);


        $sth = self::find_by_sql($sql, $options);


        foreach ($sth as $summary_income) {
            yield $summary_income;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_income_report($gym_id)
    {
        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied, ';
        $sql .= 'i.income_name FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        // $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, i.income_name ';
        $sql .= 'ORDER BY i.income_name collate "C"';

        $options = array($gym_id);

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql, $options);

        // return $sth;

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_income_report_admin($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied, ';
        $sql .= 'sum(r.price_agreed) AS price_agreed, ';
        $sql .= 'i.income_name, count(i.income_name) AS number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        // $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY i.income_name collate "C"';

        $options = array($gym_id);

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql, $options);

        // return $sth;

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_income_report_admin_daterange()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, ';
        $sql .= 'i.income_name, COUNT(i.income_name) AS number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_income_report_overview_admin($gym_id)
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, ';
        $sql .= 'i.income_name, COUNT(i.income_name) AS number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        // $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 3';

        $options = array($gym_id);


        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_income_report_overview_admin_daterange()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'SUM(r.price_agreed) AS price_agreed, ';
        $sql .= 'i.income_name, COUNT(i.income_name) AS number_of_income FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN income i ON r.income_id = i.income_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, i.income_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 5';

        $options = array($this->start_date, $this->end_date, $this->gym_id);


        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_income_report_by_gym($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        // $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($gym_id);

        // $sth = $db->query($sql, $options);

        $sth = self::find_by_sql($sql, $options);

        // return $sth;

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_income_report_daterange_by_gym()
    {
        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $dailyreport_by_outcome) {
            yield $dailyreport_by_outcome;
        }
    }

    /**//**
     * Description
     * @return array of outcome_record objects grouped by datetime
     */
    public static function find_income_report_by_customer_id($customer_id)
    {
        $sql  = 'SELECT COUNT(r.income_report_id) AS number_of_records FROM ';
        $sql .= 'income_report r INNER JOIN customer c ';
        $sql .= 'ON r.customer_id = c.id ';
        $sql .= 'WHERE r.customer_id = ? ';

        $options = array($customer_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_records : false;
    }

    /**//**
     * Description
     * @return array of outcome_record objects grouped by datetime
     */
    public static function find_income_report_by_gym_id($gym_id)
    {
        $sql  = 'SELECT COUNT(r.income_report_id) AS number_of_records FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';

        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_records : false;
    }

    /**//**
     * Description
     * @return array of outcome_record objects grouped by datetime
     */
    public static function find_income_report_by_income_id($income_id)
    {
        $sql  = 'SELECT COUNT(r.income_report_id) AS number_of_records FROM ';
        $sql .= 'income_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.income_id = ? ';

        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_records : false;
    }

    /**//**
     * Description
     * @return array of income ids or false
     */
    public static function find_array_of_income_report_ids()
    {
        $result = [];
        $sql  = "SELECT income_report_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $income_report) {
            $result[] = $income_report->income_report_id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_user_input()
    {
        $passed_validation_tests = true;
        $check_alp = has_presence($this->alp);
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        if ($check_alp and !has_number($this->alp)) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "ALP: ";
            $msg .= h($this->alp);
            $msg .= " must be a number if not blank.";
            $msg .= "</li>";
        } elseif (!$check_alp) {
            $this->alp = null;
        }
        $check_apy = has_presence($this->apy);
        $check_apy_numeric = has_number($this->apy);
        if (!$check_apy or !$check_apy_numeric) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "APY: ";
            $msg .= h($this->apy);
            $msg .= " cannot be blank and must be a number.";
            $msg .= "</li>";
        }
        // $check_apy_regex = Form::has_format_matching($apy, '/\A\d\Z/');
        $check_customer_id = has_presence($this->customer_id);
        $allowed_customer_ids = Customer::find_array_of_customers_ids();
        $check_customer_id_inclusion = has_inclusion_in($this->customer_id, $allowed_customer_ids);
        if (!$check_customer_id or !$check_customer_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Customer: ";
            $msg .= h($this->customer_id);
            $msg .= " cannot be blank and must be a valid choice: ";
            $msg .= "</li>";
        }
        $check_shift_id = has_presence($this->shift_id);
        $check_shift_id_inclusion = has_inclusion_in($this->shift_id, [1, 2]);
        if (!$check_shift_id or !$check_shift_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Shift: ";
            $msg .= h($this->shift_id);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }
        $check_income_id = has_presence($this->income_id);
        $allowed_income_ids = Income::find_array_of_income_ids();
        $check_income_id_inclusion = has_inclusion_in($this->income_id, $allowed_income_ids);
        if (!$check_income_id or !$check_income_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Income: ";
            $msg .= h($this->income_id);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }

        $check_registration_type = has_presence($this->registration_type);
        $check_registration_type_inclusion = has_inclusion_in($this->registration_type, [1, 2, 3]);
        if (!$check_registration_type or !$check_registration_type_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Type of registartion: ";
            $msg .= h($this->registration_type);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }

        $check_price_agreed = has_presence($this->price_agreed);
        $check_price_agreed_numeric = has_number($this->price_agreed);
        if ($check_price_agreed and !$check_price_agreed_numeric) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Price agreed: ";
            $msg .= h($this->price_agreed);
            $msg .= " with customer must be a number.";
            $msg .= "</li>";
        } elseif (!$check_price_agreed) {
            $price_agreed = 0;
        }
        $check_price_paied = has_presence($this->price_paied);
        $check_price_paied_numeric = has_number($this->price_paied);
        if ($check_price_paied and !$check_price_paied_numeric) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Price paied: ";
            $msg .= h($this->price_paied);
            $msg .= " by customer must be a number.";
            $msg .= "</li>";
        } elseif (!$check_price_paied) {
            $price_paied = 0;
        }
        $check_payment_method_id = has_presence($this->payment_method_id);
        $allowed_payment_method_ids = PaymentMethod::find_array_of_payment_method_ids();
        $check_payment_method_id_inclusion =
            has_inclusion_in($this->payment_method_id, $allowed_payment_method_ids);
        if (!$check_payment_method_id or !$check_payment_method_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Payment method: ";
            $msg .= h($this->payment_method_id);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }
        $check_taxes = has_presence($this->taxes);
        $check_taxes_inclusion = has_inclusion_in($this->taxes, [0.24, 0.13]);
        if (!$check_taxes or !$check_taxes_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Taxes: ";
            $msg .= h($this->taxes);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }
        $check_attraction_income_id = has_presence($this->attraction_id);
        $allowed_attraction_income_ids = AttractionIncome::find_array_of_attraction_income_ids();
        $check_attraction_income_id_inclusion =
            has_inclusion_in($this->attraction_id, $allowed_attraction_income_ids);
        if (!$check_attraction_income_id or !$check_attraction_income_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Attraction: ";
            $msg .= h($this->attraction_id);
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

