<?php
require_once('../includes/initialize.php');

// $users = LoginUser::find_all();
$customers = Customer::find_customers_enumeration();

$data = array();

foreach ($customers as $customer) {
    // $data[$customer->id] = array('name' => $customer->name, 'telephone' => $customer->telephone);
    $data[$customer->id] = $customer->name . " ( "  . $customer->telephone . " )";
}

echo json_encode($data);
