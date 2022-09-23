<?php

class Lang extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$ref = $this->input->post('ref');
		$language = $this->input->post('language');
		
		$this->session->unset_userdata('lang_active');
		$this->session->set_userdata('lang_active',$language);
		
		redirect($ref);
	}
	
	function set($language)
	{
		$this->session->unset_userdata('lang_active');
		$this->session->set_userdata('lang_active',$language);
		
		redirect('main/home');
	}
}	