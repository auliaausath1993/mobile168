<?php

// Language Library
class My_lang extends CI_Controller
{
	var $default_lang;
	
	function __construct() 
	{
		parent::__construct();
		$this->default_lang = "en";
	}
	
	function load_lang()
	{
		if(!$this->session->userdata('lang_active'))
		{
			$this->session->set_userdata('lang_active',$this->default_lang);
		}
	}	
}	
		
	