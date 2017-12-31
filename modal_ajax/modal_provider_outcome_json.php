<?php
require_once('../includes/initialize.php');

if (isset($_POST['id'])) {
    $provider_id = $_POST['id'];
// $provider_id = 2;

    $provider = Provider::find_provider_full_details_by_id($provider_id);

    $gyms = Gym::find_all();

    $outcome = Outcome::find_array_of_outcome();


    // $find_gym_by_id = Gym::find_by_primary_key($user->gym_id);
    // $user->gym_name = $find_gym_by_id->gym_name;

    $json_data = array('provider' => $provider, 'gyms' => $gyms, 'outcome' => $outcome);

    echo json_encode($json_data);
}
