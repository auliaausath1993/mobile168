<?php 

class Active extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('uri');
	}	
	
	function active_link($link)
	{
		if($this->uri->segment(3) === $link)
		{
			echo ' active ';
		}
	}	


	function expand($var)
	{
		if($this->uri->segment(3) === $var)
		{
			echo ' open ';
		}
	}	
}