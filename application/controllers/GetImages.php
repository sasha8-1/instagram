<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetImages extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('parser');
        $result = $this->parser->select_sources("jokes");
        $this->load->library('parser/vk', $result);
    }

    public function index()
    {


        $ownerId = $this->vk->getData();
        print_r($ownerId);

//        $result = getCookie([
//            'userName' => $this->config->item('userName'),
//            'password' => $this->config->item('password'),
//            'PATH_COOKIE' => $this->config->item('PATH_COOKIE')
//        ]);
//        $this->output
//            ->set_status_header(json_encode($result))
//            ->set_output(json_encode($result['data']));
    }

}

