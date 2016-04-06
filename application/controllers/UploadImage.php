<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadImage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('upload');
        $this->load->model('Image');
    }

    public function index()
    {

        $imagesArray = $this->Image->select_images();

        foreach($imagesArray as $key => $value) {
            if (file_exists($this->config->item('PATH_IMAGE').$value->image)) {
                    $result = uploadImage([
                        'PATH_IMAGE' => $this->config->item('PATH_IMAGE') . $value->image,
                        'PATH_COOKIE' => $this->config->item('PATH_COOKIE'),
                        'title' => $value->title
                    ]);
                print_r($result);
            }
        }
//
//        print_r($imagesArray);
//        $this->output
//            ->set_status_header($result['status'])
//            ->set_output(json_encode($result['data']));к3
    }
}

