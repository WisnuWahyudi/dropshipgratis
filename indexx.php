<?php

//menentukan kunci server
$server_key = "SB-Mid-server-N100kCovRufct-L7istavvOJ";

//melakukan set (jika dia production / sandbox)
$is_production = false;
$api_url = $is_production ? 'https://dashboard.midtrans.com/transactions' : 
                'https://dashboard.sandbox.midtrans.com/transactions';

if(!strpos($_SERVER['REQUEST_URI'], '/charge'))
{
    http_reponse_code(404);
    echo "wrong path, make sure it's `/charge`"; exit();
}
                
if($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    http_reponse_code(404);
    echo "Page not found or wrong HTTP request method is used"; exit();
}

$request_body = file_get_contents('php://input');
header('Content-Type: application/json');

$charge_result = chargeAPI($api_url, $server_key, $request_body);

http_response_code($charge_result['http_code']);
echo $charge_result['body']


function chargeAPI($api_url, $server_key, $request_body)
{
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $api_url,
        CURLPT_RETURNTRANSFER => 1,
        CURLOPT_POST => 1,
        CURLPT_HEADER => 0,
        // Tambahkan header ke permintaan, termasuk Otorisasi yang dihasilkan dari kunci server
        CURLPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($server_key . ':')
        ),
        CURLPT_POSTFIELDS => $request_body
    );
    curl_setopt_array($ch, $curl_options);
    $result = array(
        'body' => curl_exec($ch),
        'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    );
    return $result;
}



