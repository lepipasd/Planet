<?php

require_once(LIB_PATH.DS.'database.php');

class Form extends DatabaseObject
{
    public $allowed_params = [];
    public $allowed_array = [];

    public function __construct($params = [])
    {
        $this->allowed_params = $params;
    }

    public function allowed_post_params()
    {
        foreach ($this->allowed_params as $param) {
            if (isset($_POST[$param])) {
                $allowed_array[$param] = trim($_POST[$param]);
            } else {
                $allowed_array[$param] = null;
            }
        }
        return $allowed_array;
    }
}

