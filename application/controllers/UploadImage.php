<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadImage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('upload');
    }

    public function index()
    {

        $result = uploadImage([
        'PATH_IMAGE' => $this->config->item('PATH_IMAGE')."picture.jpg",
        'PATH_COOKIE' => $this->config->item('PATH_COOKIE'),
        'title' => 'test'
        ]);
        $this->output
            ->set_status_header($result['status'])
            ->set_output(json_encode($result['data']));
    }
}

