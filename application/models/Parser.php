<?php

class Parser extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function getSources($type)
    {
        $this->db->where('type', $type);
        $this->db->select('url, last_update');
        $query = $this->db->get('source');
        return $query->result();
    }
    function update_last_date($type, $data) {
        $this->db->where('type', $type);
        $this->db->update('source', $data);
    }
}