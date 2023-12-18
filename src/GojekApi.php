<?php
namespace Decoderid;

class GojekApi {

    private $headers = [
        'Content-Type: application/json',
        'User-Agent: '. USER_AGENT,
        'X-Platform: ' . X_PLATFORM,
        'X-Uniqueid: ' . X_UNIQUEID,
        'X-Appversion: ' . X_APPVERSION,
        'X-Appid: ' . X_APPID,
        'X-User-Type: ' . X_USER_TYPE,
        'X-Deviceos: ' . X_DEVICE_OS,
        'X-Phonemake: ' . X_PHONEMAKE,
        'X-Phonemodel: ' . X_PHONEMODEL,
        'Gojek-Country-Code: ' . GOJEK_COUNTRY_CODE
    ];

    public function __construct($token = '') {
        if ($token) {
            $this->headers = array_merge($this->headers, [
                'Authorization: Bearer ' . $token
            ]);
        }
    }

    private function uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function request($method, $url, $payload = [], $headers = []) {

        $method = strtoupper($method);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers ? array_merge($this->headers, $headers) : $this->headers);

        if ($method === 'POST' || $method === 'PATCH' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }
        
        $exec = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($exec);
    }

    public function setUuid($uuid) {
        if (($key = array_search('X-Uniqueid: ' . X_UNIQUEID, $this->headers)) !== false) {
            unset($this->headers[$key]);
        }

        $this->headers = array_merge($this->headers, [
            'X-Uniqueid: ' . $uuid
        ]);
    }

    public function generateUuid() {
        $splits = explode('-', $this->uuid());
        $result =  $splits[0] . $splits[1] . $splits[2];
        return $result;
    }

    public function login($phone) {
        return $this->request('POST', EP_LOGIN_REQUEST, [
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'country_code' => COUNTRY_CODE_PREFIX,
            'login_type' => '',
            'magic_link_ref' => '',
            'phone_number' => $phone
        ]);
    }

    public function relogin($phone, $pin) {
        $challenge = $this->request('POST', EP_LOGIN_REQUEST, [
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'country_code' => COUNTRY_CODE_PREFIX,
            'login_type' => LOGIN_TYPE_PIN,
            'phone_number' => $phone
        ]);

        if (!$challenge->success) {
            return 'Error Challenge';
        }

        $challengeToken = $this->request('POST', EP_VERIFY_MFA, [
            'challenge_id' => $challenge->data->gopay_challenge_id,
            'client_id' => CLIENT_ID_MFA,
            'pin' => $pin
        ]);

        if (!$challengeToken->success) {
            return 'Error Challenge Token';
        }

        $token = $this->request('POST', EP_VERIFY_OTP, [
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'data' => [
                'gopay_challenge_id' => $challenge->data->gopay_challenge_id,
                'gopay_jwt_value' => $challengeToken->data->token
            ],
            'grant_type' => GRANT_TYPE_PIN,
            'scopes' => []
        ]);


        return $token;
    }

    public function verifyOtp($otp, $otp_token) {
        return $this->request('POST', EP_VERIFY_OTP, [
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'data' => [
                'otp' => $otp,
                'otp_token' => $otp_token
            ],
            'grant_type' => GRANT_TYPE_OTP,
            'scopes' => []
        ]);
    }

    public function verifyMFA($challenge_id, $pin) {
        return $this->request('POST', EP_VERIFY_MFA, [
            'challenge_id' => $challenge_id,
            'client_id' => CLIENT_ID_MFA,
            'pin' => $pin
        ]);
    }

    public function verifyMFAToken($challenge_token, $token) {
        return $this->request('POST', EP_VERIFY_OTP, [
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'data' => [
                'challenge_token' => $challenge_token,
                'challenges' => [
                    [
                        'name' => CHALLENGES_PIN_2FA,
                        'value' => $token
                    ]
                ]
            ],
            'grant_type' => 'challenge',
            'scopes' => []
        ]);
    }

    public function resendOtp($otp_token) {
        return $this->request('POST', EP_RESEND_OTP, [
            'channel_type' => CHANNEL_TYPE_SMS,
            'otp_token' => $otp_token
        ]);
    }

    public function getProfile() {
        return $this->request('GET', EP_CUSTOMER);
    }

    public function getBalance() {
        return $this->request('GET', EP_PAYMENT_OPTIONS_BALANCES);
    }

    public function getTransactionList($page = 1, $limit = 10, $startDate = '', $endDate = '') {

        $query = http_build_query([
            'page' => $page,
            'limit' => $limit,
            'lower_bound' => $startDate ? $startDate . 'T00:00:00' : '',
            'upper_bound' => $endDate ? $endDate . 'T00:00:00' : '',
            'country_code' => COUNTRY_CODE_ID
        ]);

        return $this->request('GET', EP_USER_ORDER_HISTORY . '?' . $query);
    }

    public function getTransactionDetail($order_id) {
        $query = [
            'country_code' => COUNTRY_CODE_ID
        ];
        
        return $this->request('GET', str_replace('{{ORDER_ID}}', $order_id, EP_USER_ORDER_HISTORY_DETAIL) . '?' . $query);
    }

    public function getBankList() {
        $query = http_build_query([
            'type' => 'transfer',
            'show_withdrawal_block_status' => false
        ]);

        return $this->request('GET', EP_BANK_LIST . '?' . $query);
    }

    public function validateBank($bankCode, $accountNumber) {
        $query = http_build_query([
            'bank_code' => $bankCode,
            'account_number' => $accountNumber
        ]);

        return $this->request('GET', EP_VALIDATE_BANK . '?' . $query);
    }

    public function validateP2P($phoneNumber) {

        $query = http_build_query([
            'phone_number' => $phoneNumber
        ]);

        return $this->request('GET', EP_VALIDATE_P2P . '?' . $query);
    }

    public function transferBank($bankCode, $accountNumber, $amount, $notes, $pin) {
        $validateBank = $this->validateBank($bankCode, $accountNumber);

        if (!$validateBank->success) {
            return 'Error ValidateBank';
        }

        return $this->request('POST', EP_WITHDRAWALS, [
            'account_name' => $validateBank->data->account_name,
            'account_number' => $accountNumber,
            'amount' => $amount,
            'bank_code' => $bankCode,
            'currency' => 'IDR',
            'notes' => $notes,
            'pin' => $pin,
            'type' => 'transfer'
        ], [
            'Idempotency-Key: ' . $this->uuid()
        ]);
    }

    public function transferP2P($phoneNumber, $amount, $pin) {
        $validateP2P = $this->validateP2P($phoneNumber);

        if (!$validateP2P->success) {
            return 'Error ValidateP2P';
        }

        if ($validateP2P->data->is_blocked) {
            return 'Error ValidateP2P User Blocked';
        }

        return $this->request('POST', EP_FUND_TRANSFER, [
            'amount' => [
                'currency' => 'IDR',
                'value' => $amount
            ],
            'description' => '',
            'metadata' => [
                'post_visibility' => 'PRIVATE',
                'theme_id' => 'THEME_CLASSIC'
            ],
            'payee' => [
                'id' => $validateP2P->data->qr_id
            ]
        ], [
            'Pin: ' . $pin
        ]);
    }

    public function validateQRCode($data) {
        return $this->request('POST', EP_EXPLORE, [
            'data' => $data,
            'type' => 'QR_CODE'
        ]);
    }

    public function payStaticQR($payee, $additionalData, $metaData, $orderSignature, $amount, $pin) {

        $inquiry = $this->request('POST', EP_PAYMENTS_V1,  [
            'additional_data' => $additionalData,
            'amount' => [
                'currency' => 'IDR',
                'value' => $amount
            ],
            'channel_type' => 'STATIC_QR',
            'checksum' => json_decode($metaData->checksum),
            'fetch_promotion_details' => false,
            'metadata' => $metaData,
            'order_signature' => $orderSignature,
            'payee' => $payee,
            'payment_intent' => $metaData->payment_widget_intent
        ], [
            'Idempotency-Key: ' . $this->uuid()
        ]);

        if (!$inquiry->success) {
            return 'Error Inquiry';
        }

        $query = http_build_query([
            'intent' => $inquiry->data->intent,
            'merchant_id' => $inquiry->data->merchant_information->merchant_id,
        ]);

        $paymentOptions = $this->request('GET', EP_PAYMENT_OPTIONS . '?' . $query);

        if (!$paymentOptions->success) {
            return 'Error Payment Options';
        }

        $paymentOptionsToken = $paymentOptions->data->payment_options[0]->token;

        return $this->request('PATCH', str_replace('{{PAYMENT_ID}}', $inquiry->data->payment_id, EP_PAYMENTS_V3),  [
            'additional_data' => $additionalData,
            'applied_promo_code' => [
                'NO_PROMO_APPLIED'
            ],
            'checksum' => json_decode($metaData->checksum),
            'metadata' => $metaData,
            'order_signature' => $orderSignature,
            'payment_instructions' => [
                [
                    'amount' => [
                        'currency' => 'IDR',
                        'display_value' => '',
                        'value' => $amount
                    ],
                    'token' => $paymentOptionsToken
                ]
            ]
        ], [
            'Pin: ' . $pin,
            'X-User-Locale: en_ID'
        ]);
    }

    public function payDynamicQR($paymentId, $additionalData, $metaData, $orderSignature, $amount, $pin) {
    public function payDynamicQR($payee, $additionalData, $metaData, $orderSignature, $amount, $pin)
    {

        $query = http_build_query([
            'fetch_promotion_details' => false
        ]);

        $inquiry = $this->request('POST', EP_PAYMENTS_V1,  [
            'additional_data' => $additionalData,
            'amount' => [
                'currency' => 'IDR',
                'value' => $amount->value
            ],
            'channel_type' => 'DYNAMIC_QR',
            'checksum' => json_decode($metaData->checksum),
            'fetch_promotion_details' => false,
            'metadata' => $metaData,
            'order_signature' => $orderSignature,
            'payee' => $payee,
            'payment_intent' => $metaData->payment_widget_intent
        ], [
            'Idempotency-Key: ' . $this->uuid()
        ]);

        if (!$inquiry->success) {
            return 'Error Inquiry';
        }

        // $test = $this->request('GET', EP_PAYMENTS_V1 . '/' . $inquiry->data->payment_id . '?' . $query);
        // print_r($test);


        $query = http_build_query([
            'intent' => $inquiry->data->intent,
            'merchant_id' => $inquiry->data->merchant_information->merchant_id,
        ]);

        $paymentOptions = $this->request('GET', EP_PAYMENT_OPTIONS . '?' . $query);

        if (!$paymentOptions->success) {
            return 'Error Payment Options';
        }

        $paymentOptionsToken = $paymentOptions->data->payment_options[0]->token;

        return $this->request('PATCH', str_replace('{{PAYMENT_ID}}', $inquiry->data->payment_id, EP_PAYMENTS_V3),  [
            'additional_data' => $additionalData,
            'applied_promo_code' => [
                'NO_PROMO_APPLIED'
            ],
            'channel_type' => 'DYNAMIC_QR',
            'checksum' => json_decode($metaData->checksum),
            'metadata' => $metaData,
            'order_signature' => $orderSignature,
            'payment_instructions' => [
                [
                    'amount' => [
                        'currency' => 'IDR',
                        'display_value' => '',
                        'value' => $inquiry->data->amount->value
                    ],
                    'token' => $paymentOptionsToken
                ]
            ]
        ], [
            'Pin: ' . $pin,
            'X-User-Locale: en_ID'
        ]);
    }

    public function updatePIN($oldPin, $newPin) {
        return $this->request('PUT', EP_PIN_UPDATE, [
            'new_pin' => $newPin
        ], [
            'Pin: ' . $oldPin
        ]);
    }

    public function logout() {
        return $this->request('DELETE', EP_VERIFY_OTP);
    }
}}
