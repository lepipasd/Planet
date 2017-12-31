<?php
require_once('../includes/initialize.php');

if ($session->role_id == 4) {
    $users = LoginUser::find_users_no_pwd();
} else {
    $users = LoginUser::find_users_by_role($session->gym_id);
}
$data = array();

foreach ($users as $user) {
    array_push($data, $user);
}

echo json_encode($data);

