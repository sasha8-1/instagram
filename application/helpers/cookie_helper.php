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

function GetPostData($filename)
{
    if (!$filename) {
        echo "The image doesn't exist " . $filename;
    } else {
        $post_data = array('device_timestamp' => time(),
            'photo' => '@' . $filename);
        return $post_data;
    }
}


function getCookie($userData)
{

// Set the caption for the photo
    $caption = "Test caption";

// Define the user agent
    $agent = GenerateUserAgent();

// Define the GuID
    $guid = GenerateGuid();

// Set the devide ID
    $device_id = "android-" . $guid;

    /* LOG IN */
// You must be logged in to the account that you wish to post a photo too
// Set all of the parameters in the string, and then sign it with their API key using SHA-256
    $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","username":"' . $userData['userName'] . '","password":"' . $userData['password'] . '","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
    $sig = GenerateSignature($data);
    $data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';

    $login = SendRequest(array(
        'url' => 'accounts/login/',
        'post' => $data,
        'agent' => $agent,
        'PATH_save_Cookie' => $userData['PATH_IMAGE']
    ));

    if (strpos($login[1], "Sorry, an error occurred while processing this request.")) {
        echo "Request failed, there's a chance that this proxy/ip is blocked";
    } else {
        if (empty($login[1])) {
            echo "Empty response received from the server while trying to login";
        } else {
            // Decode the array that is returned
            $obj = @json_decode($login[1], true);

            if (empty($obj)) {
                echo "Could not decode the response: " . $body;
            } else {
                // Post the picture
                $data = GetPostData($userData['PATH_IMAGE']);
                //$post = SendRequest('media/upload/', true, $data, $agent, true);

                if (empty($post[1])) {
                    echo "Empty response received from the server while trying to post the image";
                } else {
                    // Decode the response
                    $obj = @json_decode($post[1], true);

                    if (empty($obj)) {
                        echo "Could not decode the response";
                    } else {
                        $status = $obj['status'];

                        if ($status == 'ok') {
                            // Remove and line breaks from the caption
                            $caption = preg_replace("/\r|\n/", "", $caption);

                            $media_id = $obj['media_id'];
                            $device_id = "android-" . $guid;
                            $data = '{"device_id":"' . $device_id . '","guid":"' . $guid . '","media_id":"' . $media_id . '","caption":"' . trim($caption) . '","device_timestamp":"' . time() . '","source_type":"5","filter_type":"0","extra":"{}","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
                            $sig = GenerateSignature($data);
                            $new_data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';

                            // Now, configure the photo
                            $conf = SendRequest('media/configure/', true, $new_data, $agent, true);

                            if (empty($conf[1])) {
                                echo "Empty response received from the server while trying to configure the image";
                            } else {
                                if (strpos($conf[1], "login_required")) {
                                    echo "You are not logged in. There's a chance that the account is banned";
                                } else {
                                    $obj = @json_decode($conf[1], true);
                                    $status = $obj['status'];

                                    if ($status != 'fail') {
                                        echo "Success";
                                    } else {
                                        echo 'Fail';
                                    }
                                }
                            }
                        } else {
                            echo "Status isn't okay";
                        }
                    }
                }
            }
        }
    }
}
