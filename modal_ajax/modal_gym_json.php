<?php
require_once('../includes/initialize.php');

if (isset($_POST['id'])) {
    $gym_id = $_POST['id'];

    $gym = Gym::find_by_primary_key($gym_id);

    $users = Gym::find_users_by_gym_id($gym_id);

    $json_data = array('gym' => $gym, 'users' => $users);

    echo json_encode($json_data);
}
