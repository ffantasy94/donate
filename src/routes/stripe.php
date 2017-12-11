<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

\Stripe\Stripe::setApiKey(getenv('STRIPE_TEST_SECRET_KEY'));


$app = new \Slim\App;


$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

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

// Add Customer
$app->post('/api/ephemeral_keys', function(Request $request, Response $response){
    $api_version = $request->getParam('api_version');
    $customer_id = $request->getParam('customer_id');

    if (!isset($api_version)){
        echo(json_response('No API Version', 400));
    }else {
        try {
            $key = \Stripe\EphemeralKey::create(
              array("customer" => $customer_id),
              array("stripe_version" => $api_version)
            );
            header('Content-Type: application/json');
            echo(json_encode($key));
        } catch (Exception $e) {
            echo(json_response($e, 500)); 
        }
    }
});
