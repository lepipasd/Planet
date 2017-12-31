<?php

require_once(LIB_PATH.DS.'database.php');

class OutcomeReport extends DatabaseObject
{
    protected static $table_name = "outcome_report";
    protected static $primary_key = "outcome_report_id";
    protected static $foreign_key = "login_user_id";
    // atributes one for each column
    public $outcome_report_id;
    public $ap;
    public $datetime;
    public $time;
    public $price_paied;
    public $comments;


    public $gym_id;
    public $login_user_id;
    public $provider_id;
    public $shift_id;
    public $reason_outcome_id;
    public $outcome_id;

    public $gym_name;
    
    public $username;
    
    public $provider_name;
    
    public $shift_name;
    
    public $reason_outcome_name;
    public $outcome_name;
    public $outcome_price;
    public $number_of_outcome;

    public $number_of_records;

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

        
        $sql  = "INSERT INTO outcome_report (ap, gym_id, login_user_id, provider_id, ";
        $sql .= "shift_id, reason_outcome_id, price_paied, comments, outcome_id) ";
        $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ";
        $sql .= "RETURNING outcome_report_id";
        $options = array($this->ap, $this->gym_id, $this->login_user_id,
            $this->provider_id, $this->shift_id, $this->reason_outcome_id, $this->price_paied,
            $this->comments, $this->outcome_id);

        // $sth = $db->query($sql, $sqloptions);

        // return $sth;

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    /**//**
     * Description
     * @return integer or false
     */
    public function update()
    {
        $sql  = "UPDATE outcome_report SET ap = ?, gym_id = ?, login_user_id = ?, ";
        $sql .= "provider_id = ?, shift_id = ?, reason_outcome_id = ?, price_paied = ?, ";
        $sql .= "comments = ?, outcome_id = ? ";
        $sql .= "WHERE outcome_report_id = ? RETURNING outcome_report_id";
        $options = array($this->ap, $this->gym_id, $this->login_user_id,
            $this->provider_id, $this->shift_id, $this->reason_outcome_id,
            $this->price_paied, $this->comments, $this->outcome_id, $this->outcome_report_id);

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
        return isset($this->outcome_report_id) ? $this->update() : $this->create();
    }

    /**//**
     * Description
     * @return generator of income_record objects
     */
    public static function find_outcome_records_full_details()
    {
        $sql  = "SELECT r.outcome_report_id, r.ap, r.datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.provider_id, p.provider_name, r.shift_id, s.shift_name, ";
        $sql .= "r.reason_outcome_id, ro.reason_outcome_name, r.price_paied, ";
        $sql .= "r.comments, r.outcome_id, o.outcome_name FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN provider p on r.provider_id = p.provider_id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN reason_outcome ro on r.reason_outcome_id = ro.reason_outcome_id ";
        $sql .= "INNER JOIN outcome o on r.outcome_id = o.outcome_id ";
        $sql .= 'ORDER BY r.outcome_report_id';
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
    public function find_outcome_records_full_details_by_role()
    {
        $sql  = "SELECT r.outcome_report_id, r.ap, r.datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.provider_id, p.provider_name, r.shift_id, s.shift_name, ";
        $sql .= "r.reason_outcome_id, ro.reason_outcome_name, r.price_paied, ";
        $sql .= "r.comments, r.outcome_id, o.outcome_name FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN provider p on r.provider_id = p.provider_id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN reason_outcome ro on r.reason_outcome_id = ro.reason_outcome_id ";
        $sql .= "INNER JOIN outcome o on r.outcome_id = o.outcome_id ";
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'ORDER BY p.provider_name collate "C"';

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
    public function find_outcome_records_full_details_as_manager()
    {
        $sql  = "SELECT r.outcome_report_id, r.ap, r.datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.provider_id, p.provider_name, r.shift_id, s.shift_name, ";
        $sql .= "r.reason_outcome_id, ro.reason_outcome_name, r.price_paied, ";
        $sql .= "r.comments, r.outcome_id, o.outcome_name FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN provider p on r.provider_id = p.provider_id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN reason_outcome ro on r.reason_outcome_id = ro.reason_outcome_id ";
        $sql .= "INNER JOIN outcome o on r.outcome_id = o.outcome_id ";
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'ORDER BY p.provider_name collate "C"';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $record) {
            yield $record;
        }
    }

    /**//**
     * Description
     * @return income_report object or false
     */
    public static function find_outcome_record_by_id($id)
    {
        $sql  = "SELECT r.outcome_report_id, r.ap, r.datetime, ";
        $sql .= "to_char(r.time AT TIME ZONE 'Europe/Athens', 'YYYY-MM-DD HH24:MI:SS') AS time, r.gym_id, g.gym_name, ";
        $sql .= "r.login_user_id, u.username, r.provider_id, p.provider_name, r.shift_id, s.shift_name, ";
        $sql .= "r.reason_outcome_id, ro.reason_outcome_name, r.price_paied, ";
        $sql .= "r.comments, r.outcome_id, o.outcome_name FROM ";
        $sql .= self::$table_name;
        $sql .= " r ";
        $sql .= "INNER JOIN gym g on r.gym_id = g.gym_id ";
        $sql .= "INNER JOIN login_user u on r.login_user_id = u.login_user_id ";
        $sql .= "INNER JOIN provider p on r.provider_id = p.provider_id ";
        $sql .= "INNER JOIN shift s on r.shift_id = s.shift_id ";
        $sql .= "INNER JOIN reason_outcome ro on r.reason_outcome_id = ro.reason_outcome_id ";
        $sql .= "INNER JOIN outcome o on r.outcome_id = o.outcome_id ";
        $sql .= "AND r.outcome_report_id = ? ";
        $sql .= "LIMIT 1";
        
        $options = array($id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth) : false;
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_outcome_chart()
    {

        $sql  = 'SELECT r.datetime, o.reason_outcome_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r inner join reason_outcome o ';
        $sql .= 'on o.reason_outcome_id = r.reason_outcome_id ';
        $sql .= 'group by r.datetime, o.reason_outcome_name';

        $sth = self::find_by_sql($sql);

        foreach ($sth as $record) {
            yield $record;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_daily_report()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'COUNT(o.outcome_name) AS number_of_outcome, o.outcome_name, ';
        $sql .= 'ro.reason_outcome_name FROM outcome_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, o.outcome_name, ro.reason_outcome_name ';
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
    public function view_daily_report_top()
    {   
        // global $db;

        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'count(o.outcome_name) AS number_of_outcome, o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, o.outcome_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 3';

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
    public function sum_outcome_report_overview_by_reason_outcome()
    {   
        // global $db;

        $sql  = 'SELECT r.datetime, g.gym_name, ro.reason_outcome_name, ';
        $sql .= 'ro.reason_outcome_id, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'COUNT(DISTINCT(o.outcome_id)) AS number_of_outcome ';
        $sql .= 'FROM outcome_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, ';
        $sql .= 'ro.reason_outcome_name, ro.reason_outcome_id ';
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
    public function sum_outcome_report_overview_by_reason_outcome_as_manager()
    {
        $sql  = 'SELECT g.gym_name, ro.reason_outcome_name, ';
        $sql .= 'ro.reason_outcome_id, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'COUNT(DISTINCT(o.outcome_id)) AS number_of_outcome ';
        $sql .= 'FROM outcome_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, ro.reason_outcome_name, ro.reason_outcome_id ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC';

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
    public function sum_outcome_report_overview_by_reason_outcome_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, ro.reason_outcome_name, ';
        $sql .= 'ro.reason_outcome_id, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'COUNT(DISTINCT(o.outcome_id)) AS number_of_outcome ';
        $sql .= 'FROM outcome_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, ro.reason_outcome_name, ro.reason_outcome_id ';
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
    public function view_outcome_report_as_manager()
    {   

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied, ';
        $sql .= 'count(o.outcome_name) AS number_of_outcome, o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, o.outcome_name ';
        $sql .= 'ORDER BY o.outcome_name collate "C"';

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
    public function view_outcome_report_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'ro.reason_outcome_name, o.outcome_name, ';
        $sql .= 'COUNT(o.outcome_name) AS number_of_outcome FROM outcome_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, ro.reason_outcome_name, o.outcome_name ';
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
    public function view_outcome_report_daterange_as_manager_top()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'count(o.outcome_name) AS number_of_outcome, o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, o.outcome_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 3';

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
    public function view_outcome_report_as_manager_top()
    {   

        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'count(o.outcome_name) AS number_of_outcome, o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, o.outcome_name ';
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
    public function view_daily_outcome()
    {

        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        // $sql .= 'WHERE r.datetime = ? ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name';

        $options = array($this->datetime, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $daily_outcome) {
            yield $daily_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_daily_outcome_by_reason()
    {

        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'ro.reason_outcome_name, r.reason_outcome_id ';
        $sql .= 'FROM outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, ';
        $sql .= 'ro.reason_outcome_name, r.reason_outcome_id ';

        $options = array($this->datetime, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $daily_outcome) {
            yield $daily_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by gym name
     */
    public function view_interval_outcome_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        // $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') = ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $daily_outcome) {
            yield $daily_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by gym name
     */
    public function view_interval_outcome_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'ro.reason_outcome_name, r.reason_outcome_id FROM outcome_report r ';
        $sql .= 'INNER JOIN gym g ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, ro.reason_outcome_name, r.reason_outcome_id ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $daily_outcome) {
            yield $daily_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_outcome_daterange()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_outcome()
    {   
        // global $db;

        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_outcome_as_manager()
    {   
        // global $db;

        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array($this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_outcome_daterange_as_manager()
    {
        $sql  = 'SELECT r.datetime, g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name ';
        $sql .= 'ORDER BY r.datetime, g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_summary_outcome_by_gym()
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_outcome_by_gym_daterange()
    {
        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_summary_outcome_by_gym_as_manager($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public function view_summary_outcome_by_gym_daterange_as_manager()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name ';
        $sql .= 'ORDER BY g.gym_name collate "C"';

        $options = array($this->start_date, $this->end_date, $this->gym_id);

        $sth = self::find_by_sql($sql, $options);

        foreach ($sth as $summary_outcome) {
            yield $summary_outcome;
        }
    }

    /**//**
     * Description
     * @return generator of outcome_record objects grouped by datetime
     */
    public static function view_report($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT r.datetime, g.gym_name, sum(r.price_paied) AS price_paied, ';
        $sql .= 'o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY r.datetime, g.gym_name, o.outcome_name ';
        $sql .= 'ORDER BY o.outcome_name collate "C"';

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
    public static function view_outcome_report($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied, ';
        $sql .= 'count(o.outcome_name) AS number_of_outcome, o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, o.outcome_name ';
        $sql .= 'ORDER BY o.outcome_name collate "C"';

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
    public function view_outcome_report_admin_daterange()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'COUNT(o.outcome_name) AS number_of_outcome, o.outcome_name, ';
        $sql .= 'ro.reason_outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'INNER JOIN reason_outcome ro ON r.reason_outcome_id = ro.reason_outcome_id ';
        $sql .= "WHERE date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') >= ? ";
        $sql .= "AND date_trunc('day', r.time AT TIME ZONE 'Europe/Athens') <= ? ";
        $sql .= 'AND r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, ro.reason_outcome_name, o.outcome_name ';
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
    public static function view_outcome_report_top($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied, ';
        $sql .= 'count(o.outcome_name) AS number_of_outcome, o.outcome_name FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'INNER JOIN outcome o ON r.outcome_id = o.outcome_id ';
        $sql .= 'WHERE r.gym_id = ? ';
        $sql .= 'GROUP BY g.gym_name, o.outcome_name ';
        $sql .= 'ORDER BY SUM(r.price_paied) DESC LIMIT 3';

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
    public static function view_outcome_report_by_gym($gym_id)
    {   
        // global $db;

        $sql  = 'SELECT g.gym_name, sum(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';
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
    public function view_outcome_report_daterange_by_gym()
    {
        $sql  = 'SELECT g.gym_name, SUM(r.price_paied) AS price_paied FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
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
     * @return array of income_record objects grouped by datetime
     */
    public static function find_outcome_report_by_provider_id($provider_id)
    {
        $sql  = 'SELECT COUNT(r.outcome_report_id) AS number_of_records FROM ';
        $sql .= 'outcome_report r INNER JOIN provider p ';
        $sql .= 'ON r.provider_id = p.provider_id ';
        $sql .= 'WHERE r.provider_id = ? ';

        $options = array($provider_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_records : false;
    }

    /**//**
     * Description
     * @return array of income_record objects grouped by datetime
     */
    public static function find_outcome_report_by_gym_id($gym_id)
    {
        $sql  = 'SELECT COUNT(r.outcome_report_id) AS number_of_records FROM ';
        $sql .= 'outcome_report r INNER JOIN gym g ';
        $sql .= 'ON r.gym_id = g.gym_id ';
        $sql .= 'WHERE r.gym_id = ? ';

        $options = array($gym_id);

        $sth = self::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->number_of_records : false;
    }

    /**//**
     * Description
     * @return array of outcome ids or false
     */
    public static function find_array_of_outcome_report_ids()
    {
        $result = [];
        $sql  = "SELECT outcome_report_id FROM ";
        $sql .= self::$table_name;
        $options = array();

        $sth = self::find_by_sql($sql);

        foreach ($sth as $outcome_report) {
            $result[] = $outcome_report->outcome_report_id;
        }

        return !empty($result) ? $result : false;
    }

    /**//**
     * Description validate user input
     * @return true or false
     */
    public function validate_user_input_outcome()
    {
        $passed_validation_tests = true;
        $msg  = "Fix the following error(s): ";
        $msg .="<ul style='text-align:left;margin-left:33%'>";
        
        $check_ap = has_presence($this->ap);
        $check_ap_numeric = has_number($this->ap);
        if (!$check_ap or !$check_ap_numeric) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "AP: ";
            $msg .= h($this->ap);
            $msg .= " cannot be blank and must be a number.";
            $msg .= "</li>";
        }
        // $check_apy_regex = Form::has_format_matching($apy, '/\A\d\Z/');
        $check_provider_id = has_presence($this->provider_id);
        $allowed_provider_ids = Provider::find_array_of_providers_ids();
        $check_provider_id_inclusion = has_inclusion_in($this->provider_id, $allowed_provider_ids);
        if (!$check_provider_id or !$check_provider_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Provider: ";
            $msg .= h($this->provider_id);
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
        $check_outcome_id = has_presence($this->outcome_id);
        $allowed_outcome_ids = Outcome::find_array_of_outcome_ids();
        $check_outcome_id_inclusion = has_inclusion_in($this->outcome_id, $allowed_outcome_ids);
        if (!$check_outcome_id or !$check_outcome_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Outcome: ";
            $msg .= h($this->outcome_id);
            $msg .= " cannot be blank and must be a valid choice.";
            $msg .= "</li>";
        }
        $check_price_paied = has_presence($this->price_paied);
        $check_price_paied_numeric = has_number($this->price_paied);
        if ($check_price_paied and !$check_price_paied_numeric) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Price paied: ";
            $msg .= h($this->price_paied);
            $msg .= " by provider must be a number.";
            $msg .= "</li>";
        } elseif (!$check_price_paied) {
            $price_paied = 0;
        }
        $check_reason_outcome_id = has_presence($this->reason_outcome_id);
        $check_reason_outcome_id_inclusion = has_inclusion_in($this->reason_outcome_id, [1, 2]);
        if (!$check_reason_outcome_id or !$check_reason_outcome_id_inclusion) {
            $passed_validation_tests = false;
            $msg .= "<li>";
            $msg .= "Reason for outcome: ";
            $msg .= h($this->reason_outcome_id);
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

