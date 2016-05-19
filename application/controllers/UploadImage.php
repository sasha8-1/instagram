<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadImage extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('upload');
        $this->load->model('Image');
        set_time_limit(0);
    }

    public function index()
    {
        $amount = (int)$this->input->get('amount');

        $imagesArray = $this->Image->getImages(0, $amount);

        foreach ($imagesArray as $key => $value) {
            if (file_exists($this->config->item('PATH_IMAGE') . $value->image)) {
                $pathImage = $this->config->item('PATH_IMAGE') . $value->image;
                $result = uploadImage([
                    'PATH_IMAGE' => $pathImage,
                    'PATH_COOKIE' => $this->config->item('PATH_COOKIE'),
                    'title' => $value->title
                ]);
                sleep(30);
                unlink($pathImage);
                $this->Image->deleteImage($value->id);
            }
        }

        print_r($imagesArray);
//        $this->output
//            ->set_status_header($result['status'])
//            ->set_output(json_encode($result['data']));
    }
}

