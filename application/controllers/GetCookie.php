<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetCookie extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('cookie');
    }

    public function index()
    {

        $result = getCookie([
            'userName' => $this->config->item('userName'),
            'password' => $this->config->item('password'),
            'PATH_COOKIE' => $this->config->item('PATH_COOKIE')
        ]);
        $this->output
            ->set_status_header($result['status'])
            ->set_output(json_encode($result['data']));
    }
    
}

