<?php

$ci = &get_instance();
$ci->load->helper('request');

function GenerateGuid()
{
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(16384, 20479),
        mt_rand(32768, 49151),
        mt_rand(0, 65535),
        mt_rand(0, 65535),
        mt_rand(0, 65535));
}

function GenerateUserAgent()
{
    $resolutions = array('720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
    $versions = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
    $dpis = array('120', '160', '320', '240');

    $ver = $versions[array_rand($versions)];
    $dpi = $dpis[array_rand($dpis)];
    $res = $resolutions[array_rand($resolutions)];

    return 'Instagram 4.' . mt_rand(1, 2) . '.' . mt_rand(0, 2) . ' Android (' . mt_rand(10, 11) . '/' . mt_rand(1, 3) . '.' . mt_rand(3, 5) . '.' . mt_rand(0, 5) . '; ' . $dpi . '; ' . $res . '; samsung; ' . $ver . '; ' . $ver . '; smdkc210; en_US)';
}

function GenerateSignature($data)
{
    return hash_hmac('sha256', $data, 'b4a23f5e39b5929e0666ac5de94c89d1618a2916');
}


function getCookie($userData)
{

    $agent = GenerateUserAgent();
    $guid = GenerateGuid();
    $device_id = "android-" . $guid;

    $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","username":"' . $userData['userName'] . '","password":"' . $userData['password'] . '","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
    $sig = GenerateSignature($data);
    $data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';

    $login = SendRequest(array(
        'url' => 'accounts/login/',
        'post' => $data,
        'agent' => $agent,
        'PATH_save_Cookie' => $userData['PATH_IMAGE']
    ));

    $data = json_decode($login[1]);

    if (!empty($data) && $data->status == 'ok') {
        $response = array(
            'status' => 200,
            'data' => $data
        );
    } else {
        $response = array(
            'status' => 400
        );
    }
    return $response;
}