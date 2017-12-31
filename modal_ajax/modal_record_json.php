<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    $form_params = ['id'];
    $valid_form = new Form($form_params);
    $valid_post_params = $valid_form->allowed_post_params();
    $record_id = $valid_post_params['id'];

    $record = IncomeReport::find_Income_record_by_id($record_id);

    $gyms = Gym::find_all();

    $income = Income::find_array_of_income();


    $payment_method = PaymentMethod::find_array_of_payment_method();

    $attraction_income = AttractionIncome::find_array_of_attraction_income();

    $json_data = array('record' => $record, 'gyms' => $gyms,
        'income' => $income, 'payment_method' => $payment_method,
        'attraction_income' => $attraction_income);

    echo json_encode($json_data);
}

