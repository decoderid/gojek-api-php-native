<?php

require_once 'vendor/autoload.php';

use Decoderid\GojekApi;

$gojek = new GojekApi();

/** SET UUID */
$uuid = $gojek->generateUuid();
$gojek->setUuid($uuid);

$pin = 'Your Pin';

$validateQr = $gojek->validateQRCode('your qr');

// print_r($validateQr);

$payee_id = $validateQr->data->payee->id;
$payee = $validateQr->data->payee;
$metadata = $validateQr->data->metadata;
$signature = $validateQr->data->order_signature;
$additionalData = $validateQr->data->additional_data;
$amount = $validateQr->data->amount;

$pay = $gojek->payDynamicQR($payee, $additionalData, $metadata, $signature, $amount, $pin);
print_r($pay);
