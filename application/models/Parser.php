<?php

class Parser extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function select_sources($type)
    {
        $this->db->where('type', $type);
        $this->db->select('url, last_update');
        $query = $this->db->get('source');
        return $query->result();
    }
}