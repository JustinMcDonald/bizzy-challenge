<?php

$url = "https://justins-cookies.myshopify.com";
$endpoint = "/admin/discounts.json";
$requestType = "POST";

$apiKey = "37dbae720e281c0089bf605364f5a012";
$secret = "048ba5add1d40877aed0fe0687574104";

$response = array('error' => 0, 'message' => "Success", 'data' => "EXAMPLEcode16");

$name = (isset($_POST['name'])) ? $_POST['name'] : "";
if (isset($_POST['type'])) $type = $_POST['type'];
else {
	echo json_encode(array('error' => 1, 'message' => "Please provide a discount type (percentage/fixed).", 'data' => ""));
	exit;
}
if (isset($_POST['amount'])) $amount = $_POST['amount'];
else {
	echo json_encode(array('error' => 1, 'message' => "Please provide a discount amount.", 'data' => ""));
	exit;
}
if (isset($_POST['min'])) $min = $_POST['min'];
else {
	echo json_encode(array('error' => 1, 'message' => "Please provide a minimum order amount.", 'data' => ""));
	exit;
}

$discount = array("discount_type"=>$type, "value"=>$amount, "minimum_order_amount"=>$min);

if ($name != "") $discount["code"] = $name;

$request = array("discount"=>$discount);

$requestJSON = json_encode($request);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => $requestType,
        'content' => $requestJSON
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url . $endpoint, false, $context);
if ($result === FALSE) { 
	echo json_encode(array('error' => 1, 'message' => "Error: unable to create shopify coupon.", 'data' => ""));
	exit;
}

$response['data'] = $result;

echo json_encode($response);
?>