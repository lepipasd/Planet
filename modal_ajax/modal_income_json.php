<?php
require_once('../includes/initialize.php');
if (request_is_same_domain() and request_is_post()) {
    $form_params = ['id'];
    $valid_form = new Form($form_params);
    $valid_post_params = $valid_form->allowed_post_params();

    $income_id = $valid_post_params['id'];

    $income = Income::find_by_primary_key($income_id);

    $json_data = array('income' => $income);

    echo json_encode($json_data);
}

