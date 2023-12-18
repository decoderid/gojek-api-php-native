<?php

require_once 'vendor/autoload.php';

use Decoderid\GojekApi;

$gojek = new GojekApi();

/** SET UUID */
$uuid = $gojek->generateUuid();
$gojek->setUuid($uuid);

/** LOGIN */
$phone = 'Your Number';
$pin = 'Your Pin';

$login = $gojek->login($phone, $pin);

print_r($login);

echo "Enter OTP: ";
$otp = trim(fgets(STDIN));
$verifyOtp = $gojek->verifyOtp($otp, $login->data->otp_token);
print_r($verifyOtp);

if ($verifyOtp->access_token) {
    print_r($verifyOtp);
}

if ($verifyOtp->success) {
    print_r($verifyOtp);
}

/**
 * IF PIN AUTHENTICATION AFTER OTP
 */
if ($verifyOtp->errors[0]->code === 'mfa:customer_send_challenge:challenge_required') {
    $challengeToken = $verifyOtp->errors[0]->details->challenge_token;
    $challengeId = $verifyOtp->errors[0]->details->challenges[0]->gopay_challenge_id;

    $verifyMFA = $gojek->verifyMFA($challengeId, $pin);

    if ($verifyMFA->success) {
        $verifyMFAToken = $gojek->verifyMFAToken($challengeToken, $verifyMFA->data->token);
        print_r($verifyMFAToken);
    }
}
