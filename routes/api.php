<?php

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('create-order', function(Request $req){
    $token = base64_encode(env('PAYPAL_CLIENT_ID').':'.env('PAYPAL_CLIENT_SECRET'));
    $client = new Client();
    $response = $client->request('POST', 'https://api-m.sandbox.paypal.com/v2/checkout/orders', [
        'headers' => [
            'Content-Type'     => 'application/json',
            'Authorization'      => 'Basic '.$token
        ],
        'body' => json_encode([
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "amount" => [
                    "currency_code" => "USD",
                    "value" => "100.00"
                ],
                "payee" => [
                    // "merchant_id" => "N6KKYGJEJ2FGU"
                    "email" => "sb-invro5953558@business.example.com"
                ],
                "payment_instruction" => [
                    "disbursement_mode" => "INSTANT",
                    "platform_fees" => [[
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => "25.00"
                        ]
                    ]]
                ]
            ]]
        ])
    ]);
    $results = json_decode($response->getBody());
    return response()->json($results);
});

Route::post('capture-order/{orderId}', function(Request $req, $orderId){
    $token = base64_encode(env('PAYPAL_CLIENT_ID').':'.env('PAYPAL_CLIENT_SECRET'));
    $client = new Client();
    $response = $client->request('POST', 'https://api-m.sandbox.paypal.com/v2/checkout/orders/'.$orderId.'/capture', [
        'headers' => [
            'Content-Type'     => 'application/json',
            'Authorization'      => 'Basic '.$token
        ]
    ]);
    return response()->json(json_decode($response->getBody()));
});