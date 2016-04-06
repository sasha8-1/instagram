<?php

class Image extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function select_images()
    {
        $query = $this->db->get('post');
        return $query->result();
    }
}