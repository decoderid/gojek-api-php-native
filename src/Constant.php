<?php
/**
 * HTTP REQUEST CONFIG
 */
define('BASE_URL_API', 'https://api.gojekapi.com');
define('BASE_URL_GOID', 'https://goid.gojekapi.com');
define('BASE_URL_CUSTOMER', 'https://customer.gopayapi.com');
define('USER_AGENT', 'okhttp/4.10.0');

/**
 * COUNTRY CODE CONFIG
 */
define('COUNTRY_CODE_PREFIX', '+62');
define('COUNTRY_CODE_ID', 'ID');

/**
 * DEVICE CONFIG
 */
define('X_PLATFORM', 'Android');
define('X_UNIQUEID', 'c6ad2e615cf54aa5');
define('X_APPVERSION', '4.74.3');
define('X_APPID', 'com.gojek.app');
define('X_USER_TYPE', 'customer');
define('X_DEVICE_OS', 'Android,9');
define('X_PHONEMAKE', 'samsung');
define('X_PHONEMODEL', 'samsung,SM-S901N');
define('GOJEK_COUNTRY_CODE', 'ID');

/** 
 * CLIENT ID & SECRET
 */
define('CLIENT_ID', 'gojek:consumer:app');
define('CLIENT_SECRET', 'pGwQ7oi8bKqqwvid09UrjqpkMEHklb');
define('CLIENT_ID_MFA', '6d11d261d7ae462dbd4be0dc5f36a697-MFAGOJEK');

/**
 * EP = ENDPOINT
 */
define('EP_LOGIN_REQUEST', BASE_URL_GOID . '/goid/login/request');
define('EP_VERIFY_OTP', BASE_URL_GOID . '/goid/token');
define('EP_VERIFY_MFA', BASE_URL_CUSTOMER . '/api/v1/users/pin/tokens');
define('EP_RESEND_OTP', BASE_URL_CUSTOMER . '/v2/otp/retry');
define('EP_PAYMENT_OPTIONS_BALANCES', BASE_URL_CUSTOMER . '/v1/payment-options/balances');
define('EP_USER_ORDER_HISTORY', BASE_URL_CUSTOMER . '/v1/users/order-history');
define('EP_USER_ORDER_HISTORY_DETAIL', BASE_URL_CUSTOMER . '/v1/users/order-history/{{ORDER_ID}}/details');
define('EP_CUSTOMER', BASE_URL_API . '/gojek/v2/customer');
define('EP_BANK_LIST', BASE_URL_CUSTOMER . '/v1/banks');
define('EP_VALIDATE_BANK', BASE_URL_CUSTOMER . '/v1/bank-accounts/validate');
define('EP_WITHDRAWALS', BASE_URL_CUSTOMER . '/v1/withdrawals');
define('EP_EXPLORE', BASE_URL_CUSTOMER . '/v1/explore');
define('EP_PAYMENTS_V1', BASE_URL_CUSTOMER . '/customers/v1/payments');
define('EP_PAYMENTS_V3', BASE_URL_CUSTOMER . '/v3/payments/{{PAYMENT_ID}}/capture');
define('EP_VALIDATE_P2P', BASE_URL_CUSTOMER . '/v1/users/p2p-profile');
define('EP_FUND_TRANSFER', BASE_URL_CUSTOMER . '/v1/funds/transfer');
define('EP_PAYMENT_OPTIONS', BASE_URL_CUSTOMER . '/v1/customer/payment-options');
define('EP_PIN_UPDATE', BASE_URL_CUSTOMER . '/v1/users/pin/update');


/**
 * CHALLENGES
 */
define('CHALLENGES_PIN_2FA', 'GoPay Pin 2FA');

/**
 * GRANT_TYPE
 */
define('GRANT_TYPE_OTP', 'otp');
define('GRANT_TYPE_PIN', 'gopay_pin');

/**
 * CHANNEL TYPE
 */
define('CHANNEL_TYPE_SMS', 'sms');

/**
 * LOGIN TYPE
 */
define('LOGIN_TYPE_PIN', 'gopay_pin');