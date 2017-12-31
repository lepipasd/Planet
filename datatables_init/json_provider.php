<?php
require_once('../includes/initialize.php');

// $users = LoginUser::find_all();
$providers = Provider::find_providers_full_details();

$data = array();

foreach ($providers as $provider) {

    // $result = Gym::find_by_primary_key($user->gym_id);
    
    // $user->gym_name = $result->gym_name;
    
    array_push($data, $provider);
}

echo json_encode($data);
