<?php

$url = "https://justins-cookies.myshopify.com";
$endpoint = "/admin/discounts.json";
$requestType = "POST";

$response = array('error' => 0, 'message' => "Success", 'data' => "EXAMPLEcode16");

$name = (isset($_POST['name'])) ? $_POST['name'] : "";
if (isset($_POST['type'])) $type = $_POST['type'];
else {
	echo json_encode(array('error' => 1, 'message' => "Please provide a type", 'data' => ""));
	exit;
}
if (isset($_POST['amount'])) $amount = $_POST['amount'];
else {
	echo json_encode(array('error' => 1, 'message' => "Please provide an amount", 'data' => ""));
	exit;
}
if (isset($_POST['min'])) $min = $_POST['min'];
else {
	echo json_encode(array('error' => 1, 'message' => "Please provide a minimum", 'data' => ""));
	exit;
}

echo json_encode($response);
?>