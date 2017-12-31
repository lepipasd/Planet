<?php
require_once('../includes/initialize.php');
if (request_is_same_domain() and request_is_post()) {
    $form_params_view_report = ['start_date', 'end_date'];
    $valid_form = new Form($form_params_view_report);
    $valid_post_params = $valid_form->allowed_post_params();

    $income_report = new IncomeReport();
    $income_report->start_date = $valid_post_params['start_date'];
    $income_report->end_date = $valid_post_params['end_date'];

    $outcome_report = new OutcomeReport();
    $outcome_report->start_date = $valid_post_params['start_date'];
    $outcome_report->end_date = $valid_post_params['end_date'];
    if ($session->role_id == 2) {
        $outcome_report->gym_id = $session->gym_id;
        
        $outcome_reports = $outcome_report->view_summary_outcome_daterange_as_manager();

        $income_report->gym_id = $session->gym_id;

        $income_reports = $income_report->view_summary_income_daterange_as_manager();

        $chart_outcome = $outcome_report->view_summary_outcome_by_gym_daterange_as_manager();
        $chart_income = $income_report->view_summary_income_by_gym_daterange_as_manager();
    } elseif ($session->role_id == 4) {
        $outcome_reports = $outcome_report->view_summary_outcome_daterange();
        $income_reports = $income_report->view_summary_income_daterange();

        $chart_outcome = $outcome_report->view_summary_outcome_by_gym_daterange();
        $chart_income = $income_report->view_summary_income_by_gym_daterange();
    }

    $data_outcome = array();
    $data_income = array();
    $data_chart = array();

    foreach ($outcome_reports as $outcome_report) {
        $arrayOutcome = array('datetime' => $outcome_report->datetime, 'gym_name' => $outcome_report->gym_name,
            'outcome_price' => floatval($outcome_report->price_paied), 'income_price' => 0);
        array_push($data_outcome, $arrayOutcome);
    }

    foreach ($income_reports as $income_report) {
        $arrayIncome = array('datetime' => $income_report->datetime,
            'gym_name' => $income_report->gym_name, 'outcome_price' => 0,
            'income_price' => floatval($income_report->price_paied));

        array_push($data_income, $arrayIncome);
    }

    foreach ($chart_outcome as $outcome) {
        $data_chart[$outcome->gym_name]['outcome'] = floatval($outcome->price_paied);
    }

    foreach ($chart_income as $income) {
        $data_chart[$income->gym_name]['income_paied'] = floatval($income->price_paied);
    }

    echo json_encode(array("outcome" => $data_outcome, "income" => $data_income,
        "chart" => $data_chart ));
}


