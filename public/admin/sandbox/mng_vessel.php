<?php
require_once('../../../includes/initialize.php');

$test_array = [];
$test_array['role_id'] = '';

$role_id = !empty($test_array['role_id']) ? (trim($test_array['role_id']) == 'User' ? 1 : 2) : 1;

echo $test_array['role_id'] . " " . $role_id;
