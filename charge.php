<?php
require_once('vendor/autoload.php');

// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(getenv('STRIPE_TEST_SECRET_KEY'));

// Get the credit card details submitted by the form
$source =  $_POST['source'];
$amount = $_POST['amount'];
$customer_id = $_POST['customer_id'];
$shipping = $_POST['shipping'];

// Create the charge on Stripe's servers - this will charge the user's card
try {
	$charge = \Stripe\Charge::create(array(
  	"amount" => $amount,
  	"source" => $source,
    "currency" => 'usd',
    "customer" => $customer_id,
    "shipping" => $shipping,
    "description" => 'Example Charge'
	);

	// Check that it was paid:
	if ($charge->paid == true) {
		$response = array( 'status'=> 'Success', 'message'=>'Payment has been charged!!' );
	} else { // Charge was not paid!
		$response = array( 'status'=> 'Failure', 'message'=>'Your payment could NOT be processed because the payment system rejected the transaction. You can try again or use another card.' );
	}
	header("HTTP/1.1 200 OK");
	header('Content-Type: application/json');
	echo json_encode($response);

} catch(\Stripe\Error\Card $e) {
  exit(http_response_code(500));
}

?>