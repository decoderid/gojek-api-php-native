[![MIT License][license-shield]][license-url]
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Issues][issues-shield]][issues-url]

[![Hits][hits-view]][hits-view-url]

## Gojek Api Unofficial (PHP NATIVE)
AppVersion: 4.74.3

Gimme Buff to Get More Power: https://trakteer.id/decoderid

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
- [x] Validate P2P
- [x] Transfer Bank
- [x] Transfer Gopay (P2P Sesama Akun)
- [x] Validate QRCode
- [x] Pay Static QR
- [x] Pay Dynamic QR
- [x] Update PIN
- [x] Logout

## Composer

```bash
$ composer require decoderid/gojek-api
```

## Example: Login
```php
<?php
require_once 'vendor/autoload.php';

use Decoderid/GojekApi;

$phone = '[PHONE]';
$pin = '[PIN]';

$gojek = new GojekApi();
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
Email: im@decoder.id

Telegram: [@decoderid](https://t.me/decoderid)

## Another Project
1. [Dana Unofficial Api](https://github.com/decoderid/Unofficial-Api-Dana)
2. [Cek Pajak Kendaraan](https://github.com/decoderid/Cek-Pajak-Kendaraan-BOT)

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/decoderid/gojek-api-php-native.svg?style=for-the-badge
[contributors-url]: https://github.com/decoderid/gojek-api-php-native/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/decoderid/gojek-api-php-native.svg?style=for-the-badge
[forks-url]: https://github.com/decoderid/gojek-api-php-native/network/members
[stars-shield]: https://img.shields.io/github/stars/decoderid/gojek-api-php-native.svg?style=for-the-badge
[stars-url]: https://github.com/decoderid/gojek-api-php-native/stargazers
[issues-shield]: https://img.shields.io/github/issues/decoderid/gojek-api-php-native.svg?style=for-the-badge
[issues-url]: https://github.com/decoderid/gojek-api-php-native/issues
[license-shield]: https://img.shields.io/github/license/decoderid/gojek-api-php-native.svg?style=for-the-badge
[license-url]: https://github.com/decoderid/gojek-api-php-native/blob/master/LICENSE.txt
[hits-view]: https://hits.seeyoufarm.com/api/count/incr/badge.svg?url=https%3A%2F%2Fgithub.com%2Fdecoderid%2Fgojek-api-php-native&count_bg=%2379C83D&title_bg=%23555555&icon=github.svg&icon_color=%23E7E7E7&title=hits&edge_flat=true
[hits-view-url]: https://hits.seeyoufarm.com