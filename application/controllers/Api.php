<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
		{
			parent::__construct();
		  $this->load->library('instagram');
		}

	public function index()
	{
		echo "sasha";
		//$this->load->view('welcome_message');
	}
}
