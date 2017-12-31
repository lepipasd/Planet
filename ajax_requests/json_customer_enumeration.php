<?php
require_once('../includes/initialize.php');

// mb_internal_encoding("UTF-8");

// $users = LoginUser::find_all();
$customers = Customer::find_customers_enumeration();

$data = array();

$term = mb_strtoupper($_GET["term"]);

$length = mb_strlen($term);

foreach ($customers as $customer) {
    if (mb_substr($customer->name, 0, $length, 'UTF-8') ===$term) {
        $cst = array();
        $cst['id'] = $customer->id;
        $cst['name'] = $customer->name;
        $cst['text'] = $customer->name;
        $cst['telephone'] = $customer->telephone;
        // $result = Gym::find_by_primary_key($user->gym_id);
    
        // $user->gym_name = $result->gym_name;
    
        array_push($data, $cst);
    }
}

// echo json_encode($data);
echo json_encode(array("results" => $data));
