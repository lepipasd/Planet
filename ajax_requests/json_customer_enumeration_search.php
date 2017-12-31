<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_get()) {
    $data = array();

    $term = $_GET["term"];

    $customers = Customer::find_customers_enumeration_by_term($term);

    foreach ($customers as $customer) {
        $cst = array();
        $cst['id'] = $customer->id;
        $cst['name'] = $customer->name;
        $cst['text'] = $customer->name;
        $cst['telephone'] = $customer->telephone;
    
        array_push($data, $cst);
    }

    echo json_encode(array("results" => $data));
}
