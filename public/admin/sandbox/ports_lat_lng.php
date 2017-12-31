<?php
require_once('../../../includes/initialize.php');

$port = new Port();

$port->country = "gr";

$ports = $port->find_port_by_country();

foreach ($ports as $port) {
    
// 	echo "<pre>";
// print_r($port);
// echo "<pre/>";
    echo $port['port_name'] . "," . $port['latitude'] . "," . $port['longitude'] . "<br/>";
}

// echo "<pre>";
// print_r($ports);
// echo "<pre/>";


// $file = "../../../ports.txt";
// $content = "";
// if($handle = fopen($file, 'r')) {

// 	while(!feof($handle)) {
// 		$content = strtolower(trim(fgets($handle)));

// 		$cnt = '%';


        // global $db;

        // $sql = "SELECT port_name, latitude, longitude FROM ports WHERE lower(port_name) LIKE '%" . $content . "%'";
        // $options = array($content);

        // $sth = $db->query($sql)->fetch();

        // echo !empty($sth) ? $sth['port_name'] . "," . $sth['latitude'] . "," . $sth['longitude'] . "<br/>" : $content . " not found<br/>";

        // echo "<pre>";
        // //echo $content_array[0] . "<br/>" . trim(trim($content_array[1]),'"') . "<br/>";
        // print_r($sth);
        // echo "<pre/>";
        // $vessel = new Vessel();

        
// 	}
// 	fclose($handle);
// };
