<?php

$host = 'https://api.github.com';
$resource = '/';
$method = 'GET';
$body = '';
$headers = [];

require_once '../src/httpclient.php';
$client = new HTTPClient($host);

$response = $client->RunCommand($resource,$method,$body,$headers);

if (isset($response['header']['set-cookie'])) {
    $cookies = $response['header']['set-cookie'];
    file_put_contents('cookies.txt',$cookies);
}

print_r($response);
