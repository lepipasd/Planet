<?php
require_once('../includes/initialize.php');

if (isset($_POST['id'])) {
    $record_id = $_POST['id'];
// $record_id = 2;

    $record = OutcomeReport::find_outcome_record_by_id($record_id);

    $gyms = Gym::find_all();

    $outcome = Outcome::find_array_of_outcome();


    

    // $find_gym_by_id = Gym::find_by_primary_key($user->gym_id);
    // $user->gym_name = $find_gym_by_id->gym_name;

    $json_data = array('record' => $record, 'gyms' => $gyms, 'outcome' => $outcome);

    echo json_encode($json_data);
}
