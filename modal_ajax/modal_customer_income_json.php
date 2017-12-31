<?php
require_once('../includes/initialize.php');

if (isset($_POST['id'])) {
    $customer_id = $_POST['id'];
// $customer_id = 2;

    $customer = Customer::find_customer_full_details_by_id($customer_id);

    $gyms = Gym::find_all();

    $income = Income::find_array_of_income();


    $payment_method = PaymentMethod::find_array_of_payment_method();

    $attraction_income = AttractionIncome::find_array_of_attraction_income();

    // $find_gym_by_id = Gym::find_by_primary_key($user->gym_id);
    // $user->gym_name = $find_gym_by_id->gym_name;

    $json_data = array('customer' => $customer, 'gyms' => $gyms, 'income' => $income, 'payment_method' => $payment_method, 'attraction_income' => $attraction_income);

    echo json_encode($json_data);
}
