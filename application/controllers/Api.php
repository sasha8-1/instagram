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
		$responceUrl = $this->instagram->instagramLogin();
		$this->output->set_status_header('200');
		$this->output->set_output($responceUrl);
	}
	public function check() {
		$code = $this->input->get("code");
		$result = $this->instagram->authorize($code);
		if (isset($result->code) && $result->code == 400) {
			$this->output->set_status_header('400');
			$this->output->set_output("Error.Acess Token is empty");
		} else {
			$this->load->model('User');
			$data = array("access_token" => $result->access_token,
										"username"=> $result->user->username,
										"full_name"=>$result->user->full_name,
										"user_id"=>$result->user->id);
			$this->User->insert_user($data);
			$this->output->set_status_header('200');
			$this->output->set_output(json_encode(array("code"=>200,"message"=>"success")));
		}

	}
}
