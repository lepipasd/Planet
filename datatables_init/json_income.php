<?php
require_once('../includes/initialize.php');
if (request_is_same_domain() and request_is_post()) {
    $incomes = Income::find_all();
    $data = array();

    foreach ($incomes as $income) {
        array_push($data, $income);
    }
    echo json_encode($data);
}

