<?php
require_once('vendor/autoload.php');

// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(getenv('STRIPE_TEST_SECRET_KEY'));

function json_response($message = null, $code = 200)
{
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-Type: application/json');
    $status = array(
        200 => '200 OK',
        400 => '400 Bad Request',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        );
    // ok, validation error, or failure
    header('Status: '.$status[$code]);
    // return the encoded json
    return json_encode(array(
        'status' => $code < 300, // success or not?
        'message' => $message
        ));
}

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
    exit(json_response($response, 200)); // {"status":true,"message":"working"}

} catch(\Stripe\Error\Card $e) {
    exit(json_response($e, 500)); // {"status":true,"message":"working"}
}

?>