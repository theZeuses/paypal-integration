<?php

use App\Models\Info;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/signup', function () {
    return view('therapist.signup');
});

Route::get('/paypal-setup', function(){
    $user = Info::where('id', 1)->first();
    $token = base64_encode(env('PAYPAL_CLIENT_ID').':'.env('PAYPAL_CLIENT_SECRET'));
    $client = new Client();
    $response = $client->request('POST', 'https://api-m.sandbox.paypal.com/v2/customer/partner-referrals', [
        'headers' => [
            'Content-Type'     => 'application/json',
            'Authorization'      => 'Basic '.$token
        ],
        'form-params' => [
            "tracking_id" => $user->id,
            // "email" => $user->email,
            "partner_config_override" => [
                // "partner_logo_url" => "https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg",
                "return_url" => "https://estoreshaper.com/signup",
                "return_url_description" => "the url to return the merchant after the paypal onboarding process.",
                // "action_renewal_url" => "https://testenterprises.com/renew-exprired-url",
                "show_add_credit_card" => true
            ],
            "operations" => [
              [
                "operation" => "API_INTEGRATION",
                "api_integration_preference" => [
                  "rest_api_integration" => [
                    "integration_method" => "PAYPAL",
                    "integration_type" => "THIRD_PARTY",
                    "third_party_details" => [
                      "features" => [
                        "PAYMENT",
                        "REFUND"
                     ]
                    ]
                  ]
                ]
              ]
            ],
            "products" => [
              "EXPRESS_CHECKOUT"
            ],
            "legal_consents" => [
              [
                "type" => "SHARE_DATA_CONSENT",
                "granted" => true
              ]
            ]
        ]
    ]);
    $links = json_decode($response->getBody());
    return redirect($links->links[1]->href);
});

Route::get('/pay', function(){
  return view('client.pay');
});

