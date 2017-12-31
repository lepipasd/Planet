<?php

require_once(LIB_PATH.DS.'database.php');

class Role extends DatabaseObject
{
    protected static $table_name = "role";
    protected static $primary_key = "role_id";
    // atributes one for each column
    public $role_id;
    public $role_name;

    public function save()
    {
        // A new record won't have licenceid
        return isset($this->role_id) ? $this->update() : $this->create();
    }

    public function create()
    {
        global $db;

        $sql = "INSERT INTO role (role_name) VALUES (?) RETURNING role_id";
        $options = array($this->role_name);

        $sth = $db->query($sql, $options);

        return $sth->fetch();
    }

    public function update()
    {
        global $db;

        $sql = "UPDATE role SET role_name = ? WHERE role_id = ?";
        $options = array($this->role_name, $this->role_id);

        $sth = $db->query($sql, $options);

        return ($db->num_rows($sth) == 1) ? true : false;
    }
}
