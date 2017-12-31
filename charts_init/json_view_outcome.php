<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    $form_params_view_outcome = ['id', 'start_date', 'end_date'];
    $valid_form = new Form($form_params_view_outcome);
    $valid_post_params = $valid_form->allowed_post_params();
    if ($session->role_id == 1) {
        $outcome_report = new OutcomeReport();

        $outcome_report->datetime = date('Y-m-d');
        $outcome_report->gym_id = $session->gym_id;

        $outcome_reports = $outcome_report->view_daily_report();
    } elseif ($session->role_id == 2) {
        $outcome_report = new OutcomeReport();

        $outcome_report->gym_id = $session->gym_id;

        $outcome_report->start_date = $valid_post_params['start_date'];
        $outcome_report->end_date = $valid_post_params['end_date'];

        $outcome_reports = $outcome_report->view_outcome_report_daterange_as_manager();
    } else {
        $outcome_report = new OutcomeReport();

        $outcome_report->gym_id = $valid_post_params['id'];

        $outcome_report->start_date = $valid_post_params['start_date'];
        $outcome_report->end_date = $valid_post_params['end_date'];

        $outcome_reports = $outcome_report->view_outcome_report_admin_daterange();
    }

    $data_chart = array();
    $data_table = array();

    foreach ($outcome_reports as $outcome_report) {
        array_push($data_table, $outcome_report);
        if ($outcome_report->reason_outcome_name == "ΗΜΕΡΗΣΙΟ ΤΑΜΕΙΟ") {
            $data_chart["daily"][$outcome_report->outcome_name]['outcome'] =
            floatval($outcome_report->price_paied);
        } else {
            $data_chart["general"][$outcome_report->outcome_name]['outcome'] =
            floatval($outcome_report->price_paied);
        }
    }
    echo json_encode(array("chart" => $data_chart, "table" => $data_table));
}

