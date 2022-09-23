<?php

/* Class Main */
class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		// $this->load->view('administrator/page_login');
		$this->load->view('administrator/page_login_php7');
	}

	public function logout()
	{
		$this->load->library('auth');
		$this->auth->logout();
	}

	public function login_process_php7()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$this->load->model('Admin_model');
		if($this->Admin_model->login_user($username,$password))
		{
			redirect('administrator/main');
		}
		else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Maaf, Username dan Password tidak cocok</div>');
			redirect('administrator/login');
		}
	}

	public function login_process()
	{
		$this->form_validation->set_rules('username','Username','required');
		$this->form_validation->set_rules('password','Password','required');

		if($this->form_validation->run() == TRUE)
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');

			$this->load->model('Admin_model');

			$check = $this->Admin_model->check_user($username,$password);

			if ($check == FALSE)
			{
				$this->load->model('main_model');
				$get_data_user = $this->main_model->get_detail('users',array('user_name' => $username));
				$get_data_user1 = $this->main_model->get_detail('users',array('user_pass' => $password));
				$pincode = $this->main_model->get_detail('content', array('name' => 'pincode'));
				$data = array(
					'webadmin_login'      => TRUE,
					'webadmin_user_id'    => $get_data_user['id'],
					'webadmin_user_name'  => $get_data_user['user_name'],
					'webadmin_user_token'  => $get_data_user1['user_pass'],
					// 'webadmin_user_token' => sha1($password),
					'user_akses_menu'     => explode(',', $get_data_user['akses_menu']),
					'webadmin_user_level' => $get_data_user['user_level'],
					'pincode'             => $pincode['value']
				);

				$this->session->set_userdata($data);
				$this->session->set_userdata('file_manager',true);
				redirect('administrator/main');
			}
			else if($check == TRUE)
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger">Maaf, Username dan Password tidak cocok</div>');
				redirect('administrator/login');
			}
		}
		else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Maaf, Lengkapi Terlebih dahulu Form Anda</div>');
			redirect('administrator/login');
		}
	}
}