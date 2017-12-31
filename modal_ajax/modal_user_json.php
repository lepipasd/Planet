<?php
require_once('../includes/initialize.php');

if (isset($_POST['id'])) {
    $login_user_id = $_POST['id'];
// $login_user_id = 2;

    $user = LoginUser::find_user_by_id($login_user_id);

    $gyms = Gym::find_all();

    $find_gym_by_id = Gym::find_by_primary_key($user->gym_id);
    $user->gym_name = $find_gym_by_id->gym_name;

    $json_data = array('user' => $user, 'gyms' => $gyms);

    echo json_encode($json_data);
}
