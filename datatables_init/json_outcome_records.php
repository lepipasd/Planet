<?php
require_once('../includes/initialize.php');

$records = OutcomeReport::find_outcome_records_full_details();

$data = array();

foreach ($records as $record) {
    array_push($data, $record);
}

echo json_encode($data);

// echo "<pre>";
// print_r($records);
// echo "</pre>";;
