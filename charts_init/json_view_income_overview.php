<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    $form_params_view_income = ['id', 'start_date', 'end_date'];
    $valid_form = new Form($form_params_view_income);
    $valid_post_params = $valid_form->allowed_post_params();
    if ($session->role_id == 1) {
        $income_report = new IncomeReport();
        $sum_income_reports = new IncomeReport();
        $sum_income_of_cash_reports = new IncomeReport();

        $income_report->datetime = date('Y-m-d');
        $income_report->gym_id = $session->gym_id;

        $sum_income_reports->datetime = date('Y-m-d');
        $sum_income_reports->gym_id = $session->gym_id;

        $sum_income_of_cash_reports->datetime = date('Y-m-d');
        $sum_income_of_cash_reports->gym_id = $session->gym_id;

        $income_reports = $income_report->view_income_report_overview();
        $sum_by_gym = $sum_income_reports->sum_income_report_overview();
        $sum_of_cash_by_gym = $sum_income_of_cash_reports->sum_income_report_overview_cash();
        # for datatable - reception view
        $records = $income_report->find_records_full_details_by_role();
    } elseif ($session->role_id == 2) {
        $sum_income_reports = new IncomeReport();

        $sum_income_reports->gym_id = $session->gym_id;

        $sum_income_reports->start_date = $valid_post_params['start_date'];
        $sum_income_reports->end_date = $valid_post_params['end_date'];

        $sum_by_gym = $sum_income_reports->sum_income_report_overview_as_manager_daterange();
        $sum_of_cash_by_gym =
        $sum_income_reports->sum_income_report_overview_cash_as_manager_daterange();

        $income_reports = $sum_income_reports->view_income_report_overview_admin_daterange();

        $records = $sum_income_reports->find_records_full_details_as_manager();
    } else {
        $sum_income_reports = new IncomeReport();

        $sum_income_reports->gym_id = $valid_post_params['id'];

        $sum_income_reports->start_date = $valid_post_params['start_date'];
        $sum_income_reports->end_date = $valid_post_params['end_date'];

        $sum_by_gym = $sum_income_reports->sum_income_report_overview_as_manager_daterange();
        $sum_of_cash_by_gym =
        $sum_income_reports->sum_income_report_overview_cash_as_manager_daterange();

        $income_reports = $sum_income_reports->view_income_report_overview_admin_daterange();

        $records = $sum_income_reports->find_records_full_details_as_manager();
    }
    $data = array();
    $data_table = array();
    # initialize datatable
    $table = array();
    foreach ($records as $record) {
        array_push($table, $record);
    }

    $data_table['gym_name'] = $sum_by_gym->gym_name;
    $data_table['number_of_income'] = intval($sum_by_gym->number_of_income);
    $data_table['price_agreed'] = floatval($sum_by_gym->price_agreed);
    $data_table['price_paied'] = floatval($sum_by_gym->price_paied);
    if ($sum_of_cash_by_gym) {
        $data_table['price_paied_cash'] = floatval($sum_of_cash_by_gym->price_paied);
    } else {
        $data_table['price_paied_cash'] = 0.0;
    }

    foreach ($income_reports as $income_report) {
        $data[$income_report->income_name]['datetime'] = $income_report->datetime;
        $data[$income_report->income_name]['gym_name'] = $income_report->gym_name;
        $data[$income_report->income_name]['income_agreed'] = floatval($income_report->price_agreed);
        $data[$income_report->income_name]['income'] = floatval($income_report->price_paied);
    }
    echo json_encode(array("chart" => $data, "table" => $data_table, "init_table" => $table));
}

