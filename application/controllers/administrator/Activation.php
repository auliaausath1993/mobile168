<?php



/* Class Main */

class Activation extends CI_Controller

{

	function __construct()

	{

		parent::__construct();

	}	

	

	public function index()

	{

		$this->load->view('administrator/page_activation');

	}


	// Json not mraz
	
	function activation_process()
	{
		
		$nama_domain = $this->config->item('tokomobile_domain');
		$code = $this->input->post('code');
		$expired_date = $this->input->post('expired_date');
		$paket = $this->input->post('paket');
		$token = $this->input->post('token');

		$data_license_db = $this->main_model->get_detail('data_license',array('id' => 1));

		if($data_license_db['token'] == $token)
		{
			// file directory
			$name_folder = substr($nama_domain,0,-4);
			$file = 'http://tokomobile.co.id/gen/file_config_client/'.$name_folder.'/tokomobile.zip' ;
			$newfile = './application/config/tokomobile.zip';
			// copy file

			copy($file, $newfile);


			// Unzip File
			$zip = new ZipArchive;
			$output_file = "./application/config/";
			$get_directory = "./application/config/tokomobile.zip";
			if ($zip->open($get_directory) === TRUE) {
			    $zip->extractTo($output_file);
			    $zip->close();
			} else {
			    echo 'failed';
			}

			unlink($get_directory); 

			$data_update = array(
								'activation_date' => date('Y-m-d'),
								'expired_date' => $expired_date,
								'activation_code' => $code,
								'paket' => $paket,
								'status' => 'Verified'
								);

			$this->db->update('data_license',$data_update);

			$data_return = array('status' => 'Success');
		}	
		else
		{
			$data_return = array('status' => 'Failed');
		}	

		echo json_encode($data_return);
		
	}
}