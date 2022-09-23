<?php

class Admin_model extends CI_Model
{
	public $table = "users";
	public $user_password;

	public function __construct()
	{
		parent::__construct();
		$this->user_password = "";
	}

	public function check_user($username = "",$password = "")
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

		$query = $this->db->get_where($this->table,array('user_name' => $username,'user_pass' => $password));

		$result = $query->row_array();

		$this->user_password = $result['user_pass'];

		// $this->user_password = $this->encrypt->decode($result['user_pass']);
		// echo json_encode($this->user_password);
		// exit;

		if(($query->num_rows() > 0 ) AND ($password === $this->user_password))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// Log user in
	public function login($username, $password){
   // Validate
		$this->db->where('user_name', $username);
		$this->db->where('user_pass', $password);

		$result = $this->db->get('users');

		if($result->num_rows() == 1){
			return $result->row(0)->id;
		} else {
			return false;
		}
	}


	function login_user($username,$password)
	{
        $query = $this->db->get_where('users',array('user_name'=>$username));
        if($query->num_rows() > 0)
        {
            $data_user = $query->row();
            if (password_verify($password, $data_user->user_pass)) {
                $this->session->set_userdata('user_name',$username);
                $this->session->set_userdata('user_akses_menu',$data_user->akses_menu);
				$this->session->set_userdata('webadmin_user_id',$data_user->id);
				$this->session->set_userdata('webadmin_user_level',$data_user->user_level);
				$this->session->set_userdata('is_login',TRUE);
                return TRUE;
            } else {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
	}
	
    function cek_login()
    {
        if(empty($this->session->userdata('is_login')))
        {
			redirect('administrator/login');
		}
    }



}