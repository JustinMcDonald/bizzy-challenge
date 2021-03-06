<?php

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
if (isset($_POST['accessToken'])) $accessToken = $_POST['accessToken'];
else {
	echo json_encode(array('error' => 1, 'message' => "Missing access token.", 'data' => ""));
	exit;
}
if (isset($_POST['shop'])) $shop = $_POST['shop'];
else {
	echo json_encode(array('error' => 1, 'message' => "Missing shop namespace.", 'data' => ""));
	exit;
}

$url = "https://" . $shop . $endpoint;

$discount = array("discount_type"=>$type, "value"=>$amount, "minimum_order_amount"=>$min);

if ($name != "") $discount["code"] = $name;

$request = array("discount"=>$discount);

$requestJSON = json_encode($request);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "X-Shopify-Access-Token: " . $accessToken . "\r\n", //"Content-type: application/x-www-form-urlencoded\r\nX-Shopify-Access-Token: $accessToken\r\n",
        'method'  => $requestType,
        'content' => http_build_query($request) //$requestJSON
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { 
	echo json_encode(array('error' => 1, 'message' => "Error: unable to create shopify coupon.", 'data' => ""));
	exit;
}

$response['data'] = json_decode($result);

echo json_encode($response);
?>