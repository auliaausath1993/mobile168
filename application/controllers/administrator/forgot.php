<?php



/* Class Main */

class Forgot extends CI_Controller

{

	function __construct()

	{

		parent::__construct();
		$this->load->helper('string');
	}



	public function index()
	{

		$this->load->view('administrator/page_forgot_password');

	}
	function reset_password($code = null)
	{
		$user = $this->main_model->get_detail('users',array('token' => $code));
		if($user != null)
		{
			$data['code'] = $code;
			$this->load->view('administrator/page_reset_password',$data);
		}
		else
		{
			$data['invalid'] = 'INVALID URL !!';
			$this->load->view('administrator/page_blank',$data);
		}
	}
	function reset_password_process()
	{

			$password1 = $this->input->post('password');
			$password2 = $this->input->post('password_confirm');
			$code = $this->input->post('code');
			if($password1 != $password2)
			{
				$this->session->set_flashdata('message','<div class="alert alert-danger">Password confirmasi anda salah atau tidak sama dengan password pertama </div>');
				redirect('administrator/forgot/reset_password/' . $code);
			}
			else
			{
				$data_update = array(
									'user_pass'=> $this->encrypt->encode($password1),
									'token'=> null
									);
				$where = array('token' => $code);
				$this->db->update('users',$data_update,$where);
				$this->session->set_flashdata('message','<div class="alert alert-success">Password anda berhasil diubah silahkan login dengan password yang baru</div>');
				redirect('administrator/login');

			}

	}
	function check_email_user()
	{
		$email = $this->input->post('email');
		$cek = $this->main_model->get_detail('users',array('user_email' => $email));
		if($cek != null)
		{
			$code = random_string('alnum', 25);

			$this->load->helper('email');
			$this->load->library('email');
			$id = $cek['id'];
			$username = $cek['user_name'];
			$name = $cek['user_fullname'];
			$url = base_url().'administrator/forgot/reset_password/'.$code;


				$to = $email;

				$headers  = "From: system@".$this->config->item('tokomobile_domain')." \r\n";
				$headers .= "Content-type: text/html\r\n";

				$subject = 'Konfirmasi Reset Password Admin';
				$content = '<html>
								<body>
									<h4>Email From Admin Tokomobile</h4>
									<p>Email : '.$to.'</p>

									<p>Message :</p>
									<br/>
									<p>Anda sudah melakukan permohonan untuk melakukan reset password,berikut data user acount anda</p>
									<p>ID : '.$id.'</p>
									<p>Username : '.$username.'</p>
									<p>Name : '.$name.'</p>
									<p>Untuk melanjutkan proses reset password admin anda silahkan kunjungi halaman berikut ini :</p>
									<p>'.$url.'</p>
									<hr/>
								</body>
							</html>';

				$sendmail = mail($to,$subject,$content,$headers);
				if($sendmail == False) {
					$this->session->set_flashdata('message','<div class="alert alert-danger">Data gagal dikirim ke email anda,silahkan hubingi admin</div>');
					redirect('administrator/forgot');
				}
				else
				{
					$data_update = array('token' => $code);
					$where = array('user_email' => $email);
					$this->db->update('users',$data_update,$where);
					$this->session->set_flashdata('message','<div class="alert alert-success">Kami sudah mengirimkan data verifikasi ke alamat email anda, silahkan diperiksa</div>');
					redirect('administrator/forgot');
				}

		}
		else
		{
			$this->session->set_flashdata('message','<div class="alert alert-danger">Email anda tidak terdaftar, silahkan masukan alamat email valid anda </div>');
			redirect('administrator/forgot');
		}
	}




}