<?php

class Session
{
    private $logged_in = false;
    public $login_user_id;
    public $role_id;
    public $role_name;
    public $gym_id;
    public $gym_name;
    public $real_name;
    public $message;

    // store last activity timestamp
    public $last_activity;
    public $last_login;

    // CRSF protection
    public $csrf_token;
    public $csrf_token_time;

    // prevent session highjacking and fixation
    public $session_ip;
    public $user_agent;

    public function __construct()
    {
        session_start();
        
        $this->check_message();
        $this->check_login();
    }

    public function is_logged_in()
    {
        return $this->logged_in;
    }

    public function login($user)
    {
        // database should find user based on licenceid/password
        if ($user) {
            session_regenerate_id();
            $this->login_user_id = $_SESSION['login_user_id'] = $user->login_user_id;
            $this->role_id = $_SESSION['role_id'] = $user->role_id;
            $this->role_name = $_SESSION['role_name'] = $user->role_name;
            $this->gym_id = $_SESSION['gym_id'] = $user->gym_id;
            $this->gym_name = $_SESSION['gym_name'] = $user->gym_name;
            $this->real_name = $_SESSION['real_name'] = $user->name . " " . $user->surname;
            $this->logged_in = true;
            $this->create_csrf_token();
            // prevent session hijacking and fixation
            $this->session_ip = $_SESSION['session_ip'] = $_SERVER['REMOTE_ADDR'];
            $this->user_agent = $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

            $this->last_login = $_SESSION['last_login'] = time();
            $this->last_activity = $_SESSION['last_activity'] = time();

            $log_msg  = "User ";
            $log_msg .= $this->real_name;
            $log_msg .= " with ID: ";
            $log_msg .= $this->login_user_id;
            $log_msg .= " as ";
            $log_msg .= $this->role_name;
            $log_msg .= " logged in to gym: ";
            $log_msg .= $this->gym_name;
            logger("INFO:", $log_msg);
        }
    }

    public function logout()
    {
        $log_msg  = "User ";
        $log_msg .= $this->real_name;
        $log_msg .= " with ID: ";
        $log_msg .= $this->login_user_id;
        $log_msg .= " as ";
        $log_msg .= $this->role_name;
        $log_msg .= " logged out from gym: ";
        $log_msg .= $this->gym_name;
        unset($this->login_user_id);
        unset($this->role_id);
        unset($this->role_name);
        unset($this->gym_id);
        unset($this->gym_name);
        unset($this->real_name);
        $this->destroy_csrf_token();
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        $this->logged_in = false;
        $this->end_session();
        logger("INFO:", $log_msg);
    }

    /**//**
     * Description compatibility with all browsers.
     * @return destroy session
     */
    public function end_session()
    {
        session_unset();
        session_destroy();
    }

    /**//**
     * Description Does the request IP match the stored value.
     * @return true or false
     */
    public function request_ip_matches_session()
    {
        if (!isset($this->session_ip) || !isset($_SERVER['REMOTE_ADDR'])) {
            return false;
        }
        if ($this->session_ip === $_SERVER['REMOTE_ADDR']) {
            return true;
        } else {
            return false;
        }
    }

    /**//**
     * Description Does the request user-agent match the stored value.
     * @return true or false
     */
    public function request_user_agent_matches_session()
    {
        if (!isset($this->user_agent) || !isset($_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }
        if ($this->user_agent === $_SERVER['HTTP_USER_AGENT']) {
            return true;
        } else {
            return false;
        }
    }

    /**//**
     * Description Has too much time passed since last login.
     * @return true or false
     */
    public function last_login_is_recent()
    {
        $max_elapsed = 60 * 60 * 24;
        if (!isset($this->last_login)) {
            return false;
        }
        if (($this->last_login + $max_elapsed) >= time()) {
            return true;
        } else {
            return false;
        }
    }

     /**//**
     * Description Has too much time passed since last login.
     * @return true or false
     */
    public function last_activity_is_recent()
    {
        $max_elapsed = 60 * 30;
        if (!isset($this->last_activity)) {
            return false;
        }
        if (($this->last_activity + $max_elapsed) >= time()) {
            return true;
        } else {
            return false;
        }
    }

    /**//**
     * Description Should the session considered valid.
     * @return true or false
     */
    public function is_session_valid()
    {
        if (!$this->last_activity_is_recent()) {
            return false;
        } else {
            $this->last_activity = $_SESSION['last_activity'] = time();
        }
        if (!$this->request_ip_matches_session()) {
            return false;
        }
        if (!$this->request_user_agent_matches_session()) {
            return false;
        }
        if (!$this->last_login_is_recent()) {
            return false;
        }
        return true;
    }

    public function message($msg = "")
    {
        if (!empty($msg)) {
            // then this is "set message"
            // make sure you understand why $this->message = $msg wouldn't work
            $_SESSION['message'] = $msg;
        } else {
            // then this is "get message"
            return $this->message;
        }
    }

    private function check_login()
    {
        if (isset($_SESSION['login_user_id'])) {
            $this->login_user_id = $_SESSION['login_user_id'];
            $this->role_id = $_SESSION['role_id'];
            $this->role_name = $_SESSION['role_name'];
            $this->gym_id = $_SESSION['gym_id'];
            $this->gym_name = $_SESSION['gym_name'];
            $this->real_name = $_SESSION['real_name'];
            $this->logged_in = true;
            $this->csrf_token = $_SESSION['csrf_token'];
            $this->csrf_token_time = $_SESSION['csrf_token_time'];

            $this->session_ip = $_SESSION['session_ip'];
            $this->user_agent = $_SESSION['user_agent'];

            $this->last_login = $_SESSION['last_login'];
            $this->last_activity = $_SESSION['last_activity'];
        } else {
            unset($this->login_user_id);
            unset($this->role_id);
            unset($this->role_name);
            unset($this->gym_id);
            unset($this->gym_name);
            unset($this->real_name);
            $this->logged_in = false;
        }
    }

    private function check_message()
    {
        // Is there a message stored in the session?
        if (isset($_SESSION['message'])) {
            // Add it as an attribute and erase the stored version
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        } else {
            $this->message = "";
        }
    }

    /**//**
     * Description generate a token for use with CSRF protection.
     * @return token
     */
    private static function csrf_token()
    {
        return md5(uniqid(rand(), true));
    }

    /**//**
     * Description generate and store CSRF token in usr session.
     * Requires session to have been started already
     * @return string
     */
    private function create_csrf_token()
    {
        $token = static::csrf_token();
        $this->csrf_token = $_SESSION['csrf_token'] = $token;
        $this->csrf_token_time = $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    /**//**
     * Description returns an html tag including the CSRF token
     * for use in a form
     * Usage: csrf_token_tag();
     * @return HTML tag
     */
    public function csrf_token_tag()
    {
        // $token = $this->create_csrf_token();
        $token = $this->csrf_token;
        $tag  = "<input type=\"hidden\" name=\"csrf_token\" value=\"";
        $tag .= $token;
        $tag .= "\">";
        return $tag;
    }

    /**//**
     * Description returns an html tag including the CSRF token_time
     * for use in a form
     * Usage: csrf_token_tag_time();
     * @return HTML tag
     */
    public function csrf_token_tag_time()
    {
        // $token = $this->create_csrf_token();
        $token_time = $this->csrf_token_time;
        $tag  = "<input type=\"hidden\" name=\"csrf_token_time\" value=\"";
        $tag .= $token_time;
        $tag .= "\">";
        return $tag;
    }

    /**//**
     * Description destroys token by removing it from the session.
     * @return token
     */
    public function destroy_csrf_token()
    {
        $_SESSION['csrf_token'] = null;
        $this->csrf_token = null;
        $_SESSION['csrf_token_time'] = null;
        $this->csrf_token_time = null;
        return true;
    }

    /**//**
     * Description returns true if user-submitted POST token is
     * identical to the previously stored SESSION token, false otherwise
     * @return true or false
     */
    public function csrf_token_is_valid()
    {
        if (isset($_POST['csrf_token'])) {
            $user_token = $_POST['csrf_token'];
            $stored_token = $this->csrf_token;
            return $user_token === $stored_token;
        } else {
            return false;
        }
    }

    /**//**
     * Description returns true if user-submitted POST token is recent
     * @return true or false
     */
    public function csrf_token_is_recent()
    {
        $max_elapsed = 60*60*24; // 1 day
        if (isset($_POST['csrf_token_time'])) {
            $stored_time = $this->csrf_token_time;
            return ($stored_time + $max_elapsed) > time();
        } else {
            // remove expired token
            $this->destroy_csrf_token();
            return false;
        }
    }
}

$session = new Session();
$message = $session->message();

