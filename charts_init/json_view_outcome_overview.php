<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    $form_params_view_outcome = ['id', 'start_date', 'end_date'];
    $valid_form = new Form($form_params_view_outcome);
    $valid_post_params = $valid_form->allowed_post_params();
    if ($session->role_id == 1) {
        $outcome_report = new OutcomeReport();
        $outcome_report_by_reason_outcome = new OutcomeReport();

        $outcome_report->datetime = date('Y-m-d');
        $outcome_report->gym_id = $session->gym_id;

        $outcome_report_by_reason_outcome->datetime = date('Y-m-d');
        $outcome_report_by_reason_outcome->gym_id = $session->gym_id;

        $outcome_reports = $outcome_report->view_daily_report_top();
        $sum_outcome_reports_by_reason_outcome =
        $outcome_report_by_reason_outcome->sum_outcome_report_overview_by_reason_outcome();
    } elseif ($session->role_id == 2) {
        $outcome_report = new OutcomeReport();
        $outcome_report_by_reason_outcome = new OutcomeReport();

        $outcome_report->gym_id = $session->gym_id;
        $outcome_report->start_date = $valid_post_params['start_date'];
        $outcome_report->end_date = $valid_post_params['end_date'];

        $outcome_report_by_reason_outcome->gym_id = $session->gym_id;
        $outcome_report_by_reason_outcome->start_date = $valid_post_params['start_date'];
        $outcome_report_by_reason_outcome->end_date = $valid_post_params['end_date'];

        $outcome_reports = $outcome_report->view_outcome_report_daterange_as_manager_top();
        $sum_outcome_reports_by_reason_outcome =
        $outcome_report_by_reason_outcome->
        sum_outcome_report_overview_by_reason_outcome_daterange_as_manager();
    } else {
        $outcome_report = new OutcomeReport();
        $outcome_report->gym_id = $valid_post_params['id'];
        $outcome_report->start_date = $valid_post_params['start_date'];
        $outcome_report->end_date = $valid_post_params['end_date'];
        
        $outcome_reports = $outcome_report->view_outcome_report_daterange_as_manager_top();
        $sum_outcome_reports_by_reason_outcome =
        $outcome_report->
        sum_outcome_report_overview_by_reason_outcome_daterange_as_manager();
    }

    $data = array();
    $data_table = array();

    foreach ($sum_outcome_reports_by_reason_outcome as $sum_by_reason) {
        $data_table['gym_name'] = $sum_by_reason->gym_name;
        if ($sum_by_reason->reason_outcome_id == 2) {
            $data_table['daily_outcome_price'] = floatval($sum_by_reason->price_paied);
            $data_table['number_of_daily_outcome'] = intval($sum_by_reason->number_of_outcome);
        } elseif ($sum_by_reason->reason_outcome_id == 1) {
            $data_table['general_outcome_price'] = floatval($sum_by_reason->price_paied);
            $data_table['number_of_general_outcome'] = intval($sum_by_reason->number_of_outcome);
        }
    }

    foreach ($outcome_reports as $outcome_report) {

        $data[$outcome_report->outcome_name]['outcome'] = floatval($outcome_report->price_paied);
    }

    echo json_encode(array("chart" => $data, "table" => $data_table));    
}

