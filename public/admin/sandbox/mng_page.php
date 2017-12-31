<?php
require_once('../../includes/initialize.php');

// $menu1 = "";

// 		$administration_menu = Navigation::find_by_foreign_key(1);

// 		while(list($key, $menu) = each($administration_menu)) {

//             if($menu->page_action) {

//                $menu1 .= '<li><a href="' . $menu->page_url . '">' . $menu->page_name . '</a></li>';
//                }
//            }
// echo $menu1;
  //          echo "<pre>";
  //          print_r($menu1);
  //          echo "<pre/>";
echo $navigation_admin = Navigation::display_administration_menu();
