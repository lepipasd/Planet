<?php
require_once('../includes/initialize.php');

$gyms = Gym::find_all();
$data = array();

foreach ($gyms as $gym) {
    array_push($data, $gym);
}
echo json_encode($data);
