[![Hits](https://hits.seeyoufarm.com/api/count/incr/badge.svg?url=https%3A%2F%2Fgithub.com%2Fdecoderid%2Fgojek-api-php-native&count_bg=%2379C83D&title_bg=%23555555&icon=&icon_color=%23E7E7E7&title=hits&edge_flat=false)](https://hits.seeyoufarm.com)

## Gojek Api Unofficial (PHP NATIVE)
AppVersion: 4.74.3

## Api List
- [x] Login
- [x] Re-Login (No Need OTP)
- [x] Resend OTP
- [x] GoPay Pin 2FA
- [x] Profile
- [x] Balance
- [x] Transaction List
- [x] Transaction Detail
- [x] Bank List
- [x] Validate Bank
- [x] Transfer Bank
- [x] Transfer Gopay (P2P Sesama Akun)
- [x] Validate QRCode
- [x] Pay Static QR
- [x] Pay Dynamic QR
- [ ] Logout

## Example: Login
```php
<?php
require_once('Gojek.php');

$phone = '[PHONE]';
$pin = '[PIN]';

$gojek = new Gojek();
$login = $gojek->login($phone, $pin);

/**
 * VERIFY OTP
 */
$verifyOtp = $gojek->verifyOtp('[OTP]', $login->data->otp_token);

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

?>
```

## Demo
https://php-demo.decoder.id/app/gojek/

## Contact
im@decoder.id

## Negotiable
Full Support 6 Months
