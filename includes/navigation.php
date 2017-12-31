<?php

require_once(LIB_PATH.DS.'database.php');

class Navigation extends DatabaseObject
{
    protected static $table_name = "page_access";
    protected static $primary_key = "page_id";
    protected static $foreign_key = "page_category";
    // atributes one for each column
    public $page_id;
    public $page_name;
    public $page_url;
    public $page_category;
    public $page_action;
    public $page_depth;
    public $role_id;
    public $image;
    public $message;

    public function save()
    {
        // A new record won't have licenceid
        return isset($this->page_id) ? $this->update() : $this->create();
    }

    public function create()
    {
        $sql = "INSERT INTO page_access (page_name, page_url, page_category, page_action, page_depth, role_id, image, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING page_id";
        $options = array($this->page_name, $this->page_url, $this->page_category, $this->page_action, $this->page_depth, $this->role_id, $this->image, $this->message);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    public function update()
    {
        $sql = "UPDATE page_access SET page_name = ?, page_url = ?, page_category = ?, page_action = ?, page_depth = ?, role_id = ?, image = ?, message = ? WHERE page_id = ?";
        $options = array($this->page_name, $this->page_url, $this->page_category, $this->page_action, $this->page_depth, $this->role_id, $this->image, $this->message, $this->page_id);

        $sth = static::find_by_sql($sql, $options);

        return !empty($sth) ? array_shift($sth)->{static::$primary_key} : false;
    }

    public static function display_menu($depth = 0, $page_category = 2, $role_id)
    {
        $menu_admin = '';
        $menu_admin .= '<li class="dropdown">';
        $menu_admin .= '<div class="btn-group" style="padding-top: 7px; padding-right: 10px">';

        $administration_menu = self::find_by_foreign_key($page_category);
        usort($administration_menu, function($a, $b) {
            return strcmp($a->page_depth, $b->page_depth);
        });
        while (list($key, $menu) = each($administration_menu)) {
            if ($menu->page_action and ($menu->page_depth == $depth)) {
                $menu_admin .= '<a href="';
                $menu_admin .= u($menu->page_url);
                $menu_admin .= '" class="btn btn-default">';
                $menu_admin .= h($menu->page_name);
                $menu_admin .= '</a>';
                $menu_admin .= '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $menu_admin .= '<span class="caret"></span>';
                $menu_admin .= '</button>';
                $menu_admin .= '<ul class="dropdown-menu">';
            } elseif ($menu->page_action and ($menu->page_depth == ($depth + 1)) and ($menu->role_id <= $role_id)) {
                $menu_admin .= '<li>';
                $menu_admin .= '<a href="';
                $menu_admin .= u($menu->page_url);
                $menu_admin .= '">';
                $menu_admin .= h($menu->page_name);
                $menu_admin .= '</a></li>';
            }
        }
        $menu_admin .= '</ul>';
        $menu_admin .= '</div>';
        $menu_admin .= '</li>';

        return $menu_admin;
    }
    
    public static function display_administration_pills($depth=0, $page_category=1, $role_id)
    {
        $pills_admin  = '';
        $pills_admin .= '<div id="pill_btn" class="container';
        if ($depth == 0 and $role_id == 1) {
            $pills_admin .= ' col-md-4">';
        } elseif ($depth == 0 and $role_id != 1) {
            $pills_admin .= ' col-md-3">';
        } else {
            $pills_admin .= '">';
        }
        $pills_admin .= '<nav id="pills_menu" class="navbar navbar-default">';
        $pills_admin .= '<ul class="nav nav-pills nav-justified">';

        $administration_pills = static::find_by_foreign_key($page_category);

        while (list($key, $pill) = each($administration_pills)) {
            if ($pill->page_action and ($pill->page_depth == $depth) and ($pill->role_id <= $role_id)) {
                if ($depth == 1) {
                    $pills_admin .= '<li style="border-width: 1px;border-style: solid;border-color:white">';
                } else {
                    $pills_admin .= '<li>';
                }
                $pills_admin .= '<a href="';
                $pills_admin .= u($pill->page_url);
                $pills_admin .= '">';
                $pills_admin .= '<h3 style="padding-top: 20px;padding-bottom: 5px;">';
                $pills_admin .= h($pill->page_name);
                $pills_admin .= ' <i class="';
                $pills_admin .= h($pill->image);
                $pills_admin .= '"></i></h3>';
                $pills_admin .= '<span class="col-md-12" style="background-color: #ffa834; height: 3px;margin-top:10px;margin-bottom: 20px"></span>';
                $pills_admin .= '<p style="text-align:left;font-size:13px"><b>';
                $pills_admin .= h($pill->message);
                $pills_admin .= '</b></p>';
                $pills_admin .= '</a></li>';
            }
        }
        $pills_admin .= '</ul>';
        $pills_admin .= '</nav>';
        $pills_admin .= '</div>';

        return $pills_admin;
    }
}

