<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    if ($session->role_id == 1) {
        $income_report = new IncomeReport();
        $income_report->datetime = date('Y-m-d');
        $income_report->gym_id = $session->gym_id;

        $records = $income_report->find_records_full_details_by_role();
    } elseif ($session->role_id == 2) {
        $income_report = new IncomeReport();
        $income_report->gym_id = $session->gym_id;

        $records = $income_report->find_records_full_details_as_manager();
    } else {
        $records = IncomeReport::find_records_full_details();
    }

    $data = array();

    foreach ($records as $record) {
        array_push($data, $record);
    }

    echo json_encode($data);
}

