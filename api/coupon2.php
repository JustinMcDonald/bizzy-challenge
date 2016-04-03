<?php

require '../lib/shopify.php';

$API_KEY = "37dbae720e281c0089bf605364f5a012";
$SECRET = "048ba5add1d40877aed0fe0687574104";

$response = array('error' => 0, 'message' => "Success", 'data' => "");

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
if (isset($_POST['shop'])) $shop = $_POST['shop'];
else {
    echo json_encode(array('error' => 1, 'message' => "Please provide your Shopify store name.", 'data' => ""));
    exit;
}
if (isset($_POST['email'])) $email = $_POST['email'];
else {
    echo json_encode(array('error' => 1, 'message' => "Please provide your Shopify email.", 'data' => ""));
    exit;
}
if (isset($_POST['password'])) $password = $_POST['password'];
else {
    echo json_encode(array('error' => 1, 'message' => "Please provide your Shopify password.", 'data' => ""));
    exit;
}

$api = new \Shopify\PrivateAPI($email, $password, 'https://' . $shop . '.myshopify.com/admin');

if (!$api->isLoggedIn() && !$api->login()) {
    echo json_encode(array('error' => 1, 'message' => "Invalid credentials.", 'data' => ""));
} else {

    $discount = [
        'discount_type' => $type,
        'value' => $amount,
        "minimum_order_amount"=>$min
    ];

    if ($name != "") $discount["code"] = $name;

    # Create a 5% discount coupon
    $new_discount = ['discount' => $discount];

    # Set the CSRF token for the POST request
    try { $api->setToken('https://' . $shop . '.myshopify.com/admin/discounts/new'); } 
    catch (\Exception $ex) { }

    $do_discount = $api->doRequest('POST', 'discounts.json', $new_discount);

    $response['message'] = print_r($do_discount, 1);

    echo json_encode($response);

    //print_r($do_discount);

    # List coupons
    /*$params = [
        'limit' => 50, 
        'order' => 'id+DESC', 
        'direction' => 'next'
    ];

    $discounts = $api->doRequest('GET', 'discounts.json', $params);
    if (isset($discounts->discounts)) {
        $coupons = $discounts->discounts;
        foreach ($coupons as $coupon) {
            print_r($coupon);
        }
    }

    $params = [
        'reportcenter' => true,
        'start_date' => '2013-02-22',
        'end_date' => '2013-03-01',
        'timezone' => 'Pacific+Time+(US+%26+Canada)'
    ];

    $referrals = $api->doRequest('GET', 'referrals.json', $params);
    print_r($referrals);

    $facts = $api->doRequest('GET', 'facts.json', $params);
    print_r($facts);

    $periodical_facts = $api->doRequest('GET', 'periodical_facts.json', $params);
    print_r($periodical_facts);
    */
}

?>