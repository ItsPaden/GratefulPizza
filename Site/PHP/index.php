<?php
error_log('           INDEX.PHP                   ', 3, '/home/PHPLOP/info.txt');
// STEP 1: read POST data
// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
// Instead, read raw POST data from the input stream.
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
    $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
$req = 'cmd=_notify-validate';
if (function_exists('get_magic_quotes_gpc')) {
  $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
  if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
    $value = urlencode(stripslashes($value));
  } else {
    $value = urlencode($value);
  }
  $req .= "&$key=$value";
}

// Step 2: POST IPN data back to PayPal to validate
//$ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
$ch = curl_init('https://ipnpb.sandbox.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// In wamp-like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
// the directory path of the certificate as shown below:
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if ( !($res = curl_exec($ch)) ) {
  // error_log("Got " . curl_error($ch) . " when processing IPN data");
  curl_close($ch);
  exit;
}

// inspect IPN validation result and act accordingly
if (strcmp ($res, "VERIFIED") == 0) {
	
	error_log('Payment Verified', 3, '/home/PHPLOP/info.txt');
  // The IPN is verified, process it:
  // check whether the payment_status is Completed
  // check that txn_id has not been previously processed
  // check that receiver_email is your Primary PayPal email
  // check that payment_amount/payment_currency are correct
  // process the notification
  // assign posted variables to local variables
	  $item_name = $_POST['item_name'];
	  $item_number = $_POST['item_number'];
	  $payment_status = $_POST['payment_status'];
	  $payment_amount = $_POST['mc_gross'];
	  $payment_currency = $_POST['mc_currency'];
	  $txn_id = $_POST['txn_id'];
	  $receiver_email = $_POST['receiver_email'];
	  $payer_email = $_POST['payer_email'];
	  $residence_country = $_POST['residence_country'];
	  $name = $_POST['first_name'] . ' ' . $_POST['last_name'];
  
	//MYSQL
  
	error_log('Connecting', 3, '/home/PHPLOP/info.txt');
  
	  /* Attempt MySQL server connection. Assuming you are running MySQL
	server with default setting (user 'root' with no password) */
	$link = mysqli_connect("localhost", "root", "PASSWORD_HERE", "payments");
	 
	// Check connection
	if($link === false){
		error_log('Die', 3, '/home/PHPLOP/info.txt');
		
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	 error_log('No Die', 3, '/home/PHPLOP/info.txt');
	 
	// Attempt insert query execution
	$sql = "INSERT INTO donations (amount, name, country, email) VALUES ($payment_amount, '$name', '$residence_country', '$payer_email')";
	if(mysqli_query($link, $sql)){
		error_log("Success :.)");
		echo "Records inserted successfully.";
	} else{
		error_log("ERROR: Could not able to execute $sql. " . mysqli_error($link));
		echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}
	 
	// Close connection
	mysqli_close($link);
  
  
  // IPN message values depend upon the type of notification sent.
  // To loop through the &_POST array and print the NV pairs to the screen:
  foreach($_POST as $key => $value) {
    echo $key . " = " . $value . "<br>";
	error_log($key . " = " . $value . "<br>", 3, '/home/PHPLOP/info.txt');
  }
} else if (strcmp ($res, "INVALID") == 0) {
  // IPN invalid, log for manual investigation
}

?>
