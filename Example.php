<?php

require_once('Gojek.php');

$accessToken = '';

$gojek = new Gojek($accessToken);

print_r($gojek->login('[PHONE]'));
print_r($gojek->relogin('[PHONE]', '[PIN]'));
print_r($gojek->verifyOtp('[OTP]', '[OTP_TOKEN]'));
print_r($gojek->verifyMFA('[CHALLENGE_ID]', '[PIN]'));
print_r($gojek->verifyMFAToken('[CHALLENGE_TOKEN]', '[TOKEN]'));
print_r($gojek->resendOtp('[OTP_TOKEN'));
print_r($gojek->getProfile());
print_r($gojek->getBalance());
print_r($gojek->getTransactionList());
print_r($gojek->getTransactionDetail('[payment_id|order_id]'));
print_r($gojek->getBankList());
print_r($gojek->validateBank('[BANK_CODE]', '[ACCOUNT_NUMBER]'));
print_r($gojek->validateP2P('[PHONE_NUMBER]'));
print_r($gojek->transferBank('[BANK_CODE]', '[ACCOUNT_NUMBER]', '[AMOUNT]', '[NOTES]', '[PIN]'));
print_r($gojek->transferP2P('[PHONE_NUMBER]', [AMOUNT], '[PIN]'));
$validateQRCode = $gojek->validateQRCode('[QRIS_STRING]');
if ($validateQRCode->success) {
    print_r($gojek->payStaticQR($validateQRCode->data->payee, $validateQRCode->data->additional_data, $validateQRCode->data->metadata, $validateQRCode->data->order_signature, [AMOUNT], '[PIN]'));
}

$validateQRCode = $gojek->validateQRCode('[QRIS_STRING]');
if ($validateQRCode->success) {
    print_r($gojek->payDynamicQR($validateQRCode->data->payment_id, $validateQRCode->data->additional_data, $validateQRCode->data->metadata, $validateQRCode->data->order_signature, [AMOUNT], '[PIN]'));
}
