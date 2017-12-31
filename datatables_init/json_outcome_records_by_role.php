<?php
require_once('../includes/initialize.php');

if ($session->role_id == 1) {

    $outcome_report = new OutcomeReport();
    $outcome_report->datetime = date('Y-m-d');
    $outcome_report->gym_id = $session->gym_id;

    $records = $outcome_report->find_outcome_records_full_details_by_role();
} elseif ($session->role_id == 2) {
    $outcome_report = new OutcomeReport();
    $outcome_report->gym_id = $session->gym_id;

    $records = $outcome_report->find_outcome_records_full_details_as_manager();
} else {
    $records = OutcomeReport::find_outcome_records_full_details();
}
$data = array();

foreach ($records as $record) {
    array_push($data, $record);
}

echo json_encode($data);



