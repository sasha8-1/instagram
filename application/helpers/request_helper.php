<?php

function SendRequest($data) {
    $ch = curl_init();
    $get = array_key_exists('get', $data) ? "?".http_build_query($data['get']) : "";
    curl_setopt($ch, CURLOPT_URL, $data['url'].$get);
    if (array_key_exists('agent', $data)) curl_setopt($ch, CURLOPT_USERAGENT, $data['agent']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    if (array_key_exists('post', $data)) {
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
    }

    if (array_key_exists('useCookie', $data)) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $data['PATH_COOKIE'].'cookies.txt');
    }

    if (array_key_exists('PATH_COOKIE', $data) && !array_key_exists('useCookie', $data)) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $data['PATH_COOKIE'].'cookies.txt');
    }

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($http, $response);
}

function saveFile($url, $filePatch) {
    $ch = curl_init($url);
    $fp = fopen($filePatch, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    return $http;
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

function GenerateSignature($data)
{
    return hash_hmac('sha256', $data, 'b4a23f5e39b5929e0666ac5de94c89d1618a2916');
}
