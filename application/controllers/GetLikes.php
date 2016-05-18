<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetLikes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('upload');
        $this->load->helper('request');
//        $this->load->model('image');
//        $this->load->model('parser');
//        $result = $this->parser->getSources("jokes");
//        $this->load->library('parser/vk', $result);
    }

    public function index()
    {

        echo 'test';
//        $a = file_get_contents('http://insta.tflop.ru/');

        $result = SendRequest(array(
            "url" => "https://api.instagram.com/oauth/authorize?client_id=9b525fd1372442148749c6ee3a874209&redirect_uri=http://insta.tflop.ru/auth.php&scope=basic+likes+comments+relationships&response_type=code",
            'useCookie' => true,
            'PATH_COOKIE' => $this->config->item('PATH_COOKIE')
        ));

        print_r($result);


//        $this->output
//            ->set_status_header(200)
//            ->set_output('Success');
//
    }

}

