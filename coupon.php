<?php 

$code = $_GET['code'];
$hmac = $_GET['hmac'];
$shop = $_GET['shop'];
$signature = $_GET['signature'];
$state = $_GET['state'];

$url = "https://" . $shop . "/admin/oauth/access_token";

$apiKey = "37dbae720e281c0089bf605364f5a012";
$secret = "048ba5add1d40877aed0fe0687574104";

$data = array('client_id' => $apiKey, 'client_secret' => $secret, 'code' => $code);

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => "POST",
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { 
  echo json_encode(array('error' => 1, 'message' => "Error: unable to connect with Shopify.", 'data' => ""));
  exit;
}

$resultJSON = json_decode($result);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Bizzy | Coupon Challenge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png">
    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- FontAwesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" media="screen">
    <!-- Main css -->
    <link href="css/main.css" rel="stylesheet" media="screen">
    <!-- Roboto Google font -->
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div class="header">
      <h1>Create custom coupons for your <span style="color:#B6EB9B;">Shopify</span> customers</h1>
    </div>
    <div class="content">
      <div id="bizzy-form">
        <div class="row">
          <div class="col-md-8 col-md-offset-2 col-xs-12">
            <h3>Create your coupon instantly</h3>
            <div class="row">
              <div class="col-md-7">
                <div class="form-group">
                  <label for="bizzy-name">Coupon Name (optional)</label>
                  <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-user"></i></div>
                    <input type="text" class="form-control" id="bizzy-name" placeholder="Example: Cookies4FREE">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2">
                <div class="form-group">
                  <label for="bizzy-type">Coupon Type</label><br>
                  <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-default radio-btn active">
                      <input id="bizzy-type-percentage" type="radio" name="bizzy-type" value="percentage" autocomplete="off" checked>
                      <i class="fa fa-percent"></i>
                    </label>
                    <label class="btn btn-default radio-btn">
                      <input id="bizzy-type-fixed" type="radio" name="bizzy-type" value="fixed_amount" autocomplete="off">
                      <i class="fa fa-usd"></i>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <label for="bizzy-amount">Discount Value</label>
                  <div class="input-group">
                    <div id="bizzy-value-label-usd" class="input-group-addon" style="display:none;"><i class="fa fa-usd"></i></div>
                    <input type="number" class="form-control" id="bizzy-amount" placeholder="Example: 10" min="0">
                    <div id="bizzy-value-label-percent" class="input-group-addon"><i class="fa fa-percent"></i></div>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <div class="form-group">
                  <label for="bizzy-min">Minimum Order</label>
                  <div class="input-group">
                    <div class="input-group-addon"><i class="fa fa-usd"></i></div>
                    <input type="number" class="form-control" id="bizzy-min" placeholder="Example: 50">
                  </div>
                </div>
              </div>
            </div>
            <button id="bizzy-submit" type="button" class="btn btn-primary btn-lg">Create Coupon</button>
          </div>
        </div>
      </div>
      <div id="bizzy-success" style="display:none;position:absolute;left:100%;">
        Success!
      </div>
    </div>
    <!-- jQuery -->
    <script src="http://code.jquery.com/jquery.js"></script>
    <!-- Bootstrap -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <!-- Custom JS -->
    <script>
      var accessToken = "<?php echo $resultJSON['access_token']; ?>";
      $(document).ready(function() {
        $("input[name=bizzy-type]").change(function(){
          if ($('input[name=bizzy-type]:checked').val() == "percentage") {
            $('#bizzy-value-label-usd').fadeOut();
            $('#bizzy-value-label-percent').fadeIn();
          } else {
            $('#bizzy-value-label-percent').fadeOut();
            $('#bizzy-value-label-usd').fadeIn();
          }
        });
        $('#bizzy-submit').bind('click', function() {
          var couponName = $('#bizzy-name').val();
          var couponType = $('input[name=bizzy-type]:checked').val();
          var couponAmount = $('#bizzy-amount').val();
          var couponMinimum = $('#bizzy-min').val();
          if (couponType == "") {
            alert("Please select a coupon type.");
            return;
          }
          else if (couponAmount == "") {
            alert("Please provide a coupon amount.");
            return;
          }
          else if (!$.isNumeric(couponAmount)) {
            alert("Please provide a numerical coupon amount (using only digits 0-9).");
            return;
          }
          else if (couponMinimum == "") {
            alert("Please provide a minimum order amount.");
            return;
          }
          else if (!$.isNumeric(couponMinimum)) {
            alert("Please provide a numerical minimum order amount (using only digits 0-9).");
            return;
          }
          var data = {
            'name': couponName,
            'type': couponType,
            'amount': couponAmount,
            'min': couponMinimum,
            'accessToken': accessToken
          };
          $.ajax({
            url: "/api/coupon.php",
            type: "POST",
            data: data,
            dataType: "json",
            success: function(response) {
              if (response.error == 0) {
                console.log(response.data);
                alert("Success! " + response.message);
              }
              else alert(response.message);
            },
            error: function(error) {
              alert("Error: " + error);
            }
          })
        });
      });
    </script>
  </body>
</html>
