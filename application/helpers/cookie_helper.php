<?php

$ci = &get_instance();
$ci->load->helper('request');

function getCookie($userData)
{

    $agent = GenerateUserAgent();
    $guid = GenerateGuid();
    $device_id = "android-" . $guid;

    $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","username":"' . $userData['userName'] . '","password":"' . $userData['password'] . '","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
    $sig = GenerateSignature($data);
    $data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';

    $response = SendRequest(array(
        'url' => 'accounts/login/',
        'post' => $data,
        'agent' => $agent,
        'PATH_COOKIE' => $userData['PATH_COOKIE']
    ));

    $data = json_decode($response[1]);

    return array(
        'status' => (!empty($data) && $data->status == 'ok' ? 200 : 400),
        'data' => $data
    );

}
