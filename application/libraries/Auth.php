<?php

/* Class Main */

class Auth {
	
	private $session;

	public function __construct()
	{
		$ci = &get_instance();
		$this->session = $ci->session;
	}
	
	public function check()
	{
		if($this->session->userdata('login') === FALSE)
		{
			redirect('administrator/main/login');
		}
	}
	
	public function get_userdata()
	{
		$username = $this->session->userdata('username');
		return $username;
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('administrator/main');
	}
	
}	

