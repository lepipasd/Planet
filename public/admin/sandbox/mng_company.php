<?php
require_once('../../../includes/initialize.php');

$file = "../../../customer_update.csv";
// $file = "../../address.txt";
$content = "";
if ($handle = fopen($file, 'r')) {
    while (!feof($handle)) {
        $content = fgets($handle);
        $content_array = explode(",", $content);
        echo "<pre>";
        //echo $content_array[0] . "<br/>" . trim(trim($content_array[1]),'"') . "<br/>";
        print_r($content_array);
        echo "<pre/>";
        // $vessel = new Vessel();

        // $customer = Customer::find_by_primary_key($content_array[0]);

        // echo "<pre>";
        // print_r($cust);
        // echo "<pre/>";

        // $customer = new Customer();

        // // $customer->customer_id = $content_array[0];
        // // $customer->address = trim(trim($content_array[1]),'"');

        // $customer->customer_id = $content_array[0];
        // $customer->company_name = $content_array[1];
        // // //$customer->address = $content_array[1];
        // $customer->contact_person = $content_array[2];
        // $customer->email = $content_array[3];
        // $customer->telephone = trim($content_array[4]);
        // $customer->save();
        // ((int)trim($content_array[4]) == 0 ? null : (int)trim($content_array[4]));

        // $vessel->vessel_name = $content_array[0];
        // $vessel->imo = is_int($content_array[1]) ? $content_array[1] : (int)$content_array[1];
        // $vessel->email = $content_array[2];
        // $vessel->customer_id = is_int($content_array[3]) ? $content_array[3] : (int)$content_array[3];
        // $vessel->marine_status = "active";

        // $vessel->vessel_id = $content_array[0];
        // $vessel->vessel_name = $content_array[1];
        // $vessel->customer_id = is_int($content_array[2]) ? $content_array[2] : (int)$content_array[2];
        // $vessel->imo = is_int($content_array[3]) ? $content_array[3] : (int)$content_array[3];
        // $vessel->email = trim($content_array[4]);
        
        // $vessel->marine_status = "active";
        
        // echo "<pre>";
        // print_r($vessel);
        // echo "<pre/>";

        // echo "<pre>";
        // print_r($customer );
        // echo "<pre/>";

        // $result = $customer->save();

        // echo "<pre>";
        // print_r($result);
        // echo "<pre/>";

        
        // echo $content . "<br/>";
    }
    fclose($handle);
}

//echo $content;
// $vessel = new Vessel();

// $vessel->vessel_name = "Nordic Bothnia";
// $vessel->imo = 9079157;

// $result = $vessel->save();

// echo "<pre>";
// print_r($result);
// echo "<pre/>";

// SELECT pg_terminate_backend(pg_stat_activity.pid)
// FROM pg_stat_activity
// WHERE pg_stat_activity.datname = 'trinity'
//   AND pid <> pg_backend_pid();;
