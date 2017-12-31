<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    $form_params_view_income = ['id', 'start_date', 'end_date'];
    $valid_form = new Form($form_params_view_income);
    $valid_post_params = $valid_form->allowed_post_params();
    if ($session->role_id == 1) {
        $income_report = new IncomeReport();

        $income_report->datetime = date('Y-m-d');
        $income_report->gym_id = $session->gym_id;

        $income_reports = $income_report->view_daily_income_report();
    } elseif ($session->role_id == 2) {
        $income_report = new IncomeReport();

        $income_report->gym_id = $session->gym_id;

        $income_report->start_date = $valid_post_params['start_date'];
        $income_report->end_date = $valid_post_params['end_date'];

        $income_reports = $income_report->view_income_report_datepicker_as_manager();
    } else {
        $income_report = new IncomeReport();

        $income_report->gym_id = $valid_post_params['id'];

        $income_report->start_date = $valid_post_params['start_date'];
        $income_report->end_date = $valid_post_params['end_date'];

        $income_reports = $income_report->view_income_report_admin_daterange();
        // $income_reports = IncomeReport::view_income_report_admin($gym_id);
    }


    $data = array();
    $data_table = array();

    foreach ($income_reports as $income_report) {
        $data[$income_report->income_name]['datetime'] = $income_report->datetime;
        $data[$income_report->income_name]['gym_name'] = $income_report->gym_name;
        $data[$income_report->income_name]['income_agreed'] = floatval($income_report->price_agreed);
        $data[$income_report->income_name]['income'] = floatval($income_report->price_paied);
        array_push($data_table, $income_report);
    }
    echo json_encode(array("chart" => $data, "table" => $data_table));
}

