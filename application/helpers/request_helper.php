<?php

function SendRequest($data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://i.instagram.com/api/v1/' . $data['url']);
    if (array_key_exists('agent', $data)) curl_setopt($ch, CURLOPT_USERAGENT, $data['agent']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    if (array_key_exists('post', $data)) {
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post']);
    }

    if (array_key_exists('useCookie', $data)) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    }

    if (array_key_exists('PATH_save_Cookie', $data) && !array_key_exists('useCookie', $data)) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $data['PATH_save_Cookie'].'cookies.txt');
    }

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($http, $response);
}