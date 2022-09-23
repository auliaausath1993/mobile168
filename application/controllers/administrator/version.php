<?php







/* Class Main */



class Version extends CI_Controller



{



	function __construct()



	{



		parent::__construct();



	}	



	



	public function index()



	{



		$this->get_data_version();



	}





	// Json not mraz

	

	function get_data_version()

	{

		


		$data_version_update = $this->main_model->get_list('data_version_update',array('perpage' => 1,'offset' => 0),array('by' => 'id','sorting' => 'DESC'));

		$data_version = $data_version_update->row_array();

		

			$version = array(

								'id' => $data_version['id'],

								'name_version' => $data_version['name_version'],

								'number_version' => $data_version['number_version'],

								'date' => $data_version['date']

							);



		

		echo json_encode($version);

	}

}