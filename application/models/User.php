<?php

class User extends CI_Model  {

  function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    public function insert_ignore($table,array $data) {
    $_prepared = array();
    foreach ($data as $col => $val)
        $_prepared[$col] = $this->db->escape($val);
    $this->db->query('INSERT IGNORE INTO `'.$table.'` ('.implode(',',array_keys($_prepared)).') VALUES('.implode(',',array_values($_prepared)).');');
}

    function insert_user($data)
    {
        $this->insert_ignore('user', $data);
    }
    //
    // function update_entry()
    // {
    //     $this->title   = $_POST['title'];
    //     $this->content = $_POST['content'];
    //     $this->date    = time();
    //
    //     $this->db->update('entries', $this, array('id' => $_POST['id']));
    // }

}
