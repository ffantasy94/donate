<?php
require_once('vendor/autoload.php');

// Set your secret key: remember to change this to your live secret key in production
// See your keys here https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey(getenv('STRIPE_TEST_SECRET_KEY'));

// This assumes that $customerId has been set appropriately from session data
if (!isset($_POST['api_version']))
{
    exit(http_response_code(400));
}
try {
    $key = \Stripe\EphemeralKey::create(
      array("customer" => $_POST['customer_id']),
      array("stripe_version" => $_POST['api_version'])
    );
    header('Content-Type: application/json');
    exit(json_encode($key));
} catch (Exception $e) {
    exit(http_response_code(500));
}

?>