<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GetImages extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('image');
        $this->load->model('parser');
        $result = $this->parser->getSources("jokes");
        $this->load->library('parser/vk', $result);
    }

    public function index()
    {
        // get all images
        $images = $this->vk->getImages();

        // update last date in db
        $this->parser->update_last_date('jokes', array('last_update' => date("Y-m-d H:i:s")));

        // save all images in folder
        $path = 'assets/images/';
        foreach($images as $key => $value) {
            $expansion = new SplFileInfo($value->url);
            $fileName = uniqid('post_', true).'.'.$expansion->getExtension();
            $result = saveFile($value->url, $path.$fileName);
            if ($result == 200) {
                $this->image->setImages(array(
                    'title' => $value->text,
                    'image' => $fileName
                ));
            }
        }

        $this->output
            ->set_status_header(200)
            ->set_output('Success');

    }

}

