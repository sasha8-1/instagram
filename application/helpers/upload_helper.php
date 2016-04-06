<?php

$ci = &get_instance();
$ci->load->helper('request');

function GetPostData($filename) {
    if(!$filename) {
        echo "The image doesn't exist ".$filename;
    } else {
        $post_data = array('device_timestamp' => time(),
            'photo' => new CURLFile($filename));
        return $post_data;
    }
}

function postingImage($conf) {

    $image = GetPostData($conf['PATH_IMAGE']);

    $agent = GenerateUserAgent();

    $dataUpload = SendRequest(array(
        'url' => 'https://i.instagram.com/api/v1/media/upload/',
        'post' => $image,
        'agent' => $agent,
        'useCookie' => true,
        'PATH_COOKIE' => $conf['PATH_COOKIE']
    ));

    $response = json_decode($dataUpload[1]);

    if (empty($response) || $response->status != 'ok') {
        return $response = array(
            'status' => $response->status,
            'data' => $response
        );
    }

    $guid = GenerateGuid();
    $device_id = "android-".$guid;
    $requestData = '{"device_id":"'.$device_id.'","guid":"'.$guid.'","media_id":"'.$response->media_id.'","caption":"'.trim($conf['title']).'","device_timestamp":"'.time().'","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
    $sig = GenerateSignature($requestData);
    $new_data = 'signed_body='.$sig.'.'.urlencode($requestData).'&ig_sig_key_version=4';

    $response = SendRequest([
        'url' => 'https://i.instagram.com/api/v1/media/configure/',
        'post' => $new_data,
        'agent' => $agent,
        'useCookie' => true,
        'PATH_COOKIE' => $conf['PATH_COOKIE']
    ]);
    $data = json_decode($response[1]);

    return array(
        'status' => (!empty($data) && $data->status == 'ok' ? 200 : 400),
        'data' => $data
    );

}

function uploadImage($conf, $depth = 0) {
    $depth = (int)$depth;
    $depth = $depth + 1;
    $result = postingImage($conf);
    if ($result['status'] != 200 && $depth < 3) {
        // hack
        file_get_contents("http://localhost/instagram/index.php/GetCookie/");
        return uploadImage($conf, $depth);
    } else if ($result['status'] != 200 && $depth >= 3) {
        log_message('error', 'Don\'t upload Image');
        throw new Exception('Error');
    } else {
        return $result;
    }
}
