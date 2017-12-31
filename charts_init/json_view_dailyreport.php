<?php
require_once('../includes/initialize.php');

if (request_is_same_domain() and request_is_post()) {
    $form_params_view_report = ['id', 'start_date', 'end_date'];
    $valid_form = new Form($form_params_view_report);
    $valid_post_params = $valid_form->allowed_post_params();

    if ($session->role_id == 1) {
        $gym = Gym::find_by_primary_key($session->gym_id);
        $outcome_report = new OutcomeReport();

        $outcome_report->datetime = date('Y-m-d');
        $outcome_report->gym_id = $session->gym_id;

        $outcome_reports = $outcome_report->view_daily_outcome_by_reason();

        $income_report = new IncomeReport();

        $income_report->datetime = date('Y-m-d');
        $income_report->gym_id = $session->gym_id;

        $income_reports = $income_report->view_daily_income();
        $income_reports_by_payment_method = $income_report->view_interval_income_by_payment_method_daterange_as_user();
    } elseif ($session->role_id == 2) {
        $gym = Gym::find_by_primary_key($session->gym_id);
        $outcome_report = new OutcomeReport();

        $outcome_report->gym_id = $session->gym_id;
        $outcome_report->start_date = $valid_post_params['start_date'];
        $outcome_report->end_date = $valid_post_params['end_date'];

        $outcome_reports = $outcome_report->view_interval_outcome_daterange_as_manager();

        $income_report = new IncomeReport();

        $income_report->gym_id = $session->gym_id;
        $income_report->start_date = $valid_post_params['start_date'];
        $income_report->end_date = $valid_post_params['end_date'];

        $income_reports = $income_report->view_interval_income_daterange_as_manager();

        $income_reports_by_payment_method =
        $income_report->view_interval_income_by_payment_method_daterange_as_manager();
    } else {
        $gym = Gym::find_by_primary_key($valid_post_params['id']);
        $outcome_report = new OutcomeReport();

        $outcome_report->gym_id = $valid_post_params['id'];

        $outcome_report->start_date = $valid_post_params['start_date'];
        $outcome_report->end_date = $valid_post_params['end_date'];

        $outcome_reports = $outcome_report->view_interval_outcome_daterange_as_manager();

        $income_report = new IncomeReport();

        $income_report->gym_id = $valid_post_params['id'];

        $income_report->start_date = $valid_post_params['start_date'];
        $income_report->end_date = $valid_post_params['end_date'];

        $income_reports = $income_report->view_interval_income_daterange_as_manager();

        $income_reports_by_payment_method =
        $income_report->view_interval_income_by_payment_method_daterange_as_manager();
    }
    $data = array();
    $data_table = array();

    // Initializition
    $data[$gym->gym_name]['outcome_daily'] = 0;
    $data[$gym->gym_name]['outcome_general'] = 0;
    $data[$gym->gym_name]['income_paied'] = 0;
    $data[$gym->gym_name]['cash'] = 0;
    $data[$gym->gym_name]['free'] = 0;
    $data[$gym->gym_name]['paypal'] = 0;
    $data[$gym->gym_name]['cheapis'] = 0;
    $data[$gym->gym_name]['golden_deal'] = 0;

    foreach ($outcome_reports as $outcome_report) {
        if ($outcome_report->reason_outcome_id == 2) {
            $data[$outcome_report->gym_name]['outcome_daily'] = floatval($outcome_report->price_paied);
        } elseif ($outcome_report->reason_outcome_id == 1) {
            $data[$outcome_report->gym_name]['outcome_general'] = floatval($outcome_report->price_paied);
        } else {
            $data[$gym->gym_name]['outcome_daily'] = 0;
            $data[$gym->gym_name]['outcome_general'] = 0;
        }
    }

    foreach ($income_reports as $income_report) {
        if ($income_report->price_paied > 0) {
            $data[$income_report->gym_name]['income_paied'] = floatval($income_report->price_paied);
        } else {
            $data[$gym->gym_name]['income_paied'] = 0;
        }
    }

    foreach ($income_reports_by_payment_method as $income_report_by_payment_method) {
        switch ($income_report_by_payment_method->payment_method_name) {
            case "ΜΕΤΡΗΤΟΙΣ":
                $data[$income_report_by_payment_method->gym_name]['cash'] =
                floatval($income_report_by_payment_method->price_paied);
                break;
            case "ΚΑΡΤΑ":
                $data[$income_report_by_payment_method->gym_name]['card'] =
                floatval($income_report_by_payment_method->price_paied);
                break;
            case "ΔΩΡΕΑΝ":
                $data[$income_report_by_payment_method->gym_name]['free'] =
                floatval($income_report_by_payment_method->price_paied);
                break;
            case "PAYPAL":
                $data[$income_report_by_payment_method->gym_name]['paypal'] =
                floatval($income_report_by_payment_method->price_paied);
                break;
            case "CHEAPIS":
                $data[$income_report_by_payment_method->gym_name]['cheapis'] =
                floatval($income_report_by_payment_method->price_paied);
                break;
            case "GOLDEN DEAL":
                $data[$income_report_by_payment_method->gym_name]['golden_deal'] =
                floatval($income_report_by_payment_method->price_paied);
                break;
            default:
                $data[$gym->gym_name]['cash'] = 0;
                $data[$gym->gym_name]['free'] = 0;
                $data[$gym->gym_name]['paypal'] = 0;
                $data[$gym->gym_name]['cheapis'] = 0;
                $data[$gym->gym_name]['golden_deal'] = 0;
                break;
        }
    }
    echo json_encode(array("chart" => $data));
}

