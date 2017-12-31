<?php
require_once('../includes/initialize.php');

// $users = LoginUser::find_all();
$customers = Customer::find_customers_full_details();

$data = array();

foreach ($customers as $customer) {

    // $result = Gym::find_by_primary_key($user->gym_id);
    
    // $user->gym_name = $result->gym_name;
    
    array_push($data, $customer);
}

echo json_encode($data);
