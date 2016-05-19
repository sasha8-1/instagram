<?php

class Image extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function getImages($start, $end)
    {
        $query = $this->db->get('post', $end, $start);
        return $query->result();
    }
    function setImages($data) {
        $this->db->insert('post', $data);
    }
    function deleteImage($id) {
        $this->db->delete('post', array('id' => $id));
    }
}