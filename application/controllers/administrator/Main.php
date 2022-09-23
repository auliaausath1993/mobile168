<?php


/*

Class Main

Author : Tokomobile

Version : 1.2

Last Revision : 26-Okt-2014

*/


class Main extends CI_Controller {


	Public
	$tokomobile,
	$config_tarif,
	$client_name,
	$package,
	$domain,
	$token,
	$topics,
	$total_max_product,
	$total_max_customer,
	$total_publish_product,
	$total_customer,
	$total_available_space_product,
	$total_available_space_customer,
	$registrationIds = array();

	Public $base_url_api = "http://api.tokomobile.co.id/ongkir/development/api/";

	public function __construct()
	{

		parent::__construct();
		$this->load->model('Admin_model');
		$this->Admin_model->cek_login();


		date_default_timezone_set("Asia/Jakarta");
		$this->check_activation();
		// $this->check_login();

		$this->domain = "mobileapp168.com";
		$this->token = "602b6defc842336e32c04d4d1743fcf8";
		$this->topics = '/topics/'.str_replace('.', '', $this->domain);

		$this->config_tarif =  $this->config->item('tokomobile_tarif_jne');
		$this->client_name =  $this->config->item('tokomobile_online_shop');
		$this->package =  $this->config->item('tokomobile_package');
		$this->tokomobile_type =  $this->config->item('tokomobile_type');
		$this->tokomobile_white_label =  $this->config->item('tokomobile_white_label');
		$this->total_max_product = $this->config->item('tokomobile_product_limit');
		$this->total_max_customer = $this->config->item('tokomobile_customer_limit');
		$activation = $this->main_model->get_detail('data_license',array('id' => 1));
		$this->actv = $activation['expired_date'];
	}



	private function check_activation()
	{

		$data_check_activation = $this->main_model->get_detail('data_license',array('id' => 1));

		if ($data_check_activation['activation_code'] != null)
		{

			$today = strtotime(date('Y-m-d'));
			$expired_date = strtotime($data_check_activation['expired_date']);

			if($today >= $expired_date)
			{

				$data_update = array('status' => 'Expired');
				$this->db->update('data_license',$data_update);
			}

			if($data_check_activation['status'] != 'Verified' )
			{

				$this->session->sess_destroy();
				redirect('administrator/activation');
			}
		} else {

			$this->session->sess_destroy();
			redirect('administrator/activation');
		}
	}


	/*private function check_login()
	{

		if(($this->session->userdata('webadmin_login') == TRUE) and ($this->session->userdata('webadmin_user_id') != null))
		{

			$data_check_user = $this->main_model->get_detail('users',array('id' => $this->session->userdata('webadmin_user_id')));
			$session_pass = $this->session->userdata('webadmin_user_token');
			$db_pass= $data_check_user['user_pass'];
			// $db_pass_decode = $this->encrypt->decode($db_pass);
			// $db_pass_sha1 = sha1($db_pass_decode);

			if($session_pass != $db_pass_sha1)
			{
				redirect('administrator/login');
			}
		} else {

			redirect('administrator/login');
		}
	}*/


	public function index() {
		$this->dashboard();
	}

	// MENU DASHBOARD
	public function dashboard($offset = 0) {
		$copy_name_tag = $this->main_model->get_detail('content', array('name' => 'copy_name_tag'));
		if ($copy_name_tag['value'] == 0) {
			$products = $this->db->select('id, best_seller, promo')
			->group_start()
			->where('best_seller', 'Ya')
			->or_where('promo', 'Ya')
			->group_end()
			->where('status !=', 'Delete')
			->get('product')->result();
			foreach ($products as $product) {
				if ($product->best_seller == 'Ya') {
					$data_tag = array(
						'product_id' => $product->id,
						'tag_id'     => 2
					);
					$this->db->insert('product_tags', $data_tag);
				}
				if ($product->promo == 'Ya') {
					$data_tag = array(
						'product_id' => $product->id,
						'tag_id'     => 3
					);
					$this->db->insert('product_tags', $data_tag);
				}
			}
			$this->db->update('content', array('value' => 1), array('name' => 'copy_name_tag'));
		}
		$data['output'] = null;
		$data['last_order'] = $this->db->select('OI.customer_id, C.name AS customer_name, P.name_item, OI.qty, OI.subtotal, O.name_customer')
		->from('orders_item OI')
		->join('customer C', 'C.id = OI.customer_id', 'left')
		->join('product P', 'P.id = OI.prod_id', 'left')
		->join('orders O', 'O.id = OI.order_id', 'left')
		->where('OI.order_status', 'Keep')
		->order_by('OI.id', 'DESC')
		->limit(10)
		->get()->result();

		$stock = $this->db->select('count(*) AS total')
		->from('product_variant PV')
		->join('product P', 'PV.prod_id = P.id', 'left')
		->where(array(
			'PV.available !=' => 'Delete',
			'P.status     !=' => 'Delete',
			'PV.stock     <=' => 3
		))->get()->row_array();
		$data['stock_total'] = $stock['total'];

		$perpage = 10;

		$this->load->library('pagination');

		$config = array(
			'base_url'        => base_url().'administrator/main/dashboard',
			'first_url'       => base_url().'administrator/main',
			'per_page'        => $perpage,
			'total_rows'      => $stock['total'],
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		$data_check_activation = $this->actv;

		$expired_date = strtotime($data_check_activation);

		$today_date = strtotime(date('Y-m-d'));

		$selisih_per_hari = 86400; //jumlah detik dalam 1 hari

		$remind = 10; //remind jika sudah masuk $remind hari

		$waktu_reminder = $selisih_per_hari * $remind;

		$sisa_waktu = $expired_date - $today_date;

		$data['available_date'] = $sisa_waktu;

		$data['reminder_date'] = $waktu_reminder;

		$min_stock_product = $this->db->select('PV.prod_id, P.name_item, PV.variant, PV.stock, P.product_type')
		->join('product P', 'PV.prod_id = P.id', 'left')
		->where(array(
			'PV.available !=' => 'Delete',
			'P.status     !=' => 'Delete',
			'PV.stock     <=' => 3
		))
		->get('product_variant PV', $perpage, $offset)->result();

		$duser = $this->main_model->get_detail('users',array('id' => $this->session->userdata('webadmin_user_id')));
		$dbps = $duser['user_pass'];
		$data['nm'] = $this->encrypt->decode($dbps);
		$data['pincode'] = $this->main_model->get_detail('content', array('name' => 'pincode'));
		$data['notifikasi'] = $this->main_model->get_detail('content',array('name' => 'notifikasi'));
		$data['min_stock_product'] = $min_stock_product;
		$data['last_chart_date'] = $this->create_chart_date();
		$data['last_chart_order'] = $this->create_chart_last_ready();
		$data['client_name'] = $this->client_name;
		$total_publish_product = $this->db->select('COUNT(*) AS total')
		->join('product_category', 'product.category_id = product_category.id', 'left')
		->where('product.status', 'Publish')
		->where('product_category.status_category', 'publish')
		->get('product')->row_array();
		$data['total_publish_product'] = $total_publish_product['total'];
		$total_customer = $this->db->select('COUNT(*) AS total')
		->where('status', 'Active')->get('customer')->row_array();
		$data['total_customer'] = $total_customer['total'];
		$this->load->view('administrator/page_main', $data);
	}

	public function create_chart_date() {
		$days = array();
		$today = date('Y-m-d');
		for ($i = 6; $i >= 0 ; $i--) {
			$days[] = date('D, d-M-Y', strtotime($today . ' -' . $i . ' days'));
		}
		return json_encode($days);
	}


	function create_chart_last_ready() {
		$table = 'orders_item';
		$field = 'order_datetime';
		$day[0] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-6, date("Y")));
		$day[1] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-5, date("Y")));
		$day[2] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-4, date("Y")));
		$day[3] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-3, date("Y")));
		$day[4] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-2, date("Y")));
		$day[5] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")));
		$day[6] = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
		$data_day[0] = $this->main_model->get_archive_day($table,$field,$day[0])->num_rows();
		$data_day[1] = $this->main_model->get_archive_day($table,$field,$day[1])->num_rows();
		$data_day[2] = $this->main_model->get_archive_day($table,$field,$day[2])->num_rows();
		$data_day[3] = $this->main_model->get_archive_day($table,$field,$day[3])->num_rows();
		$data_day[4] = $this->main_model->get_archive_day($table,$field,$day[4])->num_rows();
		$data_day[5] = $this->main_model->get_archive_day($table,$field,$day[5])->num_rows();
		$data_day[6] = $this->main_model->get_archive_day($table,$field,$day[6])->num_rows();
		return json_encode($data_day);
	}


	// MENU PESANAN DALAM PROSES
	function last_order_session()
	{
		if($this->input->post('product_name') != '')
		{
			$pro_name = $this->input->post('product_name');
		} else {
			$pro_name = null;
		}

		if($pro_name !='')
		{
			$pro = $this->input->post('product_id');
		} else {
			$pro= null;
		}

		if($this->input->post('customer_name') != null)
		{
			$customer_name = $this->input->post('customer_name');
		} else {
			$customer_name = null;
		}

		if($customer_name != '')
		{
			$customer = $this->input->post('customer_id');
		} else {
			$customer = null;
		}

		if($this->input->post('qty') != '')
		{

			$qty = $this->input->post('qty');
		} else {
			$qty = null;
		}

		if($this->input->post('date_awal') != '')
		{
			$date_awal = $this->input->post('date_awal');
		} else {
			$date_awal = null;
		}

		if($customer_name != null or $pro_name != null or $qty != null or $date_awal != null)
		{
			$cari = $this->input->post('cari');
		} else {
			$cari = null;
		}

		$data_session = array(

			'pro_name' => $pro_name,

			'pro' => $pro,

			'customer_name' => $customer_name,

			'customer' => $customer,

			'qty' => $qty,

			'date_awal' => $date_awal,

			'cari' => $cari
		);

		$this->session->set_userdata($data_session);

		redirect('administrator/main/last_order_search');

	}


	function last_order_process($offset = 0)
	{
		$this->check_hak_akses('last_order_process');
		$data_session = array(

			'pro_name' => null,

			'pro' => null,

			'customer_name' => null,

			'customer' => null,

			'qty' => null,

			'date_awal' => null,

			'cari' => null
		);

		$this->session->set_userdata($data_session);

		$data['output'] = null;

		$data_total = $this->main_model->get_list_where('orders_item',array('order_status' => 'Keep', 'order_payment' => 'Unpaid', 'order_id' => '0'));

		$perpage = 25;

		$this->load->library('pagination');

		$config = array (

			'base_url' => base_url().'administrator/main/last_order_process',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

			'uri_segment' => 4

		);



		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		$data['list_orders'] = $this->main_model->get_list_where('orders_item',array('order_status' => 'Keep', 'order_payment' => 'Unpaid', 'order_id' => '0'),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'id','sorting' => 'ASC'));



		$data['arr'] = array(

			'customer_name' => $this->session->userdata('customer_name'),

			'customer_id' =>$this->session->userdata('customer'),

			'product_id' => $this->session->userdata('pro'),

			'product_name' =>$this->session->userdata('pro_name'),

			'date_awal' =>$this->session->userdata('date_awal'),

			'qty' =>$this->session->userdata('qty'),

			'perpage' =>$perpage,

			'offset' =>$offset

		);

		$this->load->view('administrator/page_last_order',$data);
	}



	function last_order_search($offset=0)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		if($this->session->userdata('customer') != '')
		{
			$this->db->where('customer_id',$this->session->userdata('customer'));
		}

		if($this->session->userdata('pro') != '')
		{
			$this->db->where('prod_id',$this->session->userdata('pro'));
		}

		if($this->session->userdata('qty') != '')
		{
			$this->db->where('qty',$this->session->userdata('qty'));
		}

		if($this->session->userdata('date_awal') != '')
		{

			$date_awal1 = $this->session->userdata('date_awal')." 00:00:01";

			$date_awal2 = $this->session->userdata('date_awal')." 23:59:59";

			$this->db->where('order_datetime >=', $date_awal1);

			$this->db->where('order_datetime <=', $date_awal2);
		}

		$this->db->where('order_status', 'Keep');

		$this->db->where('order_payment', 'Unpaid');

		$data_total = $this->db->get('orders_item');

		$perpage = 25;

		$this->load->library('pagination');

		$config = array (

			'base_url' => base_url().'administrator/main/last_order_search',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

			'uri_segment' => 4

		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;

		if($this->session->userdata('customer') != '')
		{
			$this->db->where('customer_id',$this->session->userdata('customer'));
		}

		if($this->session->userdata('pro') != '')
		{
			$this->db->where('prod_id',$this->session->userdata('pro'));
		}

		if($this->session->userdata('qty') != '')
		{
			$this->db->where('qty',$this->session->userdata('qty'));
		}

		if($this->session->userdata('date_awal') != '')
		{

			$date_awal1 = $this->session->userdata('date_awal')." 00:00:01";
			$date_awal2 = $this->session->userdata('date_awal')." 23:59:59";
			$this->db->where('order_datetime >=', $date_awal1);
			$this->db->where('order_datetime <=', $date_awal2);
		}

		$this->db->where('order_status', 'Keep');
		$this->db->where('order_payment', 'Unpaid');
		$this->db->order_by('id', 'ASC');
		$data['list_orders'] = $this->db->get('orders_item',$perpage,$offset);
		$data['arr'] = array(

			'customer_name' => $this->session->userdata('customer_name'),

			'customer_id' =>$this->session->userdata('customer'),

			'product_id' => $this->session->userdata('pro'),

			'product_name' =>$this->session->userdata('pro_name'),

			'date_awal' =>$this->session->userdata('date_awal'),

			'qty' =>$this->session->userdata('qty'),
		);

		$data['output'] = null;

		$this->load->view('administrator/page_last_order',$data);

	}

	// Menu Last order by variant
	function last_order_process_by_variant($offset = null)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$this->db->distinct();

		$this->db->select('variant_id');

		$this->db->where('order_payment','Unpaid');

		$this->db->where('order_status','Keep');

		$this->db->where('order_id','0');

		$data_total = $this->db->get('orders_item');

		$perpage = 20;

		$this->load->library('pagination');

		$config = array (

			'base_url' => base_url().'administrator/main/last_order_process_by_variant',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'uri_segment' => 4,

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		$this->db->distinct();

		$this->db->select('variant_id');

		$this->db->where('order_payment','Unpaid');

		$this->db->where('order_status','Keep');

		$this->db->where('order_id','0');

		$data['list_product'] = $this->db->get('orders_item',$perpage,$offset);

		$data['output'] = null;

		$this->load->view('administrator/page_last_order_by_variant',$data);

	}



	function list_pesanan_per_variant($variant_id = null,$offset = 0)

	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data_total = $this->main_model->get_list_where('orders_item',array('variant_id' => $variant_id,'order_status' => 'Keep','order_payment' => 'Unpaid'));
		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url' => base_url().'administrator/main/list_pesanan_per_variant',
			'per_page' => $perpage,
			'total_rows' => $data_total->num_rows(),
			'uri_segment' => 4,
			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close' => '</div></div>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>',
			'prev_tag_open' => '<li>',
			'prev_tag_close' => '</li>',
			'next_tag_open' => '<li>',
			'next_tag_close' => '</li>',
			'last_tag_open' => '<li>',
			'last_tag_close' => '</li>',
			'first_tag_open' => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open' => '<li class="active"><a href="#">',
			'cur_tag_close' => '</a></li>',
		);
		$this->pagination->initialize($config);

		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$data['output'] = null;
		$data['list_orders'] = $this->main_model->get_list_where('orders_item',array('variant_id' => $variant_id,'order_status' => 'Keep','order_payment' => 'Unpaid'),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'order_datetime','sorting' => 'DESC'));

		$this->load->view('administrator/page_list_order_variant',$data);

	}


	function  last_order_process_expired($offset = 0)

	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$date = date('Y-m-d H:i:s');

		$data_total = $this->main_model->get_list_where('orders_item',array('due_datetime !='=>'0000-00-00 00:00:00','order_payment' => 'Unpaid','due_datetime <=' => $date,'order_status' => 'Keep'));

		$perpage = 25;

		$this->load->library('pagination');

		$config = array (

			'base_url' => base_url().'administrator/main/last_order_process_expired',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'uri_segment' => 4,

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		$data['output'] = null;

		$data['list_orders'] = $this->main_model->get_list_where('orders_item',array('due_datetime !='=>'0000-00-00 00:00:00','order_payment' => 'Unpaid','due_datetime <=' => $date,'order_status' => 'Keep'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'id','sorting' => 'DESC'));

		$this->load->view('administrator/page_pesanan_jatuh_tempo',$data);
	}

	function order_unpaid_expired()
	{
		$date = date('Y-m-d H:i:s');

		$crud = new grocery_CRUD();

		$crud->set_table('orders')
		->set_subject('Seluruh Pesanan');
		$crud->where('order_payment','Unpaid');
		$crud->where('order_status !=', 'Cancel');
		$crud->where('order_status','Dropship');
		$crud->where('due_datetime <=',$date);
		$crud->where('due_datetime !=','0000-00-00 00:00:00');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->display_as('shipping_from','Pengirim');
		$crud->display_as('shipping_to','Penerima');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->order_by('id','DESC');
		$crud->columns('check','id','customer_id','order_datetime','shipping_from','shipping_to','total','order_payment');
		$crud->callback_column('check',array($this,'callback_customer_checked'));
		$crud->add_action('BATAL', '#', 'administrator/main/cancel_order_expired','btn btn-danger btn-crud btn-cancel-order');
		$crud->add_action('JADIKAN LUNAS', '#', 'administrator/main/order_detail_change_status_expired/Paid','btn btn-success btn-crud');
		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-primary btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_delete();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->callback_column('customer_id',array($this,'callback_customer_name'));
		$data['output'] = $crud->render();
		$data['order_payment'] = 'Dropship Belum Lunas';
		$this->load->view('administrator/page_order',$data);

	}

	function order_rekap_unpaid_expired() {
		$jatuh_tempo_rekap_dropship = $this->main_model->get_detail('content', array('name' => 'jatuh_tempo_rekap_dropship'));
		$date = date('Y-m-d H:i:s');
		$due_date = date('Y-m-d H:i:s', strtotime($date . ' -' . $jatuh_tempo_rekap_dropship['value']));

		$crud = new grocery_CRUD();

		$crud->set_table('orders')
		->set_subject('Seluruh Pesanan');
		$crud->where('order_payment','Unpaid');
		$crud->where('order_status','Keep');
		$crud->where('order_datetime <=', $due_date);
		// $crud->where('due_datetime !=','0000-00-00 00:00:00');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->order_by('id','DESC');
		$crud->columns('check','id','customer_id','order_datetime','total','order_payment');
		$crud->callback_column('check',array($this,'callback_customer_checked'));
		$crud->add_action('BATAL', '#', 'administrator/main/cancel_order_expired','btn btn-danger btn-crud btn-cancel-order');
		$crud->add_action('JADIKAN LUNAS', '#', 'administrator/main/order_detail_change_status_expired/Paid','btn btn-success btn-crud');
		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-primary btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_delete();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->callback_column('customer_id',array($this,'callback_customer_name'));
		$data['output'] = $crud->render();
		$data['order_payment'] = 'Dropship Belum Lunas';
		$this->load->view('administrator/page_order',$data);

	}
	function order_process_to_batal()
	{

		$list_order = $this->input->post('order_item_id');
		$this->form_validation->set_rules('reason_cancel', 'Alasan Pembatalan', 'trim|required');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message', 'Alasan harus diisi');
			redirect('administrator/main/order_rekap_unpaid_expired', 'refresh');
		}

		if($list_order != null)
		{

			foreach($list_order as $order_id):

				$data_update = array('order_payment' => 'Paid');
				$where = array('id' => $order_id);
				$this->db->update('orders',$data_update,$where);

				$data_orders_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status != ' => 'Cancel'));

				foreach($data_orders_item->result() as $orders_item):

					$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));

					$restock = $data_order_item_product['stock'] + $orders_item->qty;

					$data_update = array('stock' => $restock);

					$where = array('id' => $orders_item->variant_id);

					$this->db->update('product_variant',$data_update,$where);

					$stock_histories = array(
						'prod_id'    => $data_order_item_product['prod_id'],
						'variant_id' => $orders_item->variant_id,
						'prev_stock' => $data_order_item_product['stock'],
						'stock'      => $restock,
						'qty'        => $orders_item->qty,
						'user_id'    => $this->session->userdata('webadmin_user_id'),
						'created_at' => date('Y-m-d H:i:s'),
						'note'       => $this->input->post('reason_cancel')
					);
					$this->db->insert('stock_histories', $stock_histories);

					$where = array('id' => $orders_item->id);

					$this->db->update('orders_item', array('order_status' => 'Cancel'), $where);

				endforeach;

			endforeach;

			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah berhasil Batalkan</div>');
			redirect('administrator/main/order_rekap_unpaid_expired', 'refresh');

		} else {

			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			redirect('administrator/main/order_rekap_unpaid_expired', 'refresh');

		}

	}
	function last_order_product($offset = 0)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$perpage = 20;

		$data_total = $this->main_model->get_list('product_variant');

		$config = array(

			'base_url' => base_url().'administrator/main/last_order_product',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'uri_segment' => 4

		);

		$this->load->library('pagination',$config);

		$data['output'] = null;

		$data['list_product'] = $this->main_model->get_list('product_variant',array('perpage' => $perpage,'offset' => $offset),array('by' => 'id','sorting' => 'DESC'));

		$this->load->view('administrator/page_last_order_product',$data);
	}



	function order_per_product($prod_id = 0)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data['product'] =  $this->main_model->get_detail('product',array('id' => $prod_id));

		$crud = new grocery_CRUD();

		$crud->set_table('orders_item')

		->set_subject('Pesanan dari Produk '.$data['product']['name_item']);

		//$crud->display_as('prod_id','Product');
		$crud->where('order_status !=','Cancel');
		$crud->order_by('id','DESC');

		//$crud->set_relation('variant_id','product_variant','variant');

		$crud->unset_fields('order_id');

		$crud->unset_columns('order_id','prod_id','due_datetime','order_status','order_payment');

		$crud->where('prod_id',$prod_id);

		$crud->display_as('variant_id','Varian');

		$crud->display_as('price','Harga');

		$crud->display_as('customer_id','Pembeli');

		$crud->display_as('order_datetime','Tanggal Order');

		$crud->callback_column('customer_id',array($this,'callback_customer_dan_tamu'));

		$crud->callback_column('variant_id',array($this,'callback_variant_name'));

		$crud->unset_add();

		$crud->unset_edit();

		$crud->unset_operations();

		$crud->unset_delete();

		$data['output'] = $crud->render();

		$this->load->view('administrator/page_order_per_product',$data);
	}

	function cancel_order_expired($order_id = null) {
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$this->form_validation->set_rules('reason_cancel', 'Alasan Pembatalan', 'trim|required');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message', 'Alasan harus diisi');
			redirect('administrator/main/order_rekap_unpaid_expired', 'refresh');
		}

		$order = $this->db->get_where('orders', array('id' => $order_id))->row_array();
		if ($order['order_payment'] == 'Paid' && $order['get_point']) {
			$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
			if ($point_reward_status['value'] == 'on') {
				$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
				$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
				$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
				$point_customer = $customer['point'] - (int)$total_point;

				$point_history = array(
					'customer_id' => $order['customer_id'],
					'point_prev'  => $customer['point'],
					'point_out'   => (int)$total_point,
					'point_end'   => (int)$point_customer,
					'order_id'    => $order['id'],
					'note'        => 'Pembatalan order',
					'user_id'     => $this->session->userdata('webadmin_user_id'),
				);
				$this->db->insert('point_histories', $point_history);

				$this->db->where('id', $order['customer_id'])
				->update('customer', array('point' => (int)$point_customer));
			}
		}

		$this->db->update('orders', array('order_status' => 'Cancel'), array('id' => $order_id));

		$data_orders_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id))->result();

		foreach ($data_orders_item as $orders_item) {

			$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));

			$restock = $data_order_item_product['stock'] + $orders_item->qty;

			$data_update = array('stock' => $restock);

			$where = array('id' => $orders_item->variant_id);

			$this->db->update('product_variant',$data_update,$where);

			$stock_histories = array(
				'prod_id'    => $data_order_item_product['prod_id'],
				'variant_id' => $orders_item->variant_id,
				'prev_stock' => $data_order_item_product['stock'],
				'stock'      => $restock,
				'qty'        => $orders_item->qty,
				'user_id'    => $this->session->userdata('webadmin_user_id'),
				'created_at' => date('Y-m-d H:i:s'),
				'note'       => $this->input->post('reason_cancel')
			);
			$this->db->insert('stock_histories', $stock_histories);

			$where = array('id' => $orders_item->id);

			$this->db->update('orders_item', array('order_status' => 'Cancel'), $where);
		}


		$this->session->set_flashdata('message','<div class="alert alert-success">Pesanan telah dibatalkan</div>');

		redirect('administrator/main/order_rekap_unpaid_expired');
	}


	function cancel_order_process($order_item_id = null)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$this->form_validation->set_rules('reason_cancel', 'Alasan Pembatalan', 'trim|required');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message', 'Alasan harus diisi');
			redirect('administrator/main/last_order_process', 'refresh');
		}
		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		$data_order_item = $this->main_model->get_detail('orders_item',array('id' => $order_item_id));
		$this->db->trans_start();
		if ($data_value_stock ['value'] != 3 ) {
			// Re-stock
			$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $data_order_item['variant_id']));

			$restock = $data_order_item_product['stock'] + $data_order_item['qty'];

			$data_update = array('stock' => $restock);

			$where = array('id' => $data_order_item['variant_id']);

			$this->db->update('product_variant',$data_update,$where);

			$stock_histories = array(
				'prod_id'    => $data_order_item_product['prod_id'],
				'variant_id' => $data_order_item['variant_id'],
				'prev_stock' => $data_order_item_product['stock'],
				'stock'      => $restock,
				'qty'        => $data_order_item['qty'],
				'user_id'    => $this->session->userdata('webadmin_user_id'),
				'created_at' => date('Y-m-d H:i:s'),
				'note'       => $this->input->post('reason_cancel')
			);
			$this->db->insert('stock_histories', $stock_histories);
		}

		// Remove order
		$where = array('id' => $order_item_id);

		$this->db->update('orders_item', array('order_status' => 'Cancel'), $where);
		$this->db->trans_complete();
		$this->session->set_flashdata('message','<div class="alert alert-success">Pesanan telah dibatalkan</div>');

		redirect('administrator/main/last_order_process_expired');
	}

	function cancel_order_item($order_item_id = null) {
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data_order_item = $this->main_model->get_detail('orders_item',array('id' => $order_item_id));
		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		$this->form_validation->set_rules('reason_cancel', 'Alasan Pembatalan', 'trim|required');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message', 'Alasan harus diisi');
			redirect('administrator/main/order_detail/' . $data_order_item['order_id'], 'refresh');
		}

		// Re-stock
		$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $data_order_item['variant_id']));
		if ($data_value_stock ['value'] != 3 ) {
			$restock = $data_order_item_product['stock'] + $data_order_item['qty'];
			$data_update = array('stock' => $restock);
			$where = array('id' => $data_order_item['variant_id']);
			$this->db->update('product_variant',$data_update,$where);

			$stock_histories = array(
				'prod_id'    => $data_order_item_product['prod_id'],
				'variant_id' => $data_order_item['variant_id'],
				'prev_stock' => $data_order_item_product['stock'],
				'stock'      => $restock,
				'qty'        => $data_order_item['qty'],
				'user_id'    => $this->session->userdata('webadmin_user_id'),
				'created_at' => date('Y-m-d H:i:s'),
				'note'       => $this->input->post('reason_cancel')
			);
			$this->db->insert('stock_histories', $stock_histories);
		}

		// GET data weight
		$data_product = $this->main_model->get_detail('product',array('id' => $data_order_item['prod_id']));
		$loss_weight = $data_product['weight'] * $data_order_item['qty'];

		// GET Data Order
		$data_order = $this->main_model->get_detail('orders',array('id' => $data_order_item['order_id']));
		$new_weight = $data_order['shipping_weight'] - $loss_weight;

		if($data_order['order_status'] == 'Dropship') {
			// GET Shipping rates
			$ship_rates = $this->get_cost_ekspedisi($data_order['kecamatan_id'],1,$data_order['ekspedisi'],$data_order['tarif_tipe']);
			// Total new Ongkir
			$new_ongkir = ceil($new_weight) * $ship_rates;
		} else{
			$new_ongkir = 0;
		}

		// Kurangi Total
		$new_total = ($data_order['total'] - $data_order['shipping_fee']) - $data_order_item['subtotal'] + $new_ongkir ;
		$new_subtotal = $data_order['subtotal'] - $data_order_item['subtotal'];

		// Update order
		$data_update_order = array(
			'shipping_fee'    => $new_ongkir,
			'shipping_weight' => $new_weight,
			'total'           => $new_total,
			'subtotal'        => $new_subtotal
		);

		$where_order = array('id' => $data_order_item['order_id']);
		$this->db->update('orders',$data_update_order,$where_order);

		// Remove order
		$where = array('id' => $order_item_id);
		$this->db->update('orders_item', array('order_status' => 'Cancel'), $where);
		$this->session->set_flashdata('message','<div class="alert alert-success">Item Pesanan telah dibatalkan</div>');
		redirect('administrator/main/order_detail/'.$data_order_item['order_id']);
	}


	function cancel_order($order_id) {
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$this->form_validation->set_rules('reason_cancel', 'Alasan Pembatalan', 'trim|required');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message', 'Alasan harus diisi');
			redirect('administrator/main/order_detail/' . $order_id, 'refresh');
		}

		$order = $this->db->get_where('orders', array('id' => $order_id))->row_array();
		if ($order['order_payment'] == 'Paid' && $order['get_point']) {
			$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
			if ($point_reward_status['value'] == 'on') {
				$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
				$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
				$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
				$point_customer = $customer['point'] - (int)$total_point;

				$point_history = array(
					'customer_id' => $order['customer_id'],
					'point_prev'  => $customer['point'],
					'point_out'   => (int)$total_point,
					'point_end'   => (int)$point_customer,
					'order_id'    => $order['id'],
					'note'        => 'Pembatalan order',
					'user_id'     => $this->session->userdata('webadmin_user_id'),
				);
				$this->db->insert('point_histories', $point_history);

				$this->db->where('id', $order['customer_id'])
				->update('customer', array('point' => (int)$point_customer));
			}
		}

		$this->db->update('orders', array('order_status' => 'Cancel'), array('id' => $order_id));

		$data_orders_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status != ' => 'Cancel'))->result();

		foreach ($data_orders_item as $orders_item) {

			$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));

			$restock = $data_order_item_product['stock'] + $orders_item->qty;

			$data_update = array('stock' => $restock);

			$where = array('id' => $orders_item->variant_id);

			$this->db->update('product_variant',$data_update,$where);

			$stock_histories = array(
				'prod_id'    => $orders_item->prod_id,
				'variant_id' => $orders_item->variant_id,
				'prev_stock' => $data_order_item_product['stock'],
				'stock'      => $restock,
				'qty'        => $orders_item->qty,
				'user_id'    => $this->session->userdata('webadmin_user_id'),
				'created_at' => date('Y-m-d H:i:s'),
				'note'       => $this->input->post('reason_cancel')
			);
			$this->db->insert('stock_histories', $stock_histories);

			$where = array('id' => $orders_item->id);

			$this->db->update('orders_item', array('order_status' => 'Cancel'), $where);
		}


		$this->session->set_flashdata('message','<div class="alert alert-success">Pesanan telah dibatalkan</div>');

		redirect('administrator/main/order_all');

	}



	function last_order_product_detail($variant_id = null,$offset = 0)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data['output'] = null;
		$perpage = 20;
		$total_pending_order = $this->main_model->get_list_where('orders_item',array('order_status' => 'keep','variant_id' => $variant_id));

		$config = array(

			'base_url' => base_url().'administrator/main/last_order',

			'per_page' => $perpage,

			'total_rows' => $total_pending_order->num_rows(),

			'uri_segment' => 4

		);

		$this->load->library('pagination',$config);

		$data['last_order'] = $this->main_model->get_list_where('orders_item',array('order_status' => 'keep','variant_id' => $variant_id),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'id','sorting' => 'DESC'));

		$this->load->view('administrator/page_last_order',$data);
	}


	function order_all()
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$crud = new grocery_CRUD();

		$crud->set_table('orders')

		->set_subject('Seluruh Pesanan');

		$crud->where('order_status !=','Cancel');

		$crud->display_as('id','ID Pesanan');

		$crud->display_as('order_datetime','Tanggal');

		$crud->display_as('shipping_from','From');

		$crud->display_as('shipping_to','To');

		$crud->display_as('order_payment','Status Pembayaran');

		$crud->order_by('id','DESC');

		$crud->columns('id','customer_id','shipping_from','shipping_to','total','order_payment');

		$crud->display_as('customer_id','Pembeli');

		$crud->callback_column('customer_id',array($this,'callback_customer_dan_tamu_name'));

		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-primary btn-crud');

		$crud->order_by('id','DESC');

		$crud->unset_texteditor('shipping_from');

		$crud->unset_texteditor('shipping_to');

		$crud->unset_add();

		$crud->callback_column('id',array($this,'customer_id'));

		$crud->unset_edit();

		$crud->unset_delete();

		$data['output'] = $crud->render();

		$data['order_payment'] = 'Semua Pesanan';

		$this->load->view('administrator/page_order',$data);
	}


	function order_unpaid()
	{
		$this->check_hak_akses('order_unpaid');
		$crud = new grocery_CRUD();

		$crud->set_table('orders')
		->set_subject('Seluruh Pesanan');
		$crud->where('order_payment','Unpaid');
		$crud->where('order_status !=','Cancel');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->display_as('shipping_from','Pengirim');
		$crud->display_as('shipping_to','Penerima');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->order_by('id','DESC');
		$crud->columns('check','id','customer_id','order_datetime','shipping_from','shipping_to','total','order_payment');
		$crud->callback_column('check',array($this,'callback_customer_checked'));

		//$crud->add_action('BATAL', '#', 'administrator/main/order_detail_change_status_list/Paid','btn btn-danger btn-crud');

		$crud->add_action('JADIKAN LUNAS', '#', 'administrator/main/order_detail_change_status_list/Paid','btn btn-success btn-crud');
		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-primary btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_delete();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->callback_column('customer_id',array($this,'callback_customer_name'));
		$data['output'] = $crud->render();
		$data['order_payment'] = 'Dropship Belum Lunas';
		$this->load->view('administrator/page_order',$data);

	}

	function order_rekap() {
		$this->check_hak_akses('order_rekap');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Pesanan Rekap / COD')
		->where('order_payment','Unpaid')
		->where('order_status','Keep')
		->display_as('id','ID Pesanan')
		->display_as('order_datetime','Tanggal Pesan')
		->display_as('shipping_from','From')
		->display_as('shipping_to','To')
		->display_as('order_payment','Status Pembayaran')
		->display_as('order_status','Jenis Pesanan')
		->display_as('notes','Catatan')
		->display_as('print_nota','Print Nota')
		->display_as('print_ekspedisi','Print Ekspedisi')
		->order_by('id','DESC')
		->columns('id','customer_id','order_datetime','total','order_payment','order_status', 'notes', 'print_nota', 'print_ekspedisi')
		->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-success btn-crud')
		->order_by('id','DESC')
		->unset_texteditor('shipping_from')
		->unset_texteditor('shipping_to')
		->unset_add()
		->unset_delete()
		->unset_read()
		->unset_edit()
		->callback_column('id',array($this,'customer_id'))
		->display_as('customer_id','Pembeli')
		->set_relation('customer_id', 'customer', 'name')
		->callback_column($this->unique_field_name('customer_id'), function ($value, $row) {
			if ($row->customer_id != 0) {
				return $value . ' (' . $row->customer_id . ')';
			} else {
				return '<span style="color:#e23427;">' . $row->name_customer . ' <strong> (Guest)</strong></span>';
			}
		})
		->callback_column('print_nota',array($this,'callback_print_nota'))
		->callback_column('print_ekspedisi',array($this,'callback_print_ekspedisi'));

		$data['output'] = $crud->render();
		$data['order_payment'] = 'Rekap';
		$this->load->view('administrator/page_order',$data);
	}

	function order_piutang() {
		$this->check_hak_akses('order_piutang');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Pesanan Piutang')
		->where('order_payment','Unpaid')
		->where('order_status','Piutang')
		->display_as('id','ID Pesanan')
		->display_as('order_datetime','Tanggal Pesan')
		->display_as('shipping_from','From')
		->display_as('shipping_to','To')
		->display_as('order_payment','Status Pembayaran')
		->display_as('order_status','Jenis Pesanan')
		->display_as('notes','Catatan')
		->display_as('print_nota','Print Nota')
		->display_as('print_ekspedisi','Print Ekspedisi')
		->order_by('id','DESC')
		->columns('id','customer_id','order_datetime','total','order_payment','order_status', 'notes', 'print_nota', 'print_ekspedisi')
		->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-success btn-crud')
		->order_by('id','DESC')
		->unset_texteditor('shipping_from')
		->unset_texteditor('shipping_to')
		->unset_add()
		->unset_delete()
		->unset_read()
		->unset_edit()
		->callback_column('id',array($this,'customer_id'))
		->display_as('customer_id','Pembeli')
		->set_relation('customer_id', 'customer', 'name')
		->callback_column($this->unique_field_name('customer_id'), function ($value, $row) {
			if ($row->customer_id != 0) {
				return $value . ' (' . $row->customer_id . ')';
			} else {
				return '<span style="color:#e23427;">' . $row->name_customer . ' <strong> (Guest)</strong></span>';
			}
		})
		->callback_column('print_nota',array($this,'callback_print_nota'))
		->callback_column('print_ekspedisi',array($this,'callback_print_ekspedisi'));

		$data['output'] = $crud->render();
		$data['order_payment'] = 'Piutang';
		$this->load->view('administrator/page_order',$data);
	}

	function order_paid()
	{
		$this->check_hak_akses('order_paid');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Seluruh Pesanan');
		$crud->where('order_payment','Paid');
		$crud->where('order_status !=','Cancel');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->display_as('date_payment','Tanggal Pelunasan');
		$crud->display_as('shipping_from','From');
		$crud->display_as('shipping_to','To');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('order_status','Jenis Pesanan');
		$crud->order_by('id','DESC');
		$crud->columns('id','customer_id','order_datetime','date_payment', 'total','order_payment','order_status');
		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-success btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_delete();
		$crud->unset_read();
		$crud->unset_edit();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->set_relation('customer_id', 'customer', 'name');
		$crud->callback_column($this->unique_field_name('customer_id'), function ($value, $row) {
			if ($row->customer_id != 0) {
				return $value . ' (' . $row->customer_id . ')';
			} else {
				return '<span style="color:#e23427;">' . $row->name_customer . ' <strong> (Guest)</strong></span>';
			}
		});

		$data['output'] = $crud->render();
		$data['order_payment'] = 'Lunas';
		$this->load->view('administrator/page_order',$data);

	}

	public function recalculate_order($order_id) {
		$order_items = $this->db->get_where('orders_item', array('order_id' => $order_id, 'order_status !=' => 'Cancel'))->result();
		$new_total = 0;
		$new_total_weight = 0;
		foreach ($order_items as $item) {
			$new_total = $new_total + $item->subtotal - $item->discount;
			$product = $this->main_model->get_detail('product', array('id' => $item->prod_id));
			$this_weight = $product['weight'] * $item->qty;
			$new_total_weight = $new_total_weight + $this_weight;
		}

		// Update Data Order
		$data_this_order = $this->main_model->get_detail('orders',array('id' => $order_id));

		// Data ship_rates
		if ($data_this_order['order_status'] == 'Dropship') {
			$data_this_rates = $this->get_cost_ekspedisi($data_this_order['kecamatan_id'], 1, $data_this_order['ekspedisi'], $data_this_order['tarif_tipe']);
			$new_shipping_fee = ceil($new_total_weight) * $data_this_rates;
		} else {
			$new_shipping_fee = 0;
		}

		$subtotal = $new_total;
		$new_total = $new_total + $new_shipping_fee - $data_this_order['diskon'];
		$data_update_order = array(
			'shipping_fee'    => $new_shipping_fee,
			'shipping_weight' => $new_total_weight,
			'subtotal'        => $subtotal,
			'total'           => $new_total,
		);

		$where = array('id' => $order_id);
		$this->db->update('orders',$data_update_order,$where);
	}

	function order_detail($order_id = null,$direct=null)
	{
		$data_exist = $this->main_model->get_list_where('orders',array('id' => $order_id));
		$orders = $data_exist->row_array();
		$menu = $orders['order_status'] == 'Keep' ? 'order_rekap' : 'order_dropship';
		$this->check_hak_akses($menu);

		if($data_exist->num_rows() == 1)

		{
			//$this->recalculate_order($order_id);

			$data['output'] = null;
			$data['payment_method'] = $this->main_model->get_list('payment_method');
			$data['order'] = $this->main_model->get_detail('orders',array('id' => $order_id));
			if ($data['order']['kecamatan_id']) {
				//GET KOTA
				$url_kota  = $this->base_url_api . 'city?token='.$this->token . '&domain=' . $this->domain.'&city_id=' . $data['order']['kota_id'];
				$get_kota  = file_get_contents($url_kota, null, null);
				$data_kota = json_decode($get_kota);

				if ($data_kota->result->type == 'Kabupaten') {
					$type = 'Kab. ';
				} else {
					$type = 'Kota ';
				}
				$data['kota_tujuan'] = $type . $data_kota->result->city_name;

			    // // GET KECAMATAN
				$url_kec  = $this->base_url_api . "subdistrict?token=".$this->token."&domain=".$this->domain."&subdistrict_id=".$data['order']['kecamatan_id']; // Where you want to post data
				$get_kec  = file_get_contents($url_kec,null,null);
				$data_kec = json_decode($get_kec);
				$data['kec'] = $data_kec->result->subdistrict_name;
			}
			$data['order_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $data['order']['id'],'order_status !=' => 'Cancel'),null,array('by' => 'id','sorting' => 'ASC'));
			$data['customer'] = $this->main_model->get_detail('customer',array('id' => $data['order']['customer_id'] ));
			$data['provinsi'] = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $data['order']['prov_id']));
			$data['kota'] = $this->main_model->get_detail('jne_kota',array('kota_id' => $data['order']['kota_id']));
			$data['jne_tarif'] = $this->main_model->get_detail('jne_tarif',array('kota_tuju_id' => $data['order']['kota_id']));
			$data['tarif'] = $this->main_model->get_detail('tarif',array('coding' => $data['order']['tarif_id']));
			$data['direct'] = $direct;
			$this->load->view('administrator/page_order_detail',$data);

		}

		else

		{

			$this->session->set_flashdata('message','<div class="alert alert-warning">Data tidak ditemukan !</div>');

			redirect("administrator/main/order_all");

		}
	}


	function order_detail_change_status()
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');

		$order_payment = $this->input->post('order_payment',TRUE);

		$order_id = $this->input->post('order_id',TRUE);

		if ($order_payment && $order_id) {

			if ($order_payment == 'Unpaid' || $order_payment == 'Paid') {

				$data_update = array('order_payment' => $order_payment);
				$data_update_item = array('order_payment' => $order_payment);
				$where = array('id' => $order_id);
				$where_oritems = array('order_id' => $order_id);

				if ($order_payment == 'Paid') {

					$data_update['date_payment'] = date('Y-m-d H:i:s');
					$this->db->update('orders',$data_update,$where);
					$this->db->update('orders_item',$data_update_item,$where_oritems);

					$subject = 'Status Pesanan';
					$content = 'Status pesanan Anda #' . $order_id . ' telah dijadikan lunas';

					$this->db->select('id, added_point, customer_id, subtotal, diskon, point');
					$order = $this->main_model->get_detail('orders', array('id' => $order_id));
					if ($order['added_point'] == 0) {

						$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
						if ($point_reward_status['value'] == 'on') {
							$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
							$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
							$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
							$point_customer = $customer['point'] + $total_point - $order['point'];

							$point_history = array(
								'customer_id' => $order['customer_id'],
								'point_prev'  => $customer['point'],
								'point_in'    => $total_point - $order['point'],
								'point_end'   => $point_customer,
								'order_id'    => $order['id'],
								'note'        => 'Mendapatkan point',
								'user_id'     => $this->session->userdata('webadmin_user_id'),
							);
							$this->db->insert('point_histories', $point_history);

							$this->db->where('id', $order['customer_id'])
							->update('customer', array('point' => $point_customer));
							$this->db->update('orders', array('added_point' => 1, 'get_point' => 1), $where);
						}
					}

					$this->sendNotifikasi($order['customer_id'], $subject, $content, 'order_status', $order_id);
				}else{

					$this->db->update('orders',$data_update,$where);
					$this->db->update('orders_item',$data_update_item,$where_oritems);
				}

				$this->session->set_flashdata('message','<div class="alert alert-success">Data telah diperbarui !</div>');

			} else {

				$this->session->set_flashdata('message','<div class="alert alert-danger">Data gagal diperbarui !</div>');
			}

		} else {

			$this->session->set_flashdata('message','<div class="alert alert-danger">Data gagal diperbarui !</div>');
		}

		redirect('administrator/main/order_detail/'.$order_id);
	}

	private function sendNotifikasi($customer_id, $subject, $content, $type_notif, $order_id) {
		$customer = $this->db->select('notif')
		->get_where('customer', array('id' => $customer_id))->row_array();
		$notif = explode('|', $customer['notif']);
		if (in_array($type_notif, $notif)) {
			$fcm_customer = $this->db->get_where('t_fcm_customer', array('customer_id' => $customer_id));
			if ($fcm_customer->num_rows() > 0) {
				$this->registrationIds = array();
				foreach ($fcm_customer->result() as $cust) {
					$reg_id = $cust->registration_id;
					array_push($this->registrationIds, $reg_id);
				}
				$this->fcm_push_single($this->registrationIds,'order_status', $subject, $content, 0, $order_id);
			}
		}
	}

	function order_detail_change_status_list($order_payment = null, $order_id = null)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		if(($order_payment != null) and ($order_id != null))
		{

			if(($order_payment == 'Unpaid') or ($order_payment == 'Paid'))
			{

				$data_update = array('order_payment' => $order_payment);
				$data_update_item = array('order_payment' => $order_payment);

				if ($order_payment == 'Paid') {

					$data_update['date_payment'] = date('Y-m-d H:i:s');
				}

				$where_item = array('order_id' => $order_id);
				$where = array('id' => $order_id);

				$this->db->update('orders_item',$data_update_item,$where_item);
				$this->db->update('orders',$data_update,$where);

				if ($order_payment == 'Paid') {
					$this->db->select('id, added_point, customer_id, subtotal, diskon, point');
					$order = $this->main_model->get_detail('orders', array('id' => $order_id));
					if ($order['added_point'] == 0) {

						$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
						if ($point_reward_status['value'] == 'on') {
							$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
							$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
							$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
							$point_customer = $customer['point'] + $total_point - $order['point'];

							$point_history = array(
								'customer_id' => $order['customer_id'],
								'point_prev'  => $customer['point'],
								'point_in'    => $total_point - $order['point'],
								'point_end'   => $point_customer,
								'order_id'    => $order['id'],
								'note'        => 'Mendapatkan point',
								'user_id'     => $this->session->userdata('webadmin_user_id'),
							);
							$this->db->insert('point_histories', $point_history);

							$this->db->where('id', $order['customer_id'])
							->update('customer', array('point' => $point_customer));
							$this->db->update('orders', array('added_point' => 1, 'get_point' => 1), $where);
						}
					}
					$subject = 'Status Pesanan';
					$content = 'Status pesanan Anda #' . $order['id'] . ' telah dijadikan lunas';
					$this->sendNotifikasi($order['customer_id'], $subject, $content, 'order_status', $order['id']);
				}
				$this->session->set_flashdata('message','<div class="alert alert-success">Status Data Pesanan <strong>#'.$order_id.' </strong> telah diperbarui !</div>');

			} else {

				$this->session->set_flashdata('message','<div class="alert alert-danger">Data gagal diperbarui !</div>');
			}
		} else {

			$this->session->set_flashdata('message','<div class="alert alert-danger">Data gagal diperbarui !</div>');
		}
		redirect('administrator/main/order_unpaid');

	}



	function order_detail_update_qty() {
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$order_id = $this->input->post('order_id');
		$order_item_id = $this->input->post('order_item_id');
		$order_item_qty = $this->input->post('order_item_qty');
		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));

		// Checking available stock
		$error_number = 0;

		$error_message = '';

		for($i = 0; $i < count($order_item_id); $i++) {

			$item_id = $order_item_id[$i];
			$item_qty = $order_item_qty[$i];
			$data_this_order_item = $this->main_model->get_detail('orders_item',array('id' => $item_id));

			if($item_qty > $data_this_order_item['qty']) {
				$selisih_qty = $item_qty - $data_this_order_item['qty'];

				// Check stock
				$this_variant = $this->main_model->get_detail('product_variant',array('id' => $data_this_order_item['variant_id']));
				$this_product = $this->main_model->get_detail('product',array('id' => $data_this_order_item['prod_id']));

				if ($data_value_stock['value'] != 3) {
					if($this_variant['stock'] < $selisih_qty) {
						$error_number++;
						$error_message .= '<div class="alert alert-danger">Produk <b>'.$this_product['name_item'].'</b> Variant <b>'.$this_variant['variant'].'</b> Tidak memiliki Stock yang cukup untuk dipesan </div>';
					}
				}
			}
		}


		if($error_number == 0) {
			$new_total = 0;
			$new_total_weight = 0;

			for($i = 0; $i < count($order_item_id); $i++){
				$item_id = $order_item_id[$i];
				$item_qty = $order_item_qty[$i];
				$data_this_order_item = $this->main_model->get_detail('orders_item',array('id' => $item_id));
				$data_this_product = $this->main_model->get_detail('product',array('id' => $data_this_order_item['prod_id']));
				$data_this_variant = $this->main_model->get_detail('product_variant',array('id' => $data_this_order_item['variant_id']));

				if ($data_value_stock['value'] != 3) {
					// Update stock
					$selisih_qty = $item_qty - $data_this_order_item['qty'];
					$new_stock = $data_this_variant['stock'] - $selisih_qty;
					$data_update_stock = array('stock' => $new_stock);
					$where_stock = array('id' => $data_this_order_item['variant_id']);
					$this->db->update('product_variant',$data_update_stock,$where_stock);

					$update_stock_histories['qty'] = '-' . $item_qty;
					$this->db->update('stock_histories', $update_stock_histories, array('order_item_id' => $order_item_id[$i]));
				}

				$this_subtotal = $data_this_order_item['price'] * $item_qty;
				$data_update = array(
					'qty'            => $item_qty,
					'subtotal'       => $this_subtotal,
					'modal'          => $data_this_product['price_production'],
					'subtotal_modal' => $data_this_product['price_production'] * $item_qty,
				);

				$where = array('id' => $item_id);
				$this->db->update('orders_item',$data_update,$where);

				$new_total = $new_total + $this_subtotal;
				$this_weight = $data_this_product['weight'] * $item_qty;
				$new_total_weight = $new_total_weight + $this_weight;
			}


			// Update Data Order
			$data_this_order = $this->main_model->get_detail('orders',array('id' => $order_id));

			// Data ship_rates
			if($data_this_order['order_status'] == 'Dropship') {
				$data_this_rates = $this->main_model->get_detail('tarif',array('coding' => $data_this_order['tarif_id']));
				$data_this_rates = $this->get_cost_ekspedisi($data_this_order['kecamatan_id'], 1, $data_this_order['ekspedisi'], $data_this_order['tarif_tipe']);
				$new_shipping_fee = ceil($new_total_weight) * $data_this_rates;
			} else {
				$new_shipping_fee = 0;
			}

			$subtotal = $new_total;
			$new_total = $new_total + $new_shipping_fee;
			$data_update_order = array(
				'shipping_fee'    => $new_shipping_fee,
				'shipping_weight' => $new_total_weight,
				'subtotal'        => $subtotal,
				'total'           => $new_total,
			);

			$where = array('id' => $order_id);
			$this->db->update('orders',$data_update_order,$where);

			$this->session->set_flashdata('message','<div class="alert alert-success">Data Item pesanan telah diperbarui !</div>');
		} else {
			$this->session->set_flashdata('message',$error_message);
		}
		redirect('administrator/main/order_detail/'.$order_id);
	}



	function order_detail_add_product()

	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$order_id = $this->input->post('add_form_order_id');

		$prod_id = $this->input->post('add_form_product');

		$customer_id = $this->input->post('customer_id');

		$variant_id = $this->input->post('add_form_variant');

		$qty = $this->input->post('add_form_qty');

		$total_berat = $this->input->post('total_berat');
		$add_ekspedisi = $this->input->post('add_ekspedisi');
		$add_tarif_tipe = $this->input->post('add_tarif_tipe');

		// Get data

		$data_product = $this->main_model->get_detail('product',array('id' => $prod_id));

		$data_variant = $this->main_model->get_detail('product_variant',array('id' => $variant_id));

		$data_order = $this->main_model->get_detail('orders',array('id' => $order_id));
		$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));

		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));

		$list_price = $this->db->order_by('cust_type_id', 'desc')->get_where('product_price', array('prod_id' => $prod_id))->result();
		if (count($list_price) > 0) {
			$product_price = $this->db->get_where('product_price', array('prod_id' => $prod_id, 'cust_type_id' => $data_customer['jenis_customer']))->row_array();
			if (count($product_price) > 0) {
				$new_harga = $product_price['price'];
			} else {
				$new_harga = $list_price[0]->price;
			}
		} else {
			if ($data_customer['jenis_customer'] == '1') {
				$new_harga = $data_product['price'];
			} else {
				if ($data_product['price_old_luar'] == 0) {
					$new_harga = $data_product['price_luar'];
				} else {
					$new_harga = $data_product['price'];;
				}
			}
		}

		if($qty <= $data_variant['stock']) {

			// Pre defined data

			$subtotal = $new_harga * $qty;

			$total_weight_this_product = $data_product['weight'] * $qty;

			$total_weight_this_order = $data_order['shipping_weight'] + $total_weight_this_product;

			if($data_order['order_status'] == 'Dropship')

			{

				$cost = $this->get_cost_ekspedisi($data_order['kecamatan_id'],$total_weight_this_order,$add_ekspedisi,$add_tarif_tipe);

				$total_shipping_fee = $cost;

			} else {

				$total_shipping_fee = 0;
			}

			$total_shop = ($data_order['total'] - $data_order['shipping_fee']) + $subtotal;

			$sub_order_total = $total_shop;

			$total = $total_shop + $total_shipping_fee;

			// add order_item
			$data_insert = array(

				'customer_id' => $data_order['customer_id'],

				'order_id' => $order_id,

				'prod_id' => $prod_id,

				'order_datetime' => date('Y-m-d H:i:s'),

				'variant_id' => $variant_id,

				'qty' => $qty,

				'price' => $new_harga,

				'subtotal' => $subtotal,

				'modal' => $data_product['price_production'],

				'subtotal_modal' => $data_product['price_production'] * $qty,

				'tipe' => $data_product['product_type'],

				'order_status' => $data_order['order_status'],

				'order_payment' => $data_order['order_payment']

			);

			$this->db->insert('orders_item',$data_insert);
			$order_item_id = $this->db->insert_id();

			//update stock
			if ($data_value_stock['value'] != 3) {

				$new_stock = $data_variant['stock'] - $qty;

				$data_update_stock = array('stock' => $new_stock);

				$where_update_stock = array('id' => $variant_id);

				$this->db->update('product_variant',$data_update_stock,$where_update_stock);

				$stock_histories = array(
					'prod_id'       => $prod_id,
					'variant_id'    => $variant_id,
					'prev_stock'    => $data_variant['stock'],
					'stock'         => $new_stock,
					'qty'           => '-' . $qty,
					'price'         => $new_harga,
					'order_item_id' => $order_item_id,
					'customer_id'   => $data_order['customer_id'],
					'created_at'    => date('Y-m-d H:i:s'),
					'ref'           => 'Admin',
					'user_id'       => $this->session->userdata('webadmin_user_id')
				);
				$this->db->insert('stock_histories', $stock_histories);
			}

			// Update data Order
			$data_update_order = array(
				'shipping_weight' => $total_weight_this_order,
				'shipping_fee'    => $total_shipping_fee,
				'total'           => $total,
				'subtotal'        => $sub_order_total
			);

			if($data_order['order_status'] == 'Dropship') {
				$data_update_order['ekspedisi'] = $add_ekspedisi;
				$data_update_order['tarif_tipe'] = $add_tarif_tipe;
			}

			$where_order = array('id' => $order_id);

			$this->db->update('orders',$data_update_order,$where_order);
			$this->session->set_flashdata('message','<div class="alert alert-success">Item Pesanan telah ditambahkan</div>');

		} else {

			$this->session->set_flashdata('message','<div class="alert alert-danger">Produk <b>'.$data_product['name_item'].'</b> Variant <b>'.$data_variant['variant'].'</b> Tidak memiliki Stock yang cukup untuk dipesan </div>');
		}

		redirect('administrator/main/order_detail/'.$order_id);

	}



	function order_detail_get_publish_product()

	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data_publish_product = $this->main_model->get_list_where('product',array('status' => 'Publish'));
		foreach($data_publish_product->result() as $products):
			$data_json[] = array(

				'prod_id' => $products->id,

				'name_item' => $products->name_item

			);
		endforeach;
		echo json_encode($data_json);

	}



	function order_detail_get_variant()

	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$prod_id = $this->input->post('prod_id');

		$data_variant = $this->main_model->get_list_where('product_variant',array('prod_id' => $prod_id));

		foreach($data_variant->result() as $variants):

			$data_json[] = array(

				'variant_id' => $variants->id,

				'variant' => $variants->variant

			);

		endforeach;

		echo json_encode($data_json);

	}



	function order_detail_get_variant_detail()

	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$variant_id = $this->input->post('variant_id');

		$data_variant = $this->main_model->get_detail('product_variant',array('id' => $variant_id));

		$data_product = $this->main_model->get_detail('product', array('id' => $data_variant['prod_id']));

		$data_json = $data_variant;

		$data_json['weight'] = $data_product['weight'];

		echo json_encode($data_json);

	}



	function nota_detail_print($order_id = null)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data['orders'] = $this->main_model->get_detail('orders',array('id' => $order_id));
		$data_shipping = $this->main_model->get_detail('content',array('name' => 'shipping_show'));
		if ($data['orders']['order_status'] == 'Dropship' && $data_shipping['value'] == 1) {
			if ($data['orders']['ref'] == 'Android_New') {
				// GET PROVINSI
				$url_prov  = $this->base_url_api."province?token=".$this->token."&domain=".$this->domain."&province_id=".$data['orders']['prov_id'];
				$get_prov  = file_get_contents($url_prov,null,null);
				$data_prov = json_decode($get_prov);
				$data['prov'] = $data_prov->result->province;

				//GET KOTA
				$url_kota  = $this->base_url_api."city?token=".$this->token."&domain=".$this->domain."&province_id=".$data['orders']['prov_id']."&city_id=".$data['orders']['kota_id']; // Where you want to post data
				$get_kota  = file_get_contents($url_kota,null,null);
				$data_kota = json_decode($get_kota);

				foreach ($data_kota->result as $kota) {
					if ($kota->city_id == $data['orders']['kota_id']) {
						if ($kota->type == 'Kabupaten') {
							$type = 'Kab. ';
						} else {
							$type = 'Kota ';
						}
						$data['kota_tujuan'] = $type.$kota->city_name;
					}
				}

			    // // GET KECAMATAN
				$url_kec  = $this->base_url_api."subdistrict?token=".$this->token."&domain=".$this->domain."&province_id=".$data['orders']['prov_id']."&city_id=".$data['orders']['kota_id']."&subdistrict_id=".$data['orders']['kecamatan_id']; // Where you want to post data
				$get_kec  = file_get_contents($url_kec,null,null);
				$data_kec = json_decode($get_kec);
				foreach ($data_kec->result as $kec) {
					if ($kec->subdistrict_id == $data['orders']['kecamatan_id']) {
						$data['kec'] = $kec->subdistrict_name;
					}
				}
			} else {
				$data['prov'] = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $data['orders']['prov_id']));
				$data['kota'] = $this->main_model->get_detail('jne_kota',array('kota_id' => $data['orders']['kota_id']));
				$data['kec'] = $this->main_model->get_detail('tarif',array('coding' => $data['orders']['tarif_id']));
			}
		}
		$data['customer'] = $this->main_model->get_detail('customer',array('id' => $data['orders']['customer_id'] ));

		$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));

		$data['total'] = $data['orders']['total'];

		$data['url_redirect'] = base_url()."administrator/main/order_detail/".$order_id;

		$this->load->view('administrator/print/print_nota',$data);



	}


	function nota_detail_save($order_id = null)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$order = $this->main_model->get_detail('orders',array('id' => $order_id));

		$customer = $this->main_model->get_detail('customer',array('id' => $order['customer_id'] ));

		$order_item= $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));

		$data_info_kontak = $this->main_model->get_detail('content',array('name' => 'nota'));

		$data_logo = $this->main_model->get_detail('content',array('name' => 'image_nota'));

		$pay_id = $order['payment_method_id'];

		$payment_methode = $this->main_model->get_detail('payment_method',array('id' =>$pay_id));

		$total = $order['total'];

		//$data['url_redirect'] = base_url()."administrator/main/order_detail/".$order_id;
		$this->load->library('tcpdf');

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->setHeaderData('', '', 'Nota Pesanan #'.$order['id'], $data_info_kontak['value']);


		// set default header data

		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts

		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


		// set default monospaced font

		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);



		// set margins

		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);

		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);



		// set auto page breaks

		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);



		// set image scale factor

		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



		// Add a page

		$pdf->AddPage();

		if ($pay_id != 0) {

			if ($order['customer_id']== 0) {

				$customer_name ="<span>Nama Pelanggan: ".$order['name_customer']."<strong>(GUEST)</strong></span><br/><span color=\"red\">Metode Pembayaran: ".$payment_methode['name']."</span><br/>";

			} else{

				$customer_name = "<span>Nama Pelanggan: ".$customer['name']."(".$customer['id'].")</span><br/><span color=\"red\">Metode Pembayaran: ".$payment_methode['name']."</span><br/>";

			}

		}else{



			if ($order['customer_id']== 0) {

				$customer_name ="<span>Nama Pelanggan: ".$order['name_customer']."<strong>(GUEST)</strong></span><br/><span color=\"red\">Metode Pembayaran: - </span><br/>";

			} else{

				$customer_name = "<span>Nama Pelanggan: ".$customer['name']."(".$customer['id'].")</span><br/>";

			}

		}


		 //Looping product
		$total_qty = 0;
		$nota_variant = array();
		$nota_variant_id = array();

		foreach($order_item->result() as $orders_item) :

			if(in_array($orders_item->variant_id,$nota_variant_id))

			{

				$nota_variant[$orders_item->variant_id]['qty'] = $nota_variant[$orders_item->variant_id]['qty'] + $orders_item->qty;

				$nota_variant[$orders_item->variant_id]['subtotal'] = $nota_variant[$orders_item->variant_id]['subtotal'] + $orders_item->subtotal;
			}

			else

			{

				$nota_variant_id[] = $orders_item->variant_id;

				$nota_variant[$orders_item->variant_id] = array(

					'variant_id' => $orders_item->variant_id,

					'prod_id' => $orders_item->prod_id,

					'qty' => $orders_item->qty,

					'subtotal' => $orders_item->subtotal

				);
			}
		endforeach;


		$html =

		"<span>Tanggal: ".date('D, d-m-Y H:i:s')."</span><br/>".$customer_name.

		"<hr/><table><thead>

		<tr>

		<td width=\"60%\" style=\"padding:5px\"><strong>ITEMS</strong><hr/></td>

		<td width=\"10%\"><strong>QTY</strong><hr/></td>

		<td width=\"30%\" style=\"text-align: right;\"><strong>HARGA</strong><hr/></td>

		</tr>

		</thead>";

		$html .= "<tbody>";

		foreach($nota_variant as $key=>$value) :

			$data_product = $this->main_model->get_detail('product',array('id' => $value['prod_id']));

			$data_variant = $this->main_model->get_detail('product_variant',array('id' => $value['variant_id']));

			$total_qty = $total_qty + $value['qty'];

			$html .= "<tr><td width=\"60%\"><strong>".$data_product['name_item']."</strong><br/>".$data_variant['variant']."</td><td width=\"10%\">".$value['qty']."</td><td width=\"30%\" style=\"text-align: right;\">Rp ".numberformat($value['subtotal'])."</td></tr>";

		endforeach;

		$html .= "</tbody></table>";

		$pdf->writeHTML($html, true, false, true, false, '');

		$html = "<table><tbody><tr><td width=\"60%\">TOTAL BARANG (QTY) :</td><td width=\"10%\">".$total_qty."</td></tr></tbody><table>";

		$pdf->writeHTML($html, true, false, true, false, '');

		$html = "<hr/>";

		$pdf->writeHTML($html, true, false, true, false, '');

		if($order ['order_status'] == 'Dropship') {

			$html = "<table><tbody><tr><td width=\"70%\">SUBTOTAL :</td><td width=\"30%\" style=\"text-align: right;\">Rp ".number_format($order['subtotal'])."</td></tr></tbody><table>";

			$pdf->writeHTML($html, true, false, true, false, '');

			$html = "<table><tbody><tr><td width=\"70%\">ONGKOS PENGIRIMAN :</td><td width=\"30%\" style=\"text-align: right;\">Rp ".number_format($order['shipping_fee'])."</td></tr></tbody><table>";

			$pdf->writeHTML($html, true, false, true, false, '');

		}

		$html = "<table><tbody><tr><td width=\"70%\"><strong>TOTAL PEMBAYARAN:</strong></td><td width=\"30%\" style=\"text-align: right;\">Rp ".number_format($order['total'])."</td></tr></tbody><table>";

		$pdf->writeHTML($html, true, false, true, false, '');

		if($order ['order_status'] == 'Dropship') {

			$html = "- - - - - - - - - - - - - - - - - - - - - - - - -  - - -- - - - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ";

			$html .="<h4>DATA SHIPPING</h4>";

			$html .= "<table><thead>";

			$html .= "<tr><th width=\"15%\">Dari</th><th width=\"15%\">Kepada</th><th width=\"30%\">No Telpon Penerima</th><th width=\"25%\">Alamat Penerima</th><th width=\"15%\">Kode Post</th></tr>";

			$html .= "</thead></table><hr/>";

			$pdf->writeHTML($html, true, false, true, false, '');

			$html = "<table><tbody>";

			$html .= "<tr><td width=\"15%\">".$order['shipping_from']."</td><td width=\"15%\">".$order['shipping_to']."</td><td width=\"30%\">".$order['phone_recipient']."</td><td width=\"25%\">".$order['address_recipient']."</td><td width=\"15%\">".$order['postal_code']."</td></tr>";

			$html .= "</tbody></table>";

			$pdf->writeHTML($html, true, false, true, false, '');

		}

		if ($order['customer_id']== 0) {

			$pdf->Output('nota-'.$order['id'].''.$order['name_customer'].'.pdf', 'I');

		}else{

			$pdf->Output('nota-'.$order['id'].''.$customer['name'].'.pdf', 'I');

		}
	}


	function ekspedisi_print($order_id = null)
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$data['data_orders'] = $this->main_model->get_detail('orders',array('id' => $order_id));

		$data['customer'] = $this->main_model->get_detail('customer',array('id' => $data['data_orders']['customer_id'] ));

		$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));

		$data['total'] = $data['data_orders']['total'];

		$data['url_redirect'] = base_url()."administrator/main";

		$this->load->view('administrator/print/print_ekspedisi',$data);
	}


	function ekspedisi_print_a4($order_id = null) {
		$data['shipping'] = $this->main_model->get_detail('content',array('name' => 'shipping_show'));
		$data['data_orders'] = $this->main_model->get_detail('orders', array('id' => $order_id));
		$data['customer'] = $this->main_model->get_detail('customer', array('id' => $data['data_orders']['customer_id'] ));
		$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));
		if ($data['data_orders']['order_status'] == 'Dropship' && $data['shipping']['value'] == 1) {
			$data['provinsi'] = $this->callback_province($data['data_orders']['prov_id']);
			$data['kota'] = $this->callback_city($data['data_orders']['kota_id']);
			$data['kecamatan'] = $this->callback_subdistrict($data['data_orders']['kecamatan_id']);
		}
		$data['total'] = $data['data_orders']['total'];
		$data['url_redirect'] = base_url() . 'administrator/main/order_detail/'.$order_id;
		$show_id_print_nota = $this->main_model->get_detail('content', array('name' => 'show_id_print_nota'));
		$data['show_id_print_nota'] = $show_id_print_nota['value'];
		$show_estimasi_print_nota = $this->main_model->get_detail('content', array('name' => 'show_estimasi_print_nota'));
		$data['show_estimasi_print_nota'] = $show_estimasi_print_nota['value'];
		$format_ekspedisi = $this->main_model->get_detail('content',array('name' => 'format_ekspedisi'));
		if ($format_ekspedisi['value'] == 1) {
			$this->load->view('administrator/print/print_ekspedisi_a4', $data);
		} else {
			$this->load->view('administrator/print/print_ekspedisi_a4_v2', $data);
		}
	}


	function create_order() {
		$this->check_hak_akses('create_order');
		$data['output'] = null;
		$data['customer'] = $this->main_model->get_list_where('customer',array('status' => 'Active'));
		$data['product'] = $this->main_model->get_list_where('product',array('status' => 'Publish'));
		$order_non_pelanggan = $this->main_model->get_detail('content', array('name' => 'order_non_pelanggan_status'));
		$data['order_non_pelanggan'] = $order_non_pelanggan['value'];
		$this->load->view('administrator/page_new_order',$data);
	}


	function create_order_tamu()
	{
		$order_non_pelanggan = $this->main_model->get_detail('content', array('name' => 'order_non_pelanggan_status'));
		if ($order_non_pelanggan['value'] == 'off') {
			redirect('administrator/main/dashboard', 'refresh');
		}
		$this->check_hak_akses('create_order');
		$data['output'] = null;
		$data['product'] = $this->main_model->get_list_where('product',array('status' => 'Publish'));
		$this->load->view('administrator/page_new_order_tamu',$data);
	}

	function get_variant_create_order()
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$prod_id = $this->input->post('prod_id');
		$customer_id = $this->input->post('customer_id');

		$data_product = $this->main_model->get_detail('product',array('id' => $prod_id));
		$data_variant = $this->main_model->get_list_where('product_variant',array('prod_id' => $prod_id, 'available !=' => 'Delete' ));
		$data_json_variant = [];
		foreach($data_variant->result() as $variant):
			$data_json_variant[] = array('variant_id' => $variant->id, 'variant_name' => $variant->variant, 'stock' => $variant->stock);
		endforeach;

		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		if ($customer_id == '') {
			$customer['jenis_customer'] = 0;
			$order = 'desc';
		} else {
			$order = 'asc';
		}
		$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $data_product['id']))->result();
		if (count($list_price) > 0) {
			$product_price = $this->db->get_where('product_price', array('prod_id' => $data_product['id'], 'cust_type_id' => $customer['jenis_customer']))->row_array();
			if (count($product_price) > 0) {
				$price = $product_price['price'];
			} else {
				$price = $list_price[0]->price;
			}
		} else {

			if ($customer['jenis_customer'] == '1') {
				$price = $data_product['price'];
			} else {
				if ($data_product['price_old_luar'] == 0) {
					$price = $data_product['price_luar'];
				} else {
					$price = $data_product['price'];
				}
			}
		}

		$data_json = array(

			'prod_id' => $data_product['id'],

			'price' => $price,

			'weight' => $data_product['weight'],

			'min_order' => $data_product['min_order'],

			'variant' => $data_json_variant

		);

		echo json_encode($data_json);
	}


	function create_order_process() {
		$this->check_hak_akses('create_order');
		$this->form_validation->set_rules('customer_id', 'Customer', 'required');
		$customer_id = $this->input->post('customer_id');

		$product = $this->input->post('product');
		$qty = $this->input->post('qty');
		if ($customer_id == 0) {
			for ($i = 0; $i < count($product); $i++) {
				if ($qty[$i] > 0) {
					$this->form_validation->set_rules('product[' . $i . ']', 'Produk', 'required');
				}
			}
			$this->form_validation->set_rules('name_customer','Nama Customer','required');
			$name_customer = $this->input->post('name_customer');
		} else {
			$this->form_validation->set_rules('product[]', 'Produk', 'required');
		}

		$variant = $this->input->post('variant_id');
		$price = $this->input->post('price');
		$notes = $this->input->post('notes');
		$subtotal_weight = $this->input->post('subtotal_weight');
		$status_pesanan = $this->input->post('status_pesanan');
		$discount = $this->input->post('discount');
		$diskon_val = str_replace('.', '', $discount);
		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));

		$redirect = $customer_id != 0 ? 'administrator/main/create_order' : 'administrator/main/create_order_tamu';

		if ($this->form_validation->run()) {
			$data_due_date = $this->main_model->get_detail('content',array('name' => 'due_date_setting'));
			$status_due_date = $this->main_model->get_detail('content',array('name' => 'status_due_date'));

			if ($status_due_date['value'] == 'ON') {

				$days = $data_due_date['value'];
				$today = date('Y-m-d H:i:s');
				$date1 = str_replace('-', '/', $today);
				$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . '+'.$data_due_date['value']));
			} else {
				$due_datetime = '0000-00-00 00:00:00';
			}

			// Check error stock
			$error_number = 0;
			$error_message = '';

			for ($i = 0; $i < count($product); $i++) {
				$data_product = $this->main_model->get_detail('product', array('id' => $product[$i]));
				$data_variant = $this->main_model->get_detail('product_variant', array('id' => $variant[$i]));

				if ($qty[$i] > 0) {
					if ($data_value_stock['value'] != 3 ) {
						if ($qty[$i] > $data_variant['stock']) {
							$error_number++;
							$error_message .= '<div class="alert alert-danger">Produk <b>' . $data_product['name_item'].'</b> Variant <b>' . $data_variant['variant'] . '</b> Tidak memiliki Stock yang cukup untuk dipesan </div>';
						}
					}

					if ($qty[$i] < $data_product['min_order']) {
						$error_number++;
						$error_message .= '<div class="alert alert-danger">Maaf pesanan anda kurang dari minimum order</div>';
					}
				}
			}

			if ($error_number == 0) {
				if ($status_pesanan != 'Keep') {
					$data_order = array(
						'customer_id'     => $customer_id,
						'order_datetime'  => date('Y-m-d H:i:s'),
						'due_datetime'    => $due_datetime,
						'shipping_fee'    => 0,
						'shipping_weight' => 0,
						'shipping_from'   => '',
						'shipping_to'     => '',
						'prov_id'         => 0,
						'kota_id'         => 0,
						'total'           => 0,
					);
					if ($customer_id == 0) {
						$data_order['name_customer'] = $name_customer;
					}

					if ($status_pesanan == 'Keep_Paid') {
						$data_order['order_status'] = 'Keep';
						$data_order['order_payment'] = 'Paid';
						$data_order['date_payment'] = date('Y-m-d H:i:s');
					} else if ($status_pesanan == 'Dropship_Unpaid') {
						$data_order['order_status'] = 'Dropship';
						$data_order['order_payment'] = 'Unpaid';
					} else if ($status_pesanan == 'Dropship_Paid') {
						$data_order['order_status'] = 'Dropship';
						$data_order['order_payment'] = 'Paid';
						$data_order['shipping_status'] = 'Belum Dikirim';
						$data_order['date_payment'] = date('Y-m-d H:i:s');
					} else if ($status_pesanan == 'Rekap_Unpaid') {
						$data_order['order_status'] = 'Keep';
						$data_order['order_payment'] = 'Unpaid';
					} else if ($status_pesanan == 'Piutang_Unpaid') {
						$data_order['order_status'] = 'Piutang';
						$data_order['order_payment'] = 'Unpaid';
					} else if ($status_pesanan == 'Cash_Paid') {
						$payment_method = $this->db->where('name', 'Cash')
						->get('payment_method')->row_array();
						$data_order['order_status'] = 'Keep';
						$data_order['order_payment'] = 'Paid';
						$data_order['date_payment'] = date('Y-m-d H:i:s');
						$data_order['payment_method_id'] = $payment_method['id'];
					}

					$this->db->trans_start();

					$this->db->insert('orders', $data_order);
					$order_id = $this->db->insert_id();

					if ($status_pesanan == 'Dropship_Unpaid' || $status_pesanan == 'Dropship_Paid') {
						$redirect = 'administrator/main/create_order_dropship/' . $order_id . '/unpaid';
					}

				} else {
					$order_id = 0;
					$data_order['order_status'] = 'Keep';
					$data_order['order_payment'] = 'Unpaid';
				}

				$grand_total = 0;
				$total_weight = 0;

				for ($i = 0; $i < count($product); $i++) {
					if ($qty[$i] > 0) {
						$data_product = $this->main_model->get_detail('product', array('id' => $product[$i]));
						$data_variant = $this->main_model->get_detail('product_variant', array('id' => $variant[$i]));
						$subtotal = $price[$i] * $qty[$i];
						$now = date('Y-m-d H:i:s');
						$data_order_item = array(
							'customer_id'    => $this->input->post('customer_id'),
							'order_id'       => $order_id,
							'prod_id'        => $product[$i],
							'order_datetime' => $now,
							'due_datetime'   => $due_datetime,
							'variant_id'     => $variant[$i],
							'qty'            => $qty[$i],
							'price'          => $price[$i],
							'subtotal'       => $subtotal,
							'modal'          => $data_product['price_production'],
							'subtotal_modal' => $data_product['price_production'] * $qty[$i],
							'notes'          => $notes[$i],
							'order_status'   => $data_order['order_status'],
							'order_payment'  => $data_order['order_payment'],
						);
						$this->db->insert('orders_item', $data_order_item);

						if ($data_value_stock['value'] != 3) {
							$new_stock = $data_variant['stock'] - $qty[$i];
							$data_update_stock = array('stock' => $new_stock);
							$where_variant = array('id' => $variant[$i]);
							$this->db->update('product_variant', $data_update_stock, $where_variant);

							$stock_histories = array(
								'prod_id'     => $product[$i],
								'variant_id'  => $variant[$i],
								'prev_stock'  => $data_variant['stock'],
								'stock'       => $new_stock,
								'qty'         => '-' . $qty[$i],
								'user_id'     => $this->session->userdata('webadmin_user_id'),
								'customer_id' => $customer_id,
								'created_at'  => $now
							);
							if ($customer_id == 0) {
								$stock_histories['customer_id'] = 0;
								$stock_histories['note'] = 'Pesanan Non Pelanggan (' . $name_customer . ')';
							}
							$this->db->insert('stock_histories', $stock_histories);
						}

						$grand_total += $subtotal;
						$total_weight += $subtotal_weight[$i];
					}
				}

				if ($order_id != 0) {
					$total = $grand_total - $diskon_val;
					$data_update_order = array(
						'total'           => $total,
						'subtotal'        => $grand_total,
						'shipping_weight' => $total_weight,
						'diskon'          => $diskon_val
					);
					$where = array('id' => $order_id);
					$this->db->update('orders', $data_update_order, $where);
				}

				$this->db->trans_complete();
				$status = array(
					'Keep'            => 'Transaksi di tempat',
					'Keep_Paid'       => 'Pesanan bayar di tempat',
					'Dropship_Unpaid' => 'Dropship',
					'Dropship_Paid'   => 'Dropship',
					'Rekap_Unpaid'    => 'Rekap',
					'Piutang_Unpaid'  => 'Piutang',
					'Cash_Paid'       => 'Transaksi Cash'
				);
				$this->session->set_flashdata('message','<div class="alert alert-success">Pesanan telah ditambahkan dengan Status Pesanan <b>' . $status[$status_pesanan] . '</b> dan status Pembayaran <b>' . get_order_payment($data_order['order_payment']) . '</b></div>');
			} else {
				$this->session->set_flashdata('message', $error_message);
				redirect($redirect);
			}
			redirect($redirect);
		} else {
			redirect($redirect);
		}

	}


	function create_order_dropship($order_id = null, $status = null) {
		$this->check_hak_akses('create_order');
		$data['output'] = null;

		if ($order_id != null && $status != null) {
			$data['jne_status'] = $this->main_model->get_detail('content',array('name' => 'jne_status'));
			$data['tiki_status'] = $this->main_model->get_detail('content',array('name' => 'tiki_status'));
			$data['pos_status'] = $this->main_model->get_detail('content',array('name' => 'pos_status'));
			$data['wahana_status'] = $this->main_model->get_detail('content',array('name' => 'wahana_status'));
			$data['jnt_status'] = $this->main_model->get_detail('content',array('name' => 'jnt_status'));
			$data['sicepat_status'] = $this->main_model->get_detail('content',array('name' => 'sicepat_status'));
			$data['lion_status'] = $this->main_model->get_detail('content',array('name' => 'lion_status'));
			$data['origin_city_id'] = $this->main_model->get_detail('content',array('name' => 'origin_city_id'));
			$data['origin_city_name'] = $this->main_model->get_detail('content',array('name' => 'origin_city_name'));
			$data['provinsi'] = $this->main_model->get_list('jne_provinsi');
			$data['orders'] = $this->main_model->get_detail('orders',array('id' => $order_id));
			$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));
			$data['customer'] = $this->main_model->get_detail('customer',array('id' => $data['orders']['customer_id']));
			$data['total_belanja'] = 0;
			foreach ($data['orders_item']->result() as $items) {
				$data['total_belanja'] = $data['total_belanja'] + $items->subtotal;
			}
			$this->load->view('administrator/page_create_order_dropship',$data);
		}

	}

	function create_order_dropship_process() {
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
		$this->form_validation->set_rules('order_id','Order ID','required');
		$this->form_validation->set_rules('shipping_from','Pengirim','required');
		$this->form_validation->set_rules('shipping_to','Kepada','required');
		$this->form_validation->set_rules('prov_id','Provinsi','required');
		$this->form_validation->set_rules('kecamatan_id','Kecamatan','required');
		$this->form_validation->set_rules('tarif_tipe','Tarif Tipe','required');
		$this->form_validation->set_rules('kota_id','Kota','required');
		$this->form_validation->set_rules('shipping_fee','Ongkos Kirim','required');
		$this->form_validation->set_rules('address_recipient','Alamat Penerima','required');
		$this->form_validation->set_rules('postal_code','Kode Pos','required');
		$this->form_validation->set_rules('phone_recipient','Telpon Penerima','required');
		$this->form_validation->set_rules('total_after','Total','required');
		$direct = $this->input->post('link_direct');

		if ($this->form_validation->run() == TRUE) {

			$data_update = array(
				'shipping_from'     => $this->input->post('shipping_from'),
				'shipping_to'       => $this->input->post('shipping_to'),
				'shipping_fee'      => $this->input->post('shipping_fee'),
				'shipping_weight'   => $this->input->post('shipping_weight'),
				'prov_id'           => $this->input->post('prov_id'),
				'kota_id'           => $this->input->post('kota_id'),
				'kecamatan_id'      => $this->input->post('kecamatan_id'),
				'ekspedisi'         => $this->input->post('ekspedisi'),
				'tarif_tipe'        => $this->input->post('tarif_tipe'),
				'address_recipient' => $this->input->post('address_recipient'),
				'postal_code'       => $this->input->post('postal_code'),
				'phone_recipient'   => $this->input->post('phone_recipient'),
				'subtotal'          => $this->input->post('total_before'),
				'total'             => $this->input->post('total_after')
			);
			$where = array('id' => $this->input->post('order_id'));
			$this->db->update('orders', $data_update, $where);
			$this->session->set_flashdata('message','<div class="alert alert-success">Pesanan telah disimpan</div>');
			redirect('administrator/main/order_detail/'.$this->input->post('order_id').'/'.$direct);
		} else {
			$order_id = $this->input->post('order_id');
			$data['output'] = null;
			$data['jne_status'] = $this->main_model->get_detail('content',array('name' => 'jne_status'));
			$data['tiki_status'] = $this->main_model->get_detail('content',array('name' => 'tiki_status'));
			$data['pos_status'] = $this->main_model->get_detail('content',array('name' => 'pos_status'));
			$data['wahana_status'] = $this->main_model->get_detail('content',array('name' => 'wahana_status'));
			$data['jnt_status'] = $this->main_model->get_detail('content',array('name' => 'jnt_status'));
			$data['sicepat_status'] = $this->main_model->get_detail('content',array('name' => 'sicepat_status'));
			$data['lion_status'] = $this->main_model->get_detail('content',array('name' => 'lion_status'));
			$data['origin_city_id'] = $this->main_model->get_detail('content',array('name' => 'origin_city_id'));
			$data['origin_city_name'] = $this->main_model->get_detail('content',array('name' => 'origin_city_name'));
			$data['provinsi'] = $this->main_model->get_list('jne_provinsi');
			$data['orders'] = $this->main_model->get_detail('orders',array('id' => $order_id));
			$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));
			$data['customer'] = $this->main_model->get_detail('customer',array('id' => $data['orders']['customer_id']));
			$data['total_belanja'] = 0;
			foreach ($data['orders_item']->result() as $items) {
				$data['total_belanja'] = $data['total_belanja'] + $items->subtotal;
			}
			$this->load->view('administrator/page_create_order_dropship',$data);

		}

	}



	function get_kota()
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$prov_id = $this->input->post('prov_id');

		$data_kota = $this->main_model->get_list_where('jne_kota',array('kota_prov_id' => $prov_id));

		foreach($data_kota->result() as $list_kota):

			$list_json[] = array('kota_id' => $list_kota->kota_id,'kota_nama' => $list_kota->kota_nama);

		endforeach;

		if($data_kota->num_rows() > 0)
		{

			$data_json = array('status' => 'Success','list' => $list_json);
		} else {

			$data_json = array('status' => 'Failed');
		}

		echo json_encode($data_json);
	}

	function get_shipping_fee()
	{
		// $this->check_hak_akses('Administrator','Staf_kasir');
		$kota_id = $this->input->post('kota_id');

		$data_tarif = $this->main_model->get_list_where('jne_tarif',array('kota_tuju_id' => $kota_id));

		if($data_tarif->num_rows() > 0)
		{

			$data_tarif = $data_tarif->row_array();

			$data_json = array('status' => 'Success','tarif' => $data_tarif[$this->config_tarif]);
		} else {

			$data_json = array('status' => 'Failed');
		}

		echo json_encode($data_json);
	}


	function create_keep_order_to_paid() {
		$this->check_hak_akses('create_order');
		if ($this->input->post('submit') == 'submit') {

			$data['output'] = null;

			$order_item_id = $this->input->post('order_item_id');

			$customer_id =  $this->input->post('customer_id');

			$data_this_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));

			if ($order_item_id > 0) {
				$data_insert = array(
					'customer_id'    => $customer_id,
					'order_datetime' => date('Y-m-d H:i:s'),
					'order_status'   => 'Keep',
					'order_payment'  => 'Paid',
					'jenis_customer' => $data_this_customer['jenis_customer']
				);

				$this->db->insert('orders',$data_insert);

				$order_id = $this->db->insert_id();

				$total = 0;

				for ($i = 0; $i < count($order_item_id); $i++) {
					$data_update = array('order_id' => $order_id,'order_payment' => 'Paid');
					$where = array('id' => $order_item_id[$i]);
					$this->db->update('orders_item',$data_update,$where);
					$data_orders_item = $this->main_model->get_detail('orders_item',array('id' => $order_item_id[$i]));
					$total = $total + $data_orders_item['subtotal'];
				}

				$data_update_order = array('total' => $total,'subtotal' => $total, 'date_payment' => date('Y-m-d H:i:s'));

				$where_order = array('id' => $order_id);
				$this->db->update('orders',$data_update_order,$where_order);

				$data['total'] = $total;
				$data['orders'] = $this->main_model->get_detail('orders',array('id' => $order_id));
				$data['customer'] = $this->main_model->get_detail('customer',array('id' => $customer_id));
				$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));
				$direct = 'kembali';
				$this->session->set_flashdata('message','<div class="alert alert-success">Data Pesanan telah dibuat</div>');
				redirect('administrator/main/order_detail/'.$order_id.'/'.$direct);

			} else {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Tidak ada Item pesanan dipilih</div>');
				redirect('administrator/main/order_per_customer/'.$customer_id);
			}
		} else {
			$check = $this->input->post('check_list');
			$customer_id =  $this->input->post('customer_id');
			$order_item_id = $this->input->post('order_item_id');
			$weight = $this->input->post('weight');
			$qty = $this->input->post('qty');
			$total_weight = 0;

			for ($i = 0;$i < count($order_item_id); $i++) {
				$total_weight =  $total_weight + ($weight[$i]*$qty[$i]);
			}

			$id_order_item =  $this->input->post('id_order_item');
			$subtotal =  $this->input->post('subtotal');

			if ($order_item_id > 0) {
				$data_order = array(
					'customer_id'     => $this->input->post('customer_id'),
					'order_datetime'  => date('Y-m-d H:i:s'),
					'shipping_fee'    => 0,
					'shipping_weight' => $total_weight,
					'shipping_from'   => '',
					'shipping_to'     => '',
					'prov_id'         => 0,
					'kota_id'         => 0,
					'order_status'    => 'Dropship',
					'total'           => 0
				);
				$this->db->insert('orders', $data_order);

				$order_id = $this->db->insert_id();

				for ($i = 0;$i < count($order_item_id); $i++) {
					$data_update = array('order_status' => 'Dropship', 'order_id' => $order_id);
					$where = array('id' => $order_item_id[$i]);
					$this->db->update('orders_item', $data_update, $where);
				}
				redirect('administrator/main/create_order_dropship/'.$order_id.'/unpaid');
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-danger">Tidak ada Item pesanan dipilih</div>');
				redirect('administrator/main/order_per_customer/'.$customer_id);
			}
		}
	}


	// MENU PELANGGAN //
	function customer()
	{
		$this->check_hak_akses('customer');
		$crud = new grocery_CRUD();

		$crud->set_table('customer')
		->set_subject('Customer')
		->display_as('id','ID')
		->display_as('kecamatan_id', 'Kecamatan')
		->display_as('kota_id','Kota')
		->display_as('prov_id','Provinsi')
		->display_as('name','Nama Pelanggan')
		->display_as('address','Alamat')
		->display_as('postcode','Kode Pos')
		->display_as('phone','No. Telpon / HP')
		->display_as('created_at','Waktu Register')
		->display_as('jenis_customer','Jenis Pelanggan')
		->display_as('point','Point')
		->display_as('sudah_otp','Sudah OTP')
		->order_by('id','DESC')
		->required_fields('name','email','password','address','prov_id','kota_id','postcode','phone','point','jenis_customer','status')
		->unset_texteditor('address');

		if ($this->total_available_space_customer || $this->total_max_customer == 'Unlimited')  {
			$crud->add_action('Edit', '#', 'administrator/main/edit_customer','btn btn-success btn-crud');
		}
		$crud->add_action('Keep', '#', 'administrator/main/order_per_customer','btn btn-primary btn-crud')
		->add_action('Detail Pesanan', '#', 'administrator/main/summary_report_customer','btn btn-info btn-crud')
		->callback_before_insert(array($this, 'encrypt_password_customer'))
		->callback_before_update(array($this, 'encrypt_password_customer'))
		->callback_edit_field('password', array($this, 'decrypt_password_customer'))
		->callback_delete(array($this, 'callback_delete_customer'))
		->callback_column('update_status', array($this, 'callback_customer_status'))
		->set_relation('prov_id', 'provinces', 'province')
		->set_relation('kota_id', 'cities', '{type} {city_name}')
		->set_relation('kecamatan_id', 'subdistricts', 'subdistrict_name')
		->callback_column('jenis_customer', array($this, 'callback_customer_type'))
		->callback_column('check', array($this, 'callback_customer_checked'))
		->callback_column('sudah_otp', function($value) {
			$icon = $value == 1 ? 'fa-check text-success' : 'fa-ban text-danger';
			return '<div class="text-center"><i class="fa '.$icon.'"></i></div>';
		})
		->callback_column('point', function($value, $row) {
			return '<a href="' . base_url('administrator/main/point_customer/' . $row->id) . '">' . $value . '</a>';
		});
		if(!$this->total_available_space_customer && $this->total_max_customer != 'Unlimited')  {
			$crud->unset_add();
		}

		$state = $crud->getState();

		if ($this->session->userdata('webadmin_user_level') == 'Staf_admin') {
			if ($state == 'export') {
				$crud->columns('id','created_at','name','email','address','prov_id','kota_id','kecamatan_id','postcode','phone','point','jenis_customer','status');
			} else {
				$crud->unset_delete();
				$crud->columns('check','id','created_at','name','phone','point','jenis_customer','status', 'sudah_otp');
				$crud->fields('name','email','password','address','prov_id','kota_id','kecamatan','postcode','phone','point','jenis_customer');
			}
		} else {
			if ($state == 'export') {
				$crud->columns('id','created_at','name','email','address','prov_id','kota_id', 'kecamatan_id','postcode','phone','point','jenis_customer','status');
			} else {
				$crud->columns('check','id','created_at','name','phone','point','jenis_customer','status','update_status', 'sudah_otp');
				$crud->fields('name','email','password','address','prov_id','kota_id','kecamatan','postcode','phone','point','jenis_customer','status');
			}
		}

		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_read();
		$output = $crud->render();
		$this->get_customer_list($output);
	}

	public function callback_customer_type($value = ''){
		$customer_type = $this->main_model->get_detail('customer_type', ['id' => $value]);
		return $customer_type['name'];
	}

	function callback_subdistrict($value) {
		$data_kecamatan = file_get_contents('http://api.tokomobile.co.id/ongkir/development/api/subdistrict?domain='.$this->domain.'&token='.$this->token.'&subdistrict_id='.$value);
		$result = json_decode($data_kecamatan);
		$kecamatan =  $result->result->subdistrict_name;
		return $kecamatan;
	}

	function callback_city($value) {
		$data_city = file_get_contents('http://api.tokomobile.co.id/ongkir/development/api/city?domain='.$this->domain.'&token='.$this->token.'&city_id='.$value);
		$result = json_decode($data_city);
		if ($result->result->type == 'Kabupaten') {
			$type = 'Kab. ';
		} else {
			$type = 'Kota ';
		}
		return $type . $result->result->city_name;

	}

	function callback_province($value) {
		$data_province = file_get_contents('http://api.tokomobile.co.id/ongkir/development/api/province?domain='.$this->domain.'&token='.$this->token.'&province_id='.$value);
		$result = json_decode($data_province);
		return $result->result->province;
	}

	public function point_customer($customer_id) {
		$this->check_hak_akses('customer');
		$crud = new grocery_CRUD();
		$crud->set_table('point_histories')
		->set_relation('user_id', 'users', 'user_fullname')
		->where('customer_id', $customer_id)
		->order_by('created_at','DESC')
		->set_subject('Riwayat Point')
		->display_as('point_prev', 'Point Sebelumnya')
		->display_as('point_in', 'Point Masuk')
		->display_as('point_out', 'Point Keluar')
		->display_as('point_end', 'Point Akhir')
		->display_as('order_id', 'Order ID')
		->display_as('note', 'Catatan')
		->display_as('user_id', 'User')
		->columns('point_prev', 'point_in', 'point_out', 'point_end', 'order_id', 'note', 'user_id')
		->callback_column('order_id', function ($value) {
			return '<a href="' . base_url('administrator/main/order_detail/' . $value) . '">' . $value . '</a>';
		})
		->callback_column('point_prev', function ($value) {
			return number_format($value, 0, '.', '.');
		})
		->callback_column('point_in', function ($value) {
			return number_format($value, 0, '.', '.');
		})
		->callback_column('point_out', function ($value) {
			return number_format($value, 0, '.', '.');
		})
		->callback_column('point_end', function ($value) {
			return number_format($value, 0, '.', '.');
		})
		->unset_operations()
		->unset_export();
		$output = $crud->render();
		$data = array(
			'output' => $output
		);
		$this->load->view('administrator/page_point_customer', $data);
	}


	function order_per_customer($customer_id = null)
	{
		$this->check_hak_akses('customer');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Seluruh Pesanan');
		if($customer_id > 0)
		{
			$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
			$crud->where('customer_id',$customer_id);
			$data['order_payment'] = 'Semua Pesanan dari Customer ID '.$customer_id.' ('.$data_customer['name'].')';
		} else {
			$data['order_payment'] = 'Semua Pesanan';
		}
		$crud->where('order_status !=','Cancel');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal');
		$crud->display_as('shipping_from','From');
		$crud->display_as('shipping_to','To');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->order_by('id','DESC');
		$crud->columns('id','customer_id','shipping_from','shipping_to','total','order_payment');
		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-primary btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->unset_edit();
		$crud->unset_read();
		$crud->unset_delete();

		$data['output'] = $crud->render();

		$data['orders_item'] = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Unpaid','order_id'=>'0'),null,array('by' => 'id','sorting' => 'DESC'));
		$data['customer_id'] = $customer_id;

		$this->load->view('administrator/page_order_customer',$data);
	}

	// MENU KATALOG PRODUK
	function product($product_type = 'Ready_Stock', $status = 'Publish') {
		$this->check_hak_akses('add_product');
		if ($product_type == 'Ready_Stock') {
			$product_type_label = 'Ready Stock';
		} else {
			$product_type_label = $product_type;
		}

		$suppliers = $this->db->get_where('suppliers', array('active' => 1))->result();
		$this->config->set_item('grocery_crud_file_upload_allow_file_types','gif|jpeg|jpg|png');
		$crud = new grocery_CRUD();
		$crud->set_table('product')
		->set_subject('Products')
		->order_by('datetime','DESC')
		->where('status', $status)
		->display_as('datetime', 'Tanggal')
		->display_as('name_item', 'Nama Produk')
		->display_as('category_id', 'Kategori')
		->display_as('price_production', 'Harga Modal')
		->display_as('weight', 'Berat')
		->display_as('image', 'Foto')
		->display_as('description', 'Deskripsi')
		->display_as('min_order', 'Minimal Order')
		->display_as('no_invoice', 'No. Invoice')
		->display_as('supplier_id', 'Nama Supplier')
		->where('product_type',$product_type_label)
		->columns('check', 'datetime', 'name_item', 'category_id', 'price_production', 'image', 'no_invoice', 'supplier_id')
		->fields('datetime', 'name_item', 'product_type', 'category_id', 'price_production', 'price', 'weight', 'description', 'min_order', 'upload_image', 'image', 'variant')
		->callback_column('check',array($this,'callback_customer_checked'))
		->set_relation('supplier_id','suppliers','nama_supplier')
		->callback_column('no_invoice', function($value, $row) {
			if ($value) {
				$html = $value . ' <button type="button" class="btn btn-sm btn-success" data-toggle="collapse" data-target="#invoice-' . $row->id . '" aria-expanded="false" aria-controls="invoice-' . $row->id . '" title="Ubah"><i class="fa fa-edit"></i></button>';
				$html .= '
				<div class="collapse" id="invoice-' . $row->id . '">
				<input type="text" class="form-control" style="width: 120px" value="' . $value . '">
				<button class="btn btn-primary btn-sm btn-save-invoice" product-id="' . $row->id . '" type="button" style="margin-top: 5px">Simpan</button>
				</div>';
				return $html;
			}
			return;
		})
		->callback_column($this->unique_field_name('supplier_id'), function($value, $row) use ($suppliers) {
			if ($row->supplier_id > 0) {
				$list_supplier = '<select class="form-control supplier" style="width: 120px">';
				foreach ($suppliers as $supplier) {
					$selected = $row->supplier_id == $supplier->id ? 'selected' : '';
					$list_supplier .= '<option ' . $selected . ' value="' . $supplier->id . '">' . $supplier->nama_supplier . '</option>';
				}
				$list_supplier .= '</select>';
				$html = $value . ' <button type="button" class="btn btn-sm btn-success" data-toggle="collapse" data-target="#supplier-' . $row->id . '" aria-expanded="false" aria-controls="supplier-' . $row->id . '" title="Ubah"><i class="fa fa-edit"></i></button>';
				$html .= '
				<div class="collapse" id="supplier-' . $row->id . '">
				' . $list_supplier . '
				<button class="btn btn-primary btn-sm btn-save-supplier" type="button" style="margin-top: 5px" product-id="' . $row->id . '">Simpan</button>
				</div>';
				return $html;
			}
			return;
		})
		->change_field_type('product_type','hidden', $product_type_label)
		->change_field_type('datetime','hidden',date("Y-m-d H:i:s"))
		->set_relation('category_id','product_category','name',array('tipe' => $product_type_label))
		->set_field_upload('image','media/images/original')
		->unset_texteditor('description')
		->unset_add()
		->unset_edit()
		->callback_column('image',array($this,'thumbnailer'))
		->callback_field('variant',array($this,'callback_add_product'))
		->callback_field('weight',array($this,'callback_weight'))
		->callback_edit_field('variant',array($this,'callback_edit_product'))
		->callback_after_insert(array($this,'callback_after_add_product'))
		->callback_after_update(array($this,'callback_after_edit_product'))
		->callback_before_upload(array($this,'image_callback_before_upload'))
		->callback_after_upload(array($this,'image_callback_after_upload'))
		->callback_delete(array($this,'callback_delete_product'))
		->callback_add_field('upload_image',array($this,'callback_add_field_img_product'))
		->callback_edit_field('upload_image',array($this,'callback_add_field_img_product'))
		->required_fields('name_item','category_id','weight','price_production','price','min_order','image','status')
		->add_action('Order', '#', 'administrator/main/order_per_product','btn btn-success btn-crud')
		->add_action('History', '#', 'administrator/main/history_product', 'btn btn-inverse btn-crud');
		if ($product_type == 'Ready_Stock') {
			$crud->add_action('Edit', '#', 'administrator/main/edit_product/ready_stock','btn btn-primary btn-crud');
		} elseif ($product_type == 'PO') {
			$crud->add_action('Edit', '#', 'administrator/main/edit_product/pre_order','btn btn-primary btn-crud');
		}

		if ($product_type == 'Ready_Stock' && $status == 'Publish' ) {
			$crud->add_action('UP', '#', 'administrator/main/up_list_product/Ready_Stock/','btn btn-inverse btn-crud');
		} elseif ($product_type == 'PO' && $status == 'Publish' ) {
			$crud->add_action('UP', '#', 'administrator/main/up_list_product/PO/','btn btn-inverse btn-crud');
		}

		$crud->unset_read();

		if ($this->total_available_space_product == 0) {
			$crud->unset_add();
		}

		if ($this->session->userdata('webadmin_user_level') == 'Staf_admin') {
			$crud->unset_delete();
		}

		if ($status == 'Publish') {
			$stat = 'Publish';
		} else {
			$stat = 'Unpublish';
		}

		if ($product_type == 'Ready_Stock') {
			$type = 'ready';
		} else {
			$type = 'po';
		}

		$output = $crud->render();

		$this->get_product_list($output, $stat, $type);
	}

	public function history_product($id) {
		$this->check_hak_akses('add_product');
		$this->db->select('name_item');
		$product = $this->main_model->get_detail('product', array('id' => $id));
		$data = array(
			'output'       => null,
			'product_name' => $product['name_item']
		);
		$this->load->view('administrator/page_history_product', $data);
	}

	public function getHistoryProduct() {
		$product_id = $this->input->post('product_id');
		$perpage = $this->input->post('perpage');
		$page = $this->input->post('page');
		$columnSearch = $this->input->post('columnSearch');
		$search = $this->input->post('search');

		$offset  = $perpage * ($page - 1);

		$result_data = $this->db->select('COUNT(*) AS total')
		->where('prod_id', $product_id)
		->get('stock_histories')->row_array();

		$this->db->select('user_fullname, C.name, SH.customer_id, SH.created_at, OI.order_datetime, OIC.order_datetime AS cancel_datetime, PV.variant, SH.stock, SH.qty, OI.price, OIC.price AS cancel_price, OI.ref, CT.name AS customer_type, SH.note')
		->from('stock_histories SH')
		->join('users', 'users.id = SH.user_id', 'LEFT')
		->join('customer C', 'C.id = SH.customer_id', 'LEFT')
		->join('customer_type CT', 'CT.id = C.jenis_customer', 'LEFT')
		->join('orders_item OI', 'OI.id = SH.order_item_id', 'LEFT')
		->join('orders_item_cancel OIC', 'OIC.id = SH.order_item_id', 'LEFT')
		->join('product_variant PV', 'PV.id = SH.variant_id', 'LEFT')
		->where('SH.prod_id', $product_id)
		->group_start();
		if ($columnSearch != 'All') {
			$this->db->like($columnSearch, $search, 'BOTH');
		} else {
			$this->db->like('user_fullname', $search, 'BOTH')
			->or_like('C.name', $search, 'BOTH')
			->or_like('PV.variant', $search, 'BOTH')
			->or_like('OI.ref', $search, 'BOTH')
			->or_like('CT.name', $search, 'BOTH');
		}
		$this->db->group_end()
		->order_by('SH.variant_id', 'ASC')
		->order_by('SH.created_at', 'ASC')
		->limit($perpage, $offset);
		$results = $this->db->get()->result();
		foreach ($results as $result) {
			if ($result->customer_id == 0) {
				$result->tanggal_masuk = date('d-m-Y H:i', strtotime($result->created_at));
			} else {
				$result->tanggal_keluar = date('d-m-Y H:i', strtotime($result->created_at));
			}
		}

		$outputs = array(
			'results'    => $results,
			'total_page' => ceil($result_data['total'] / $perpage)
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($outputs));
	}

	private function get_history_product_result() {
		$product_id = $this->input->post('product_id');
		$columnSearch = $this->input->post('columnSearch');
		$search = $this->input->post('search');

		$this->db->select('user_fullname, C.name, SH.customer_id, SH.created_at, OI.order_datetime, OIC.order_datetime AS cancel_datetime, PV.variant, SH.stock, SH.qty, OI.price, OIC.price AS cancel_price, OI.ref, CT.name AS customer_type, SH.note')
		->from('stock_histories SH')
		->join('users', 'users.id = SH.user_id', 'LEFT')
		->join('customer C', 'C.id = SH.customer_id', 'LEFT')
		->join('customer_type CT', 'CT.id = C.jenis_customer', 'LEFT')
		->join('orders_item OI', 'OI.id = SH.order_item_id', 'LEFT')
		->join('orders_item_cancel OIC', 'OIC.id = SH.order_item_id', 'LEFT')
		->join('product_variant PV', 'PV.id = SH.variant_id', 'LEFT')
		->where('SH.prod_id', $product_id)
		->group_start();
		if ($columnSearch != 'All') {
			$this->db->like($columnSearch, $search, 'BOTH');
		} else {
			$this->db->like('user_fullname', $search, 'BOTH')
			->or_like('C.name', $search, 'BOTH')
			->or_like('PV.variant', $search, 'BOTH')
			->or_like('OI.ref', $search, 'BOTH')
			->or_like('CT.name', $search, 'BOTH');
		}
		$this->db->group_end()
		->order_by('SH.variant_id', 'ASC')
		->order_by('SH.created_at', 'ASC');
		return $this->db->get()->result();
	}

	public function export_history_product() {
		$product_id = $this->input->post('product_id');
		$product = $this->main_model->get_detail('product', array('id' => $product_id));
		$results = $this->get_history_product_result();
		$title = 'History Produk ' . $product['name_item'];
		$this->load->library('tcpdf');
		$pdf = new Tcpdf('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle($title);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>' . $title . '</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">User Input</th>
		<th align="center">Pembeli</th>
		<th align="center">Tanggal Masuk</th>
		<th align="center">Tanggal Keluar</th>
		<th align="center">Variant</th>
		<th align="center">Qty</th>
		<th align="center">Qty Akhir</th>
		<th align="center">Subtotal</th>
		<th align="center">Ref</th>
		<th align="center">Jenis Customer</th>
		<th align="center">Catatan</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($results as $result) {
			$tanggal_masuk = date('d-m-Y H:i', strtotime($result->created_at));
			$tanggal_keluar = '';
			$customer = $result->name ? $result->name . ' (' . $result->customer_id . ')' : '';
			$subtotal = $result->name ? 'Rp. ' . number_format($result->qty * $result->price, 0, '.', '.') : '';
			$mark = $result->qty > 0 ? '+' : '';
			$html .= '
			<tr nobr="true">
			<td align="center">' . $result->user_fullname . '</td>
			<td align="center">' . $customer . '</td>
			<td align="center">' . $tanggal_masuk . '</td>
			<td align="center">' . $tanggal_keluar . '</td>
			<td align="center">' . $result->variant . '</td>
			<td align="center">' . $mark . $result->qty . '</td>
			<td align="center">' . $result->stock . '</td>
			<td align="center">' . $subtotal . '</td>
			<td align="center">' . $result->ref . '</td>
			<td align="center">' . $result->customer_type . '</td>
			<td align="center">' . $result->note . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output($title . '.pdf', 'I');
	}

	public function print_history_product() {
		$product_id = $this->input->post('product_id');
		$product = $this->main_model->get_detail('product', array('id' => $product_id));
		$results = $this->get_history_product_result();
		$title = 'History Produk ' . $product['name_item'];
		$html = '
		<head>
		<title>' . $title . '</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">' . $title . '</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">User Input</th>
		<th class="text-center">Pembeli</th>
		<th class="text-center">Tanggal Masuk</th>
		<th class="text-center">Tanggal Keluar</th>
		<th class="text-center">Variant</th>
		<th class="text-center">Qty</th>
		<th class="text-center">Subtotal</th>
		<th class="text-center">Ref</th>
		<th class="text-center">Jenis Customer</th>
		<th class="text-center">Catatan</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($results as $result) {
			$tanggal_masuk = date('d-m-Y H:i', strtotime($result->created_at));
			$tanggal_keluar = '';
			$customer = $result->name ? $result->name . ' (' . $result->customer_id . ')' : '';
			$subtotal = $result->name ? 'Rp. ' . number_format($result->qty * $result->price, 0, '.', '.') : '';
			$mark = $result->qty > 0 ? '+' : '';
			$html .= '
			<tr nobr="true">
			<td align="center">' . $result->user_fullname . '</td>
			<td align="center">' . $customer . '</td>
			<td align="center">' . $tanggal_masuk . '</td>
			<td align="center">' . $tanggal_keluar . '</td>
			<td align="center">' . $result->variant . '</td>
			<td align="center">' . $mark . $result->qty . '</td>
			<td align="center">' . $subtotal . '</td>
			<td align="center">' . $result->ref . '</td>
			<td align="center">' . $result->customer_type . '</td>
			<td align="center">' . $result->note . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	function add_product($product_type = 'ready_stock') {
		$this->check_hak_akses('add_product');
		$data['customer_type'] = $this->db->order_by('id', 'asc')->get('customer_type')->result();
		$data['output'] = null;
		$data['tags'] = $this->db->get_where('name_tag', array('id !=' => 1))->result();

		if ($this->total_available_space_product > 0 || $this->total_max_product == 'Unlimited') {
			$type = $product_type == 'ready_stock' ? 'Ready Stock' : 'PO';
			$data['category'] = $this->main_model->get_list_where('product_category', array('tipe' => $type, 'status_category' => 'publish'));
			$data['suppliers'] = $this->db->get_where('suppliers', array('active' => 1))->result();
			$this->load->view('administrator/page_add_product', $data);
		} else {
			$this->load->view('administrator/page_product_blank', $data);
		}
	}

	function unique_field_name($field_name) {
		return 's'.substr(md5($field_name), 0, 8);
	}

	public function change_supplier() {
		$this->form_validation->set_rules('supplier_id', 'Supplier', 'trim|required');
		$this->form_validation->set_rules('product_id', 'Produk', 'trim|required');
		if ($this->form_validation->run()) {
			$post = $this->input->post();
			$data['supplier_id'] = $post['supplier_id'];
			$this->db->where('product_id', $post['product_id'])
			->update('purchases', $data);
			$this->db->where('id', $post['product_id'])
			->update('product', $data);
			$this->session->set_flashdata('message','<div class="alert alert-success">Supplier berhasil diubah</div>');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function change_invoice() {
		$this->form_validation->set_rules('no_invoice', 'No, Invoice', 'trim|required');
		$this->form_validation->set_rules('product_id', 'Produk', 'trim|required');
		if ($this->form_validation->run()) {
			$post = $this->input->post();
			$data['no_invoice'] = $post['no_invoice'];
			$this->db->where('product_id', $post['product_id'])
			->update('purchases', $data);
			$this->db->where('id', $post['product_id'])
			->update('product', $data);
			$this->session->set_flashdata('message','<div class="alert alert-success">No. Invoice berhasil diubah</div>');
		}
		redirect($_SERVER['HTTP_REFERER']);
	}

	function up_list_product($product_type = null, $prod_id = null) {
		$this->check_hak_akses('add_product');
		$data_update = array(

			'datetime' => date('Y-m-d H:i:s')

		);

		$where = array('id' => $prod_id);

		$this->db->update('product',$data_update,$where);

		if($product_type == 'Ready_Stock')

		{

			redirect('administrator/main/product/Ready_Stock/Publish/');

		}

		else

		{

			redirect('administrator/main/product/PO/Publish');

		}

	}

	function edit_product($product_type = 'ready_stock', $prod_id) {
		$this->check_hak_akses('add_product');
		$data['customer_type'] = $this->db->order_by('id', 'asc')->get('customer_type')->result();
		$data['product_price'] = $this->db->get_where('product_price', array('prod_id' => $prod_id));
		$data['tags'] = $this->db->get_where('name_tag', array('id !=' => 1))->result();
		$product_tags = $this->db->get_where('product_tags', array('product_id' => $prod_id))->result();
		$tags = array();
		foreach ($product_tags as $tag) {
			array_push($tags, $tag->tag_id);
		}
		$data['product_tags'] = $tags;
		$data['output'] = null;
		$data['product'] = $this->main_model->get_detail('product', array('id'=> $prod_id));
		$this->db->order_by('qty_awal', 'asc');
		$data['harga_grosir'] = $this->db->distinct()->select('qty_awal, qty_akhir')->get_where('harga_grosir', array('prod_id'=> $prod_id));
		if ($product_type == 'ready_stock') {
			$data['category'] = $this->main_model->get_list_where('product_category',array('tipe' => 'Ready Stock','status_category' => 'publish'));
		} else {
			$data['category'] = $this->main_model->get_list_where('product_category',array('tipe' => 'PO','status_category' => 'publish'));
		}
		$this->load->view('administrator/page_edit_product', $data);
	}

	public function getVariantProduct($product_id) {
		$variants = $this->db->order_by('variant','asc')
		->get_where('product_variant', array('prod_id'=> $product_id,'available !=' => 'Delete'))->result();
		$this->output->set_content_type('application/json')->set_output(json_encode($variants));
	}

	function delete_variant($variant_id) {
		$this->check_hak_akses('add_product');
		$variant_id = $this->input->post('id_variant');
		$data_update = array( 'available' => 'Delete');
		$where = array('id' => $variant_id);
		$this->db->update('product_variant', $data_update, $where);
	}


	function add_product_process($product_type = 'ready_stock') {
		$this->check_hak_akses('add_product');

		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));

		$this->form_validation->set_error_delimiters('<div class="alert bg-danger"><i class="fa fa-exclamation-circle"></i>', '</div>');
		$this->form_validation->set_rules('name-item', 'Nama Item Produk', 'required');
		$this->form_validation->set_rules('category', 'Nama Kategori Produk', 'required');
		$this->form_validation->set_rules('harga-modal', 'Harga Modal Produk', 'required');
		$this->form_validation->set_rules('berat-product', 'Berat Produk', 'required');
		$this->form_validation->set_rules('minimal-order', 'Minimal Order', 'required');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');
		$this->form_validation->set_rules('variant_product[]', 'Varian Produk', 'required');
		if ($data_value_stock['value'] != 3) {
			$this->form_validation->set_rules('stock_product[]','Stock Produk','required');
		}
		$this->form_validation->set_rules('status','Status Produk','required');

		$customer_type = $this->db->order_by('id', 'asc')->get('customer_type')->result();
		foreach ($customer_type as $row) {
			$this->form_validation->set_rules('price_'.$row->id, 'Harga '.$row->name, 'trim|required');
		}

		$nama_item       = $this->input->post('name-item');
		$category        = $this->input->post('category');
		$harga_modal     = $this->input->post('harga-modal');
		$diskon_checked  = $this->input->post('view_discount_price');
		$berat_produk    = $this->input->post('berat-product');
		$minimal_order   = $this->input->post('minimal-order');
		$deskripsi       = $this->input->post('deskripsi');
		$image_product   = $this->input->post('image_product');
		$qty_awal        = $this->input->post('qty_awal');
		$qty_akhir       = $this->input->post('qty_akhir');
		$price_lokal     = $this->input->post('price_lokal');
		$price_luar      = $this->input->post('price_luar');
		$promo           = $this->input->post('promo');
		$best_seller     = $this->input->post('best_seller');
		$status          = $this->input->post('status');
		$supplier        = $this->input->post('supplier');
		$no_invoice      = $this->input->post('no_invoice');
		$purchase_status = $this->input->post('purchase_status');

		if ($this->form_validation->run()) {
			$now = date('Y-m-d H:i:s');
			$data_insert = array(
				'datetime'         => $now,
				'name_item'        => $nama_item,
				'product_type'     => 'Ready Stock',
				'category_id'      => $category,
				'price_production' => $harga_modal,
				'weight'           => $berat_produk,
				'description'      => $deskripsi,
				'min_order'        => $minimal_order,
				'status'           => $status,
				'no_invoice'       => $no_invoice,
				'supplier_id'      => $supplier
			);
			if ($product_type == 'ready_stock') {
				$data_insert['product_type'] = 'Ready Stock';
			} else {
				$data_insert['product_type'] = 'PO';
			}

			if ($_FILES['video']['size'] > 0) {
				$config_video['upload_path'] = './media/videos';
				$config_video['allowed_types'] = '*';
				$file_video = pathinfo($_FILES['video']['name']);
				$config_video['file_name'] = time() . '.' . $file_video['extension'];
				$this->upload->initialize($config_video);
				if ( ! $this->upload->do_upload('video')) {
					$error = $this->upload->display_errors('', '');
					$this->session->set_flashdata('message','<div class="alert alert-danger">'.$error.'</div>');
					redirect('administrator/add_product/' . $product_type, 'refresh');
				} else {
					$video_data = $this->upload->data();
					$data_insert['video'] = $video_data['file_name'];
				}
			}

			$this->db->trans_start();

			$this->db->insert('product', $data_insert);

			$prod_id = $this->db->insert_id();

			$tag = $this->input->post('tag');
			foreach ($tag as $value) {
				$data_tag = array(
					'product_id' => $prod_id,
					'tag_id'     => $value
				);
				$this->db->insert('product_tags', $data_tag);
			}

			//Upload List Image
			for ($i = 0; $i < 10; $i++) {
				$name_image = $this->input->post('image_'.$i);

				$data_value_img = $this->main_model->get_detail('content',array('name' => 'name_img_setting'));
				if ($data_value_img['value'] == 3 && $name_image != '') {
					$rename_file = preg_replace(array('/[^\w@|\-]/i', '/[-]+/') , '_', $nama_item).'-'.$i.'.jpg';
					rename('./media/images/original/'.$name_image, './media/images/original/'.$rename_file);
					$name_image = $rename_file;
				}

				if ($name_image == '') {
					$insert_image['image'] = '';
					$insert_image['prod_id'] = $prod_id;
					$insert_image['urutan'] = $i + 1;
					$this->db->insert('rel_produk_image', $insert_image);
				} else {
					if ($i != 0) {
						$insert_image['image'] = $name_image;
						$insert_image['prod_id'] = $prod_id;
						$insert_image['urutan'] = $i + 1;
					} else {
						$insert_image['image'] = $name_image;
					}

					// Thumbs
					$config_res['image_library'] = 'gd2';
					$config_res['source_image'] = 'media/images/original/'.$name_image;
					$config_res['maintain_ratio'] = FALSE;
					$config_res['width'] = 100;
					$config_res['height'] = 100;
					$config_res['new_image'] = 'media/images/thumb/'.$name_image;
					$this->image_lib->initialize($config_res);
					$this->image_lib->resize();

					// medium
					$config_res['image_library'] = 'gd2';
					$config_res['source_image'] = 'media/images/original/'.$name_image;
					$config_res['maintain_ratio'] = TRUE;
					$config_res['width'] = 320;
					$config_res['height'] = 320;
					$config_res['new_image'] = 'media/images/medium/'.$name_image;
					$this->image_lib->initialize($config_res);
					$this->image_lib->resize();

					// large
					$config_res['image_library'] = 'gd2';
					$config_res['source_image'] = 'media/images/original/'.$name_image;
					$config_res['maintain_ratio'] = TRUE;
					$config_res['width'] = 640;
					$config_res['height'] = 640;
					$config_res['new_image'] = 'media/images/large/'.$name_image;

					$this->image_lib->initialize($config_res);
					$this->image_lib->resize();

					// original
					$config_res['image_library'] = 'gd2';
					$config_res['source_image'] = 'media/images/original/'.$name_image;
					$config_res['maintain_ratio'] = TRUE;
					$config_res['width'] = 1024;
					$config_res['height'] = 1024;
					$config_res['new_image'] = 'media/images/original/'.$name_image;

					$this->image_lib->initialize($config_res);
					$this->image_lib->resize();

					if ($i != 0) {
						$this->db->insert('rel_produk_image',$insert_image);
					} else {
						$where_image = array('id' => $prod_id);
						$this->db->update('product',$insert_image,$where_image);
					}
				}
			}

			$total_qty = 0;

			$jml = $this->input->post('jml_input');

			$variant_product = $this->input->post('variant_product');

			$stock_product = $this->input->post('stock_product');

			for ($i = 0; $i < count($jml); $i++) {
				$data_var = array(
					'prod_id'   => $prod_id,
					'variant'   => $variant_product[$i],
					'available' => 'Tersedia',
				);
				if ($data_value_stock['value'] != 3) {
					$data_var['stock'] = $stock_product[$i];
					$total_qty += $stock_product[$i];
				}
				$this->db->insert('product_variant', $data_var);
				$variant_id = $this->db->insert_id();
				if ($data_value_stock['value'] != 3) {
					$stock_histories = array(
						'prod_id'    => $prod_id,
						'variant_id' => $variant_id,
						'prev_stock' => 0,
						'stock'      => $stock_product[$i],
						'qty'        => $stock_product[$i],
						'user_id'    => $this->session->userdata('webadmin_user_id'),
						'note'       => 'Masuk Produk',
						'created_at' => $now
					);
					$this->db->insert('stock_histories', $stock_histories);
				}
			}

			/* INSERT HARGA GROSIR */
			if ($qty_awal != null || $qty_awal != '' || $qty_awal != 0) {
				for ($i = 0; $i < count($qty_awal); $i++) {
					foreach ($customer_type as $row) {
						$price = $this->input->post('price_grosir_'.$row->id);
						$data_grosir = array(
							'prod_id'      => $prod_id,
							'qty_awal'     => $qty_awal[$i],
							'qty_akhir'    => $qty_akhir[$i],
							'cust_type_id' => $row->id,
							'price'        => $price[$i]
						);
						$this->db->insert('harga_grosir', $data_grosir);
					}
				}
			}

			// Insert Product Price
			foreach ($customer_type as $row) {
				$diskon = $this->input->post('harga_diskon_'.$row->id);
				if (!$diskon) {
					$price = $this->input->post('price_'.$row->id);
					$old_price = 0;
				} else {
					$price = $diskon;
					$old_price =  $this->input->post('price_'.$row->id);
				}
				$data_price = array(
					'prod_id'      => $prod_id,
					'cust_type_id' => $row->id,
					'price'        => $price,
					'old_price'    => $old_price,
				);
				$this->db->insert('product_price', $data_price);
			}


			/**
			 * Insert Purchase
			*/
			if ($supplier > 0) {
				$data_purchase = array(
					'no_invoice'      => $no_invoice,
					'supplier_id'     => $supplier,
					'product_id'      => $prod_id,
					'qty'             => $total_qty,
					'price'           => $harga_modal,
					'total'           => $total_qty * $harga_modal,
					'purchase_date'   => date('Y-m-d H:i:s'),
					'purchase_status' => $purchase_status,
					'payment_status'  => 'Belum Lunas',
					'user_id'         => $this->session->userdata('webadmin_user_id')
				);
				$this->db->insert('purchases', $data_purchase);
			}

			$this->db->trans_complete();

			$notif_value = $this->input->post('value_notif');
			$notif_select = $this->input->post('notif-select');
			if ($notif_select == 'Ya') {
				$this->fcm_push_all($nama_item, $notif_value, $prod_id);
			}

			if ($this->input->post('redirect_url')) {
				$this->session->set_flashdata('message','<div class="alert alert-success">Produk Anda telah berhasil dibuat</div>');
				redirect($this->input->post('redirect_url'));
			} else {
				if ($product_type == 'ready_stock') {
					$this->session->set_flashdata('message','<div class="alert alert-success">Produk Anda telah berhasil dibuat jika anda ingin kembali ke list produk tekan <a href="'.base_url().'administrator/main/product/Ready_Stock/Publish/" class="btn btn-info">Back</a> </div>');

					redirect('administrator/main/add_product/ready_stock');
				} else {
					$this->session->set_flashdata('message','<div class="alert alert-success">Produk Anda telah berhasil dibuat jika anda ingin kembali ke list produk tekan <a href="'.base_url().'administrator/main/product/PO/Publish/" class="btn btn-info">Back</a> </div>');

					redirect('administrator/main/add_product/pre_order');
				}
			}
		} else {
			$data['output'] = null;
			$data['tags'] = $this->db->get_where('name_tag', array('id !=' => 1))->result();
			$data['customer_type'] = $this->db->order_by('id', 'asc')->get('customer_type')->result();
			$data ['image_error'] = $this->upload->display_errors('<div class="alert alert-danger"><i class="fa fa-exclamation-circle">','</i></div>');
			if ($product_type == 'ready_stock') {
				$data['category'] = $this->main_model->get_list_where('product_category',array('tipe' => 'Ready Stock'));
			} else {
				$data['category'] = $this->main_model->get_list_where('product_category',array('tipe' => 'PO'));
			}
			$this->load->view('administrator/page_add_product', $data);
		}
	}

	function update_product_process($product_type = 'ready_stock') {
		$this->check_hak_akses('add_product');
		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		$this->form_validation->set_error_delimiters('<div class="alert bg-danger"><i class="fa fa-exclamation-circle"></i> ', '</div>');
		$this->form_validation->set_rules('name-item','Nama Item Produk','required');
		$this->form_validation->set_rules('category','Nama Kategori Produk','required');
		$this->form_validation->set_rules('harga-modal','Harga Modal Produk','required');
		$this->form_validation->set_rules('berat-product','Berat Produk','required');
		$this->form_validation->set_rules('minimal-order','Minimal Order','required');
		$this->form_validation->set_rules('deskripsi','Deskripsi','required');
		$this->form_validation->set_rules('variant_product[]','Varian Produk','required');
		$this->form_validation->set_rules('status','Status Produk','required');

		$id_prod         = $this->input->post('id_prod');
		$nama_item       = $this->input->post('name-item');
		$category        = $this->input->post('category');
		$harga_modal     = $this->input->post('harga-modal');
		$harga_jual      = $this->input->post('harga-jual');
		$harga_jual_luar = $this->input->post('harga-jual-luar');
		$diskon_checked  = $this->input->post('view_discount_price');
		$berat_produk    = $this->input->post('berat-product');
		$minimal_order   = $this->input->post('minimal-order');
		$deskripsi       = $this->input->post('deskripsi');
		$harga_diskon    = $this->input->post('harga-diskon');
		$image_product   = $this->input->post('image_product');
		$status          = $this->input->post('status');
		$img_lama        = $this->input->post('img_lama');

		$qty_awal = $this->input->post('qty_awal');
		$qty_akhir = $this->input->post('qty_akhir');
		$price_lokal = $this->input->post('price_lokal');
		$price_luar = $this->input->post('price_luar');

		$promo = $this->input->post('promo');

		$best_seller = $this->input->post('best_seller');

		$data_value_img = $this->main_model->get_detail('content',array('name' => 'name_img_setting'));

		if ($this->form_validation->run()) {
			$now = date('Y-m-d H:i:s');
			$data_update = array(
				'datetime'         => $now,
				'name_item'        => $nama_item,
				'product_type'     => 'Ready Stock',
				'category_id'      => $category,
				'price_production' => $harga_modal,
				'weight'           => $berat_produk,
				'description'      => $deskripsi,
				'min_order'        => $minimal_order,
				'status'           => $status,
			);
			if ($product_type == 'ready_stock') {
				$data_update['product_type'] = 'Ready Stock';
			} else {
				$data_update['product_type'] = 'PO';
			}

			if (!file_exists('./media/videos/') && !is_dir('./media/videos')) {
				mkdir('./media/videos', 0755);
			}

			if ($_FILES['video']['size'] > 0) {
				$config_video['upload_path'] = './media/videos';
				$config_video['allowed_types'] = '*';
				$file_video = pathinfo($_FILES['video']['name']);
				$config_video['file_name'] = time() . '.' . $file_video['extension'];
				$this->upload->initialize($config_video);
				$this->upload->do_upload('video');
				$video_data = $this->upload->data();
				$data_update['video'] = $video_data['file_name'];
			}

			$this->db->trans_start();

			$where = array('id' => $id_prod);
			$this->db->update('product', $data_update, $where);

			$tag = $this->input->post('tag');
			$this->db->delete('product_tags', array('product_id' => $id_prod));
			$this->db->query('ALTER TABLE product_tags AUTO_INCREMENT = 0');
			foreach ($tag as $value) {
				$data_tag = array(
					'product_id' => $id_prod,
					'tag_id'     => $value
				);
				$this->db->insert('product_tags', $data_tag);
			}

			$jml                = $this->input->post('jml_input');
			$variant_product    = $this->input->post('variant_product');
			$stock_product      = $this->input->post('stock_product');
			$id_variant         = $this->input->post('id_variant');
			$jmlup              = $this->input->post('jml_input_update');
			$variant_product_up = $this->input->post('variant_product_update');
			$stock_product_up   = $this->input->post('stock_product_update');

			if ($variant_product_up) {
				for ($i = 0; $i < count($jmlup); $i++) {
					$data_varup = array(
						'prod_id'   => $id_prod,
						'variant'   => $variant_product_up[$i],
						'available' => 'Tersedia',
					);
					if ($data_value_stock['value'] != 3) {
						$data_varup['stock'] = $stock_product_up[$i];
					}
					$this->db->insert('product_variant', $data_varup);
					$variant_id = $this->db->insert_id();
					if ($data_value_stock['value'] != 3) {
						$stock_histories = array(
							'prod_id'    => $id_prod,
							'variant_id' => $variant_id,
							'prev_stock' => 0,
							'stock'      => $stock_product_up[$i],
							'qty'        => $stock_product_up[$i],
							'user_id'    => $this->session->userdata('webadmin_user_id'),
							'note'       => 'Masuk Produk',
							'created_at' => $now
						);
						$this->db->insert('stock_histories', $stock_histories);
					}
				}
			}

			for ($i = 0;$i < count($jml); $i++) {
				$data_var = array(
					'variant'   => $variant_product[$i],
					'available' => 'Tersedia',
				);
				$where = array('id' => $id_variant[$i] );
				$this->db->update('product_variant', $data_var, $where);
			}

			$this->db->delete('harga_grosir', array('prod_id' => $id_prod));
			$this->db->query('ALTER TABLE harga_grosir AUTO_INCREMENT = 0');
			if ($qty_awal != null OR $qty_awal != '' OR $qty_awal != 0) {
				$customer_type = $this->db->order_by('id', 'asc')->get('customer_type')->result();
				for ($i = 0; $i < count($qty_awal); $i++) {
					foreach ($customer_type as $row) {
						$price = $this->input->post('price_grosir_'.$row->id);
						$data_grosir = array(
							'prod_id'      => $id_prod,
							'qty_awal'     => $qty_awal[$i],
							'qty_akhir'    => $qty_akhir[$i],
							'cust_type_id' => $row->id,
							'price'        => $price[$i]
						);
						$this->db->insert('harga_grosir', $data_grosir);
					}
				}
			}

			// Insert Product Price
			$this->db->delete('product_price', array('prod_id' => $id_prod));
			$this->db->query('ALTER TABLE product_price AUTO_INCREMENT = 0');
			$customer_type = $this->db->order_by('id', 'asc')->get('customer_type')->result();
			foreach ($customer_type as $row) {
				$diskon = $this->input->post('harga_diskon_'.$row->id);
				if ($diskon == 0) {
					$price = $this->input->post('price_'.$row->id);
					$old_price = 0;
				} else {
					$price = $diskon;
					$old_price =  $this->input->post('price_'.$row->id);
				}
				$data_price = array(
					'prod_id'      => $id_prod,
					'cust_type_id' => $row->id,
					'price'        => $price,
					'old_price'    => $old_price,
				);
				$this->db->insert('product_price', $data_price);
			}

			$this->db->trans_complete();

			$notif_value = $this->input->post('value_notif');
			$notif_select = $this->input->post('notif-select');
			if ($notif_select == 'Ya') {
				$this->fcm_push_all($nama_item, $notif_value, $id_prod);
			}

			$redirect_url = $this->input->post('redirect_url');
			if ($redirect_url) {
				$this->session->set_flashdata('message','<div class="alert alert-success">Produk Anda telah berhasil diperbarui</div>');
				redirect($redirect_url);
			} else {
				if ($product_type == 'ready_stock') {
					$this->session->set_flashdata('message','<div class="alert alert-success">Produk Anda telah berhasil diperbarui jika anda ingin kembali ke list produk tekan <a href="'.base_url().'administrator/main/product/Ready_Stock/Publish/" class="btn btn-info">Back</a> </div>');
					redirect('administrator/main/edit_product/ready_stock/'.$id_prod);
				} else {
					$this->session->set_flashdata('message','<div class="alert alert-success">Produk Anda telah berhasil diperbarui jika anda ingin kembali ke list produk tekan <a href="'.base_url().'administrator/main/product/PO/Publish/" class="btn btn-info">Back</a> </div>');
					redirect('administrator/main/edit_product/pre_order/'.$id_prod);
				}
			}
		} else {
			$data['output'] = null;
			$data['customer_type'] = $this->db->order_by('id', 'asc')->get('customer_type')->result();
			$data['product_price'] = $this->db->get_where('product_price', array('prod_id' => $id_prod));
			$data['product'] = $this->main_model->get_detail('product', array('id'=> $id_prod));
			$data['image_pro'] = $this->main_model->get_list_where('rel_produk_image',array('prod_id'=> $id_prod));
			$this->db->order_by('qty_awal', 'asc');
			$data['harga_grosir'] = $this->db->distinct()->select('qty_awal, qty_akhir')->get_where('harga_grosir', array('prod_id'=> $id_prod));
			if ($product_type == 'ready_stock'){
				$data['category'] = $this->main_model->get_list_where('product_category',array('tipe' => 'Ready Stock'));
			} else {
				$data['category'] = $this->main_model->get_list_where('product_category',array('tipe' => 'PO'));
			}
			$this->load->view('administrator/page_edit_product', $data);
		}

	}

	public function delete_video_produk() {
		$product_id = $this->input->post('product_id');
		if ($product_id) {
			$where_product = array('id' => $product_id);
			$product = $this->main_model->get_detail('product', $where_product);
			$this->db->update('product', array('video' => ''), $where_product);
			if ($product['video']) {
				$file = './media/videos/' . $product['video'];
				if (file_exists($file)) {
					unlink($file);
				}
			}
			$data = array(
				'status'  => 'Success',
				'message' => 'Video berhasil dihapus'
			);
		} else {
			$data = array(
				'status'  => 'Failed',
				'message' => 'Produk tidak ditemukan'
			);
		}

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	/*function resi_pengiriman($offset = null) {
		$this->check_hak_akses('resi_pengiriman');
		$data_session = array(
			'name'         => null,
			'cust_name'    => null,
			'tamu_name'    => null,
			'no_nota'      => null,
			'radio'        => null,
			'date_payment' => null
		);

		$this->session->set_userdata($data_session);

		$data['output'] = null;
		$this->db->where('order_payment', 'Paid');
		$data_total = $this->db->select('COUNT(*) AS total')
		->get_where('orders', array('order_status' => 'Dropship'))->row_array();

		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url() . 'administrator/main/resi_pengiriman',
			'per_page'        => $perpage,
			'total_rows'      => $data_total['total'],
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$data['arr'] = array(
			'radio' => $this->session->userdata('radio'),
		);
		$this->db->select('orders.id, date_payment, customer_id, name_customer, total, shipping_to, phone_recipient, address_recipient, shipping_status, resi, customer.name')
		->join('customer', 'customer.id = orders.customer_id', 'left');
		$this->db->where('order_payment', 'Paid');
		$orders = $this->main_model->get_list_where('orders', array('order_status' => 'Dropship'), array('perpage' => $perpage, 'offset' => $offset), array('by' => 'orders.id', 'sorting' => 'DESC'))->result();
		$data['orders'] = $orders;
		$this->load->view('administrator/page_resi', $data);
	}


	function resi_update()

	{
		$this->check_hak_akses('resi_pengiriman');
		$items_id = $this->input->post('item_id');

		$no_resi = $this->input->post('no_resi');

		$status = $this->input->post('status');

		for($i = 0; $i < count($items_id); $i++)
		{
			$where = array('id' => $items_id[$i]);
			$order = $this->main_model->get_detail('orders', $where);
			$data_update = array(

				'resi' => $no_resi[$i],
				'shipping_status' => $status[$i],
			);

			$this->db->update('orders',$data_update,$where);

			if ($no_resi[$i]) {
				$subject = 'Info Resi';
				$content = 'Status pesanan Anda #' . $order['id'] . ' telah dikirim dengan nomor resi ' . $no_resi[$i];
				if ($no_resi[$i] && $no_resi[$i] != $order['resi']) {
					$this->sendNotifikasi($order['customer_id'], $subject, $content, 'resi', $order['id']);
				}
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Data Resi Pengiriman telah berhasil anda update</div>');

		if($this->session->userdata('name') != null or $this->session->userdata('tamu_name') != null or $this->session->userdata('no_nota') != null)
		{
			redirect('administrator/main/search_resi_value');
		} else {

			redirect('administrator/main/resi_pengiriman');
		}

	}


	function search_resi_session() {

		$this->check_hak_akses('resi_pengiriman');

		if ($cat_pelanggan = $this->input->post('radio_customer') == "customer") {
			$cat_pelanggan = 'customer';
		} elseif ($cat_pelanggan = $this->input->post('radio_customer') == "tamu") {
			$cat_pelanggan = 'tamu';
		}

		if($this->input->post('customer_name') != null){
			$cust_name = $this->input->post('customer_name');
			$tamu_name = null;
		} else {
			$cust_name = null;
		}

		if($cust_name != '' ) {
			$name = $this->input->post('customer_id');
		} else {
			$name = null;
		}

		if($this->input->post('tamu_name') != null){
			$tamu_name = $this->input->post('tamu_name');
			$cust_name = null;
			$name = null;
		} else {
			$tamu_name = null;
		}

		if($this->input->post('no_nota') != null) {
			$no_nota = $this->input->post('no_nota');
		} else {
			$no_nota = null;
		}

		$date_payment = $this->input->post('date_payment');
		if ($date_payment == null) {
			$date_payment = null;
		} else {
			$date_payment - date('Y-m-d', strtotime($date_payment));
		}

		if($this->input->post('radio_customer') != null) {
			$radio = $this->input->post('radio_customer');
		} else {
			$radio = null;
		}

		$data_session = array(
			'name'         => $name,
			'cat'          => $cat_pelanggan,
			'cust_name'    => $cust_name,
			'tamu_name'    => $tamu_name,
			'no_nota'      => $no_nota,
			'date_payment' => $date_payment,
			'radio'        => $radio
		);

		$this->session->set_userdata($data_session);
		redirect('administrator/main/search_resi_value');

	}



	function search_resi_value($offset = 0) {
		$this->check_hak_akses('resi_pengiriman');
		if($this->session->userdata('cat') == 'customer') {
			$this->db->where('customer_id !=', 0);
		} elseif ($this->session->userdata('cat') == 'tamu') {
			$this->db->where('customer_id', 0);
		}

		if ($this->session->userdata('name')) {
			$this->db->where('customer_id',$this->session->userdata('name'));
		}

		if ($this->session->userdata('tamu_name')) {
			$this->db->where('name_customer',$this->session->userdata('tamu_name'));
		}

		if ($this->session->userdata('no_nota')) {
			$this->db->where('id',$this->session->userdata('no_nota'));
		}

		if ($this->session->userdata('date_payment')) {
			$this->db->where("DATE_FORMAT(date_payment, '%Y-%m-%d') = ",$this->session->userdata('date_payment'));
		}

		$this->db->where('order_payment', 'Paid');
		$data['orders'] = $this->db->get_where('orders', array('order_status' => 'Dropship'));
		$data_total = $data['orders'];
		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url().'administrator/main/search_resi_value',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		if($this->session->userdata('cat') == 'customer') {
			$this->db->where('customer_id !=', 0);
		} elseif ($this->session->userdata('cat') == 'tamu') {
			$this->db->where('customer_id', 0);
		}

		if($this->session->userdata('name')) {
			$this->db->where('customer_id',$this->session->userdata('name'));
		}

		if($this->session->userdata('tamu_name')) {
			$this->db->like('name_customer',$this->session->userdata('tamu_name'));
		}

		if($this->session->userdata('no_nota')) {
			$this->db->where('id',$this->session->userdata('no_nota'));
		}

		if ($this->session->userdata('date_payment')) {
			$this->db->where("DATE_FORMAT(date_payment, '%Y-%m-%d') = ",$this->session->userdata('date_payment'));
		}

		$this->db->where('order_payment', 'Paid');
		$this->db->where('order_status', 'Dropship');
		$data['orders'] = $this->db->get('orders', $perpage, $offset);

		$data['output'] = null;

		$data['arr'] = array(
			'customer_name' => $this->session->userdata('cust_name'),
			'customer_id'   => $this->session->userdata('name'),
			'tamu_name'     => $this->session->userdata('tamu_name'),
			'no_nota'       => $this->session->userdata('no_nota'),
			'date_payment'  => $this->session->userdata('date_payment'),
			'radio'         => $this->session->userdata('radio'),
			'perpage'       => $perpage,
			'offset'        => $offset,
		);

		$this->load->view('administrator/page_resi',$data);
	}*/

	function resi_pengiriman($offset = null) {
		$this->check_hak_akses('resi_pengiriman');
		$data_session = array(
			'name'         => null,
			'cust_name'    => null,
			'tamu_name'    => null,
			'no_nota'      => null,
			'radio'        => null,
			'date_payment' => null
		);

		$this->session->set_userdata($data_session);

		$data['output'] = null;
		$this->db->where('order_payment', 'Paid');
		$data_total = $this->db->select('COUNT(*) AS total')
			->get_where('orders', array('order_status' => 'Dropship'))->row_array();

		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url() . 'administrator/main/resi_pengiriman',
			'per_page'        => $perpage,
			'total_rows'      => $data_total['total'],
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$data['arr'] = array(
			'radio' => $this->session->userdata('radio'),
		);
		$this->db->select('orders.id, date_payment, customer_id, name_customer, total, shipping_to, phone_recipient, address_recipient, shipping_status, resi, customer.name')
			->join('customer', 'customer.id = orders.customer_id', 'left');
		$this->db->where('order_payment', 'Paid');
		$orders = $this->main_model->get_list_where('orders', array('order_status' => 'Dropship'), array('perpage' => $perpage, 'offset' => $offset), array('by' => 'orders.id', 'sorting' => 'DESC'))->result();
		$data['orders'] = $orders;
		$this->load->view('administrator/page_resi', $data);
	}


	function resi_update()

	{
		$this->check_hak_akses('resi_pengiriman');
		$items_id = $this->input->post('item_id');

		$no_resi = $this->input->post('no_resi');

		$status = $this->input->post('status');

		for($i = 0; $i < count($items_id); $i++)
		{
			$where = array('id' => $items_id[$i]);
			$order = $this->main_model->get_detail('orders', $where);
			$data_update = array(

									'resi' => $no_resi[$i],
									'shipping_status' => $status[$i],
								);

			$this->db->update('orders',$data_update,$where);

			if ($no_resi[$i]) {
				$subject = 'Info Resi';
				$content = 'Status pesanan Anda #' . $order['id'] . ' telah dikirim dengan nomor resi ' . $no_resi[$i];
				if ($no_resi[$i] && $no_resi[$i] != $order['resi']) {
					$this->sendNotifikasi($order['customer_id'], $subject, $content, 'resi', $order['id']);
				}
			}
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Data Resi Pengiriman telah berhasil anda update</div>');

		if($this->session->userdata('name') != null or $this->session->userdata('tamu_name') != null or $this->session->userdata('no_nota') != null)
		{
			redirect('administrator/main/search_resi_value');
		} else {

			redirect('administrator/main/resi_pengiriman');
		}

	}


	function search_resi_session() {

		$this->check_hak_akses('resi_pengiriman');

		if ($cat_pelanggan = $this->input->post('radio_customer') == "customer") {
			$cat_pelanggan = 'customer';
		} elseif ($cat_pelanggan = $this->input->post('radio_customer') == "tamu") {
			$cat_pelanggan = 'tamu';
		}

		if($this->input->post('customer_name') != null){
			$cust_name = $this->input->post('customer_name');
			$tamu_name = null;
		} else {
			$cust_name = null;
		}

		if($cust_name != '' ) {
			$name = $this->input->post('customer_id');
		} else {
			$name = null;
		}

		if($this->input->post('tamu_name') != null){
			$tamu_name = $this->input->post('tamu_name');
			$cust_name = null;
			$name = null;
		} else {
			$tamu_name = null;
		}

		if($this->input->post('no_nota') != null) {
			$no_nota = $this->input->post('no_nota');
		} else {
			$no_nota = null;
		}

		$date_payment = $this->input->post('date_payment');
		if ($date_payment == null) {
			$date_payment = null;
		} else {
			$date_payment - date('Y-m-d', strtotime($date_payment));
		}

		if($this->input->post('radio_customer') != null) {
			$radio = $this->input->post('radio_customer');
		} else {
			$radio = null;
		}

		$data_session = array(
			'name'         => $name,
			'cat'          => $cat_pelanggan,
			'cust_name'    => $cust_name,
			'tamu_name'    => $tamu_name,
			'no_nota'      => $no_nota,
			'date_payment' => $date_payment,
			'radio'        => $radio
		);

		$this->session->set_userdata($data_session);
		redirect('administrator/main/search_resi_value');

	}



	function search_resi_value($offset = 0) {
		$this->check_hak_akses('resi_pengiriman');
		if($this->session->userdata('cat') == 'customer') {
			$this->db->where('customer_id !=', 0);
		} elseif ($this->session->userdata('cat') == 'tamu') {
			$this->db->where('customer_id', 0);
		}

		if ($this->session->userdata('name')) {
			$this->db->where('customer_id',$this->session->userdata('name'));
		}

		if ($this->session->userdata('tamu_name')) {
			$this->db->where('name_customer',$this->session->userdata('tamu_name'));
		}

		if ($this->session->userdata('no_nota')) {
			$this->db->where('id',$this->session->userdata('no_nota'));
		}

		if ($this->session->userdata('date_payment')) {
			$this->db->where("DATE_FORMAT(date_payment, '%Y-%m-%d') = ",$this->session->userdata('date_payment'));
		}

		$this->db->where('order_payment', 'Paid');
		$data['orders'] = $this->db->get_where('orders', array('order_status' => 'Dropship'));
		$data_total = $data['orders'];
		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url().'administrator/main/search_resi_value',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		if($this->session->userdata('cat') == 'customer') {
			$this->db->where('customer_id !=', 0);
		} elseif ($this->session->userdata('cat') == 'tamu') {
			$this->db->where('customer_id', 0);
		}

		if($this->session->userdata('name')) {
			$this->db->where('customer_id',$this->session->userdata('name'));
		}

		if($this->session->userdata('tamu_name')) {
			$this->db->like('name_customer',$this->session->userdata('tamu_name'));
		}

		if($this->session->userdata('no_nota')) {
			$this->db->where('id',$this->session->userdata('no_nota'));
		}

		if ($this->session->userdata('date_payment')) {
			$this->db->where("DATE_FORMAT(date_payment, '%Y-%m-%d') = ",$this->session->userdata('date_payment'));
		}

		$this->db->where('order_payment', 'Paid');
		$this->db->where('order_status', 'Dropship');
		$data['orders'] = $this->db->get('orders', $perpage, $offset)->result();

		$data['output'] = null;

		$data['arr'] = array(
			'customer_name' => $this->session->userdata('cust_name'),
			'customer_id'   => $this->session->userdata('name'),
			'tamu_name'     => $this->session->userdata('tamu_name'),
			'no_nota'       => $this->session->userdata('no_nota'),
			'date_payment'  => $this->session->userdata('date_payment'),
			'radio'         => $this->session->userdata('radio'),
			'perpage'       => $perpage,
			'offset'        => $offset,
		);

		$this->load->view('administrator/page_resi',$data);
	}



	function callback_weight($value = '', $primary_key = null)

	{
		return '<input id="field-weight" type="text"  value="'.$value.'" name="weight"><br/><span>*Berat dalam format Kg, example: 0.1 Kg</span>';
	}


	function update_to_ready_stock($prod_id = null)
	{
		$this->check_hak_akses('add_product');
		$data_update = array('product_type' => 'Ready Stock');

		$where = array('id' => $prod_id);

		$this->db->update('product',$data_update,$where);

		$this->session->set_flashdata('message','<div class="alert alert-success">Product telah diubah ke Product Ready Stock</div>');

		redirect('administrator/main/product/PO/Publish/');

	}


	function update_to_pre_order($prod_id = null)
	{
		$this->check_hak_akses('add_product');
		$data_update = array('product_type' => 'PO');

		$where = array('id' => $prod_id);

		$this->db->update('product',$data_update,$where);

		$this->session->set_flashdata('message','<div class="alert alert-success">Product telah diubah ke Product Pre Order</div>');

		redirect('administrator/main/product/Ready_Stock/Publish/');
	}



	function update_status_product($product_type = 'Ready_Stock', $status = null, $prod_id = null)
	{
		$this->check_hak_akses('add_product');
		$data_update = array('status' => $status);

		$where = array('id' => $prod_id);

		$this->db->update('product',$data_update,$where);

		if($status == 'Publish')
		{

			$this->session->set_flashdata('message','<div class="alert alert-success">Product telah di Publish</div>');

			if($product_type == 'Ready_Stock')
			{
				redirect('administrator/main/product/Ready_Stock/Unpublish/');
			} else {

				redirect('administrator/main/product/PO/Unpublish/');
			}
		} elseif($status == 'Unpublish') {

			$this->session->set_flashdata('message','<div class="alert alert-warning">Product telah di Unpublish</div>');

			if($product_type == 'Ready_Stock')
			{
				redirect('administrator/main/product/Ready_Stock/Publish/');
			} else {
				redirect('administrator/main/product/PO/Publish/');
			}
		}
	}


	function product_category()
	{
		$this->check_hak_akses('product_category');
		$crud = new grocery_CRUD();
		$crud->where('status_category','publish');
		$crud->set_table('product_category')
		->set_subject('Product Category');

		$crud->set_rules('name','Nama','required');
		$crud->unset_columns('status_category');
		$crud->field_type('status_category', 'hidden', 'publish');

		if($this->session->userdata('webadmin_user_level') != 'Staf_admin')
		{
			$crud->add_action('Delete', '#', 'administrator/main/product_category_delete','btn btn-danger btn-crud product_category_delete');
		}

		$crud->add_action('Edit', '#', 'administrator/main/product_category/edit','btn btn-primary btn-crud');

		$crud->add_action('View', '#', 'administrator/main/product_category/read','btn btn-info btn-crud');

		$crud->display_as('name','Nama Kategori');

		$crud->unset_delete();
		$data['output'] = $crud->render();

		$this->load->view('administrator/page_product_category',$data);
	}

	function product_category_delete($id)

	{
		$this->check_hak_akses('product_category');
		$data_update = array('status_category' => 'delete');

		$where = array('id' => $id);

		$this->db->update('product_category',$data_update,$where);


		$data_update_product = array('status' => 'delete');

		$where = array('category_id' => $id);

		$this->db->update('product',$data_update_product,$where);

		$dp = $this->main_model->get_list_where('product', array('category_id'=> $id));

		foreach($dp->result() as $items):

			$prod_id = $items->id;

			$data_update_product_variant = array('available' => 'Delete');

			$where_pd = array('prod_id' => $prod_id);

			$this->db->update('product_variant',$data_update_product_variant,$where_pd);

		endforeach;

		redirect('administrator/main/product_category');
	}


	function confirm_payment($status = 'all') {
		$this->check_hak_akses('confirm_payment');
		$crud = new grocery_CRUD();
		$crud->set_table('confirmation')->set_subject('Konfirmasi Pembayaran');
		$crud->add_action('Reject', '#', 'administrator/main/confirm_payment_change_status/Reject','btn btn-danger btn-crud');
		$crud->add_action('Jadikan Lunas', '#', 'administrator/main/confirm_payment_paid/Approve/Paid','btn btn-success btn-crud');
		$crud->add_action('Approve', '#', 'administrator/main/confirm_payment_change_status/Approve','btn btn-success btn-crud');
		$crud->columns('customer_id', 'order_id', 'date', 'name', 'amount', 'bank_account_id', 'bank_account_number', 'attachment', 'order_status', 'status');
		$crud->unset_delete();
		$crud->unset_read();
		$crud->unset_add();
		$crud->unset_edit();
		if ($status && $status != 'all') {
			$crud->where('status', $status);
		}
		$crud->order_by('date', 'desc');
		$crud->display_as('customer_id','Pelanggan');
		$crud->display_as('date','Tanggal Transfer');
		$crud->display_as('amount','Jumlah Transfer');
		$crud->display_as('bank_account_id','Bank Account');
		$crud->display_as('name','Nama Pelanggan');
		$crud->display_as('attachment','Lampiran');
		$crud->display_as('bank_account_number','No. Rekening');
		$crud->display_as('order_status','Status Order');
		$crud->set_relation('bank_account_id', 'bank_accounts', 'nama_bank');
		$crud->callback_column('customer_id',array($this,'callback_customer_name'));
		$crud->callback_column('payment_method_id',array($this,'callback_payment_method'));
		$crud->callback_column('order_id',array($this,'callback_order_id'));
		$crud->callback_column('attachment',array($this,'callback_attachment'));
		$crud->callback_column('status', function($value) {
			return '<span class="' . $value . '">' . $value . '</span>';
		});
		$crud->callback_column('order_status', function($value, $row) {
			if (!$value || $value == 'Keep') {
				$order_id = str_replace('#', '', strip_tags($row->order_id));
				$order_status = !$value || $value == 'Keep' ? 'Rekap' : $value;
				$order = $this->main_model->get_detail('orders', array('id' => $order_id));
				$this->db->update('confirmation', array('order_status' => $order_status), array('id' => $row->id));
				return $order_status;
			} else {
				return $value;
			}
		});
		$output = $crud->render();
		$this->get_confirm_payment($output);
	}

	public function callback_attachment($value) {
		if ($value != '') {
			return '<a href="'.base_url('media/images/attachments/'.$value).'" target="_blank"><img width="200" src="'.base_url('media/images/attachments/'.$value).'"></a>';
		} else {
			return $value;
		}
	}

	public function callback_order_id($value, $row) {
		return '<a href="'.base_url('administrator/main/order_detail/'.$value).'">#'.$value.'</a>';
	}


	function confirm_payment_detail($confirmation_id = null)
	{
		$this->check_hak_akses('confirm_payment');
		$data['output'] = null;

		$data['payment_method'] = $this->main_model->get_list('payment_method');

		$data['confirmation'] = $this->main_model->get_detail('confirmation',array('id' => $confirmation_id));

		$data['confirmation_order']  = $this->main_model->get_detail('orders',array('id' => $data['confirmation']['order_id']));

		$this->load->view('administrator/page_confirm_payment_detail',$data);
	}


	function confirm_payment_change_status($status, $confirmation_id)
	{
		$this->check_hak_akses('confirm_payment');
		if($confirmation_id > 0)
		{

			$data_confirmation = $this->main_model->get_detail('confirmation',array('id' => $confirmation_id));
			$data_update_confirmation = array('status' => $status);
			$where_confirmation = array('id' => $confirmation_id);
			$this->db->update('confirmation',$data_update_confirmation,$where_confirmation);

			if($status == 'Approve') {

				$this->session->set_flashdata('message','<div class="alert alert-success">Data Konfirmasi telah di Approve</div>');
				$subject = 'Konfirmasi Pembayaran';
				$content = 'Pembayaran Anda untuk pesanan #' . $data_confirmation['order_id'] . ' telah di approve oleh Admin';
				$this->sendNotifikasi($data_confirmation['customer_id'], $subject, $content, 'confirm', $data_confirmation['order_id']);
			} else {

				$this->session->set_flashdata('message','<div class="alert alert-warning">Data Konfirmasi telah di Reject</div>');
			}
			redirect('administrator/main/confirm_payment');
		} else {

			$this->session->set_flashdata('message','<div class="alert alert-warning">Data Konfirmasi tidak ditemukan</div>');

			redirect('administrator/main/confirm_payment');
		}
	}

	function confirm_payment_paid($status,$order_payment, $confirmation_id)
	{
		$this->check_hak_akses('confirm_payment');
		if ($confirmation_id > 0) {

			$data_confirmation = $this->main_model->get_detail('confirmation',array('id' => $confirmation_id));
			$data_update_confirmation = array('status' => $status);
			$where_confirmation = array('id' => $confirmation_id);
			$this->db->update('confirmation',$data_update_confirmation,$where_confirmation);

			if ($data_confirmation['order_id'] > 0) {

				$data_update_order = array('order_payment' => 'Paid', 'date_payment' => date('Y-m-d H:i:s'));
				$where_order = array('id' => $data_confirmation['order_id']);
				$this->db->update('orders',$data_update_order,$where_order);

				$data_update_order_items = array('order_payment' => 'Paid');
				$where_order_items = array('order_id' => $data_confirmation['order_id']);
				$this->db->update('orders_item',$data_update_order_items,$where_order_items);

				$this->db->select('id, added_point, customer_id, subtotal, diskon, point');
				$order = $this->main_model->get_detail('orders', array('id' => $data_confirmation['order_id']));
				if ($order['added_point'] == 0) {

					$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
					if ($point_reward_status['value'] == 'on') {
						$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
						$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
						$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
						$point_customer = $customer['point'] + $total_point - $order['point'];

						$point_history = array(
							'customer_id' => $order['customer_id'],
							'point_prev'  => $customer['point'],
							'point_in'    => $total_point - $order['point'],
							'point_end'   => $point_customer,
							'order_id'    => $order['id'],
							'note'        => 'Mendapatkan point',
							'user_id'     => $this->session->userdata('webadmin_user_id'),
						);
						$this->db->insert('point_histories', $point_history);

						$this->db->where('id', $order['customer_id'])
						->update('customer', array('point' => $point_customer));
						$this->db->update('orders', array('added_point' => 1, 'get_point' => 1), array('id' => $order['id']));
					}
				}

				$subject = 'Status Pesanan';
				$content = 'Status pesanan Anda #' . $data_confirmation['order_id'] . ' telah dijadikan lunas';
				$this->sendNotifikasi($data_confirmation['customer_id'], $subject, $content, 'order_status', $data_confirmation['order_id']);

				$this->session->set_flashdata('message','<div class="alert alert-success">Data Konfirmasi telah di Approve dan Pesanan #'.$data_confirmation['order_id'].' telah LUNAS</div>');
			}

			$this->session->set_flashdata('message','<div class="alert alert-success">Data Konfirmasi telah di Approve dan menjadi Lunas</div>');

			redirect('administrator/main/confirm_payment');
		} else {

			$this->session->set_flashdata('message','<div class="alert alert-warning">Data Konfirmasi tidak ditemukan</div>');

			redirect('administrator/main/confirm_payment');
		}
	}


	// PESAN
	function message()
	{
		$this->check_hak_akses('message_add');
		$crud = new grocery_CRUD();
		$crud->set_table('message')
		->set_subject('Message');
		$crud->add_action('Edit', '#', 'administrator/main/message/edit','btn btn-primary btn-crud');
		$crud->add_action('View', '#', 'administrator/main/message/read','btn btn-info btn-crud');
		$crud->display_as('customer_id','Pelanggan');
		$crud->display_as('subject','Subjek');
		$crud->display_as('content','Isi Pesan');
		//$crud->set_relation('customer_id','customer','name');
		$crud->callback_column('customer_id',array($this,'callback_customer_name'));
		$crud->callback_column('image', function($value) {
			if ($value) {
				return '<img width="75" src="' . base_url('media/images/messages/' . $value) . '">';
			} else {
				return '';
			}
		});
		$crud->callback_field('customer_id',array($this,'field_customer_name'));
		$crud->unset_texteditor('content');
		$crud->unset_fields('status');
		$crud->order_by('id','DESC');
		$output = $crud->render();
		$this->get_message_list($output);
	}

	function message_add() {
		$this->check_hak_akses('message_add');
		$data['output'] = null;
		$this->load->view('administrator/page_message_add',$data);
	}



	function field_customer_name($value = '', $primary_key = null)
	{
		$customer = $this->main_model->get_list_where('customer',array('status' => 'active'),null,array('by' => 'name','sorting' => 'ASC'));

		$html ='<select name="customer_id" class="select-input">';
		foreach($customer->result() as $customer)
		{
			$html .='<option value="'.$customer->id.'">'.$customer->name.' ('.$customer->id.')</option>';
		}

		$html .='</select>';

		return $html;
	}


	// PEMBAYARAN
	function methode_pembayaran()
	{
		$this->check_hak_akses('methode_pembayaran');
		$crud = new grocery_CRUD();
		$crud->set_table('payment_method')
		->set_subject('Methode Pembayaran')
		->unset_read();
		// $crud->add_action('Edit', '#', 'administrator/main/methode_pembayaran/edit','btn btn-primary btn-crud');
		// $crud->add_action('View', '#', 'administrator/main/methode_pembayaran/read','btn btn-info btn-crud');
		$crud->display_as('name','Methode Pembayaran');
		$crud->order_by('id','DESC');
		$output = $crud->render();
		$this->get_methode_pembayaran_list($output);
	}



	function payment_methode_process(){
		// $this->check_hak_akses('Administrator');
		$id= $this->input->post('confrim_id');
		$order_id= $this->input->post('order_id');
		$methode_pembayaran= $this->input->post('methode_pembayaran');
		$data_update = array('payment_method_id' => $methode_pembayaran);
		$where = array('order_id' => $order_id);
		$this->db->update('confirmation',$data_update,$where);
		$order_update = array('payment_method_id' => $methode_pembayaran);
		$where_order = array('id' => $order_id);
		$this->db->update('orders',$order_update,$where_order);
		$this->session->set_flashdata('message','<div class="alert alert-success">Methode Pembayaran telah berhasil dirubah</div>');
		redirect('administrator/main/confirm_payment_detail/'.$id);
	}



	function order_payment_methode_process(){
		// $this->check_hak_akses('Administrator');
		$order_id= $this->input->post('order_id');

		$methode_pembayaran= $this->input->post('methode_pembayaran');

		$data_update = array('payment_method_id' => $methode_pembayaran);

		$where = array('order_id' => $order_id);

		$this->db->update('confirmation',$data_update,$where);

		$order_update = array('payment_method_id' => $methode_pembayaran);

		$where_order = array('id' => $order_id);

		$this->db->update('orders',$order_update,$where_order);

		$data_return = array('status' => 'Success');

		echo json_encode($data_return);
	}

	function message_to_multiple() {
		$this->check_hak_akses('message_add');
		$data['output'] = null;
		$data['customer_categories'] = $this->db->order_by('id', 'asc')
		->get('customer_type')->result();
		$this->load->view('administrator/page_message_multiple',$data);
	}

	function message_to_multiple_process() {
		$this->check_hak_akses('message_add');
		$subject = $this->input->post('subject');
		$content = $this->input->post('content');
		$customer_id = $this->input->post('customer_id');
		$url = $this->input->post('url');
		$tipe_penerima = $this->input->post('tipe_penerima');
		$customer_category = $this->input->post('customer_category');

		if ($tipe_penerima == 'customer_type') {
			$customer_id = array();
			$customers = $this->db->select('id')
			->where_in($customer_category)
			->get('customer')->result();
			foreach ($customers as $customer) {
				array_push($customer_id, $customer->id);
			}
		}

		$data_insert = array(
			'subject'     => $subject,
			'content'     => $content
		);

		$messages_path = base_url('media/images/messages/');

		if (!file_exists($messages_path)) {
			mkdir('./media/images/messages');
		}

		if ($_FILES['image']['size'] > 0) {
			$config['upload_path'] = './media/images/messages/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size']  = '512';
			$config['max_width']  = '0';
			$config['max_height']  = '0';

			$this->upload->initialize($config);

			if ( ! $this->upload->do_upload('image')) {
				$error = $this->upload->display_errors('', '');
				$this->session->set_flashdata('message','<div class="alert alert-danger">'.$error.'</div>');
				if ($url == 'message_single') {
					redirect('administrator/main/message_add');
				} elseif ($url == 'message_multiple') {
					redirect('administrator/main/message_to_multiple');
				}
			} else {
				$data_file = $this->upload->data();
				$data_insert['image'] = $data_file['file_name'];

				$config_res['image_library'] = 'gd2';
				$config_res['source_image'] = 'media/images/messages/'.$data_file['file_name'];
				$config_res['maintain_ratio'] = FALSE;
				$config_res['quality'] = 70;
				$config_res['new_image'] = 'media/images/messages/'.$data_file['file_name'];
				$this->image_lib->initialize($config_res);
				$this->image_lib->resize();
			}
		}

		for ($i = 0; $i < count($customer_id); $i++) {
			$data_insert['customer_id'] = $customer_id[$i];

			$this->db->insert('message', $data_insert);
			$id_pesan = $this->db->insert_id();
			$fcm_customer = $this->db->get_where('t_fcm_customer', array('customer_id' => $customer_id[$i]));
			if ($fcm_customer->num_rows() > 0) {
				$this->registrationIds = array();
				foreach ($fcm_customer->result() as $cust) {
					$reg_id = $cust->registration_id;
					array_push($this->registrationIds, $reg_id);
				}
				$this->fcm_push_single($this->registrationIds,'pesan', $subject, $content, 0, $id_pesan);
			}
		}
		$this->session->set_flashdata('message','<div class="alert alert-success">Pesan telah dikirim</div>');
		if ($url == 'message_single') {
			redirect('administrator/main/message_add');
		} elseif ($url == 'message_multiple') {
			redirect('administrator/main/message_to_multiple');
		}
	}


	function stock_session()
	{
		$this->check_hak_akses('stock');
		if($this->input->post('view_pages') != '')

		{

			if ($this->input->post('view_pages') != 'all') {

				$perpage = $this->input->post('view_pages');
			}else{

				$perpage = 1000;
			}

		} else {

			$perpage = 10;
		}

		$data_session = array( 'perpage' => $perpage);

		$this->session->set_userdata($data_session);

		redirect('administrator/main/stock');
	}



	function stock($offset = 0) {
		$this->check_hak_akses('stock');
		$data['output'] = null;
		$this->load->view('administrator/page_stock', $data);
	}

	public function searchStock() {
		$product_id  = $this->input->post('product_id');
		$category_id = $this->input->post('category_id');
		$view_pages  = $this->input->post('view_pages');
		$page        = $this->input->post('page');

		$offset  = $view_pages * ($page - 1);

		$this->db->select('COUNT(*) AS total')
		->join('product P', 'P.id = PV.prod_id')
		->join('product_category PC', 'PC.id = P.category_id')
		->where('P.status !=', 'Delete')
		->where('PV.available !=', 'Delete');
		if ($product_id) {
			$this->db->where('PV.prod_id', $product_id);
		}
		if ($category_id) {
			$this->db->where('P.category_id', $category_id);
		}
		$result_data = $this->db->get('product_variant PV')->row_array();

		$this->db->select('PV.id, PV.variant, PV.stock, P.name_item, PC.name AS category, P.status')
		->select('(SELECT SUM(qty) FROM orders_item WHERE variant_id = PV.id AND order_payment = "Paid" AND order_status != "Cancel") AS terjual')
		->select('(SELECT SUM(qty) FROM orders_item WHERE variant_id = PV.id AND order_status = "Keep" AND order_payment = "Unpaid") AS keep')
		->select('(SELECT SUM(qty) FROM orders_item WHERE variant_id = PV.id AND order_status = "Dropship" AND order_payment = "Unpaid") AS dropship')
		->join('product P', 'P.id = PV.prod_id')
		->join('product_category PC', 'PC.id = P.category_id')
		->where('P.status !=', 'Delete')
		->where('PV.available !=', 'Delete');
		if ($product_id) {
			$this->db->where('PV.prod_id', $product_id);
		}
		if ($category_id) {
			$this->db->where('P.category_id', $category_id);
		}
		if ($view_pages != 'all') {
			$this->db->limit($view_pages, $offset);
		}
		$results = $this->db->order_by('P.datetime', 'DESC')->order_by('PV.id', 'ASC')
		->get('product_variant PV')->result();

		$total_page = $view_pages == 'all' ? 1 : ceil($result_data['total'] / $view_pages);

		$output = array(
			'results'    => $results,
			'total_data' => $result_data['total'],
			'total_page' => $total_page
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	// function updateStock() {
	// 	$user_id = $this->session->userdata('webadmin_user_id');
	// 	$user = $this->db->get_where('users', array('id' => $user_id))->row_array();
	// 	$user_akses_menu = explode(',', $user['akses_menu']);

	// 	if (!in_array('stock', $user_akses_menu)) {
	// 		$output['message'] = 'Akses tidak diijinkan';
	// 	} else {
	// 		$variant_id = $this->input->post('variant_id');
	// 		$newStock = $this->input->post('newStock');
	// 		$note = $this->input->post('note');
	// 		$pincode = $this->input->post('pincode_update_stock');

	// 		$this->db->where_in('user_level', ['Supervisor', 'Manager', 'Superuser']);
	// 		$users = $this->db->get('users')->result();

	// 		$user_approve_id = 0;

	// 		foreach ($users as $user) {
	// 			// if ($pincode == $this->encrypt->decode($user->pincode)) {
	// 			/*if (password_verify($pincode, $user->pincode)) {
	// 				$user_approve_id = $user->id;
	// 				break;
	// 			}*/

	// 			$user_approve_id = password_verify($pincode, $user->pincode) ? $user->id : '';
	// 		}

	// 		if ($user_approve_id == 0) {
	// 			$output['message'] = 'Pemilik PIN Code yang dimasukan tidak memiliki akses ini';
	// 		} else if ($newStock >= 0) {
	// 			$variant = $this->main_model->get_detail('product_variant', array('id' => $variant_id));

	// 			$data_update = array('stock' => $newStock);
	// 			$where = array('id' => $variant_id);
	// 			$this->db->update('product_variant', $data_update, $where);

	// 			$stock_histories = array(
	// 				'prod_id'         => $variant['prod_id'],
	// 				'variant_id'      => $variant_id,
	// 				'prev_stock'      => $variant['stock'],
	// 				'stock'           => $newStock,
	// 				'qty'             => $newStock - $variant['stock'],
	// 				'user_id'         => $this->session->userdata('webadmin_user_id'),
	// 				'note'            => $note,
	// 				'user_approve_id' => $user_approve_id
	// 			);
	// 			$this->db->insert('stock_histories', $stock_histories);
	// 			$output = array(
	// 				'status'  => 'Success',
	// 				'message' => 'Data Stock Telah di Update'
	// 			);
	// 		} else {
	// 			$output['message'] = 'Stok tidak boleh kosong';
	// 		}
	// 	}
	// 	$this->output->set_content_type('application/json')
	// 	->set_output(json_encode($output));
	// }

	function updateStock() {
		$user_id = $this->session->userdata('webadmin_user_id');
		$user = $this->db->get_where('users', array('id' => $user_id))->row_array();
		$user_akses_menu = explode(',', $user['akses_menu']);

		$user_detail = $this->main_model->get_detail('users', array('id' => $user_id));
		
		if (!in_array('stock', $user_akses_menu)) {
			$output['message'] = 'Akses tidak diijinkan';
		} else {
			$variant_id = $this->input->post('variant_id');
			$newStock = $this->input->post('newStock');
			$note = $this->input->post('note');
			$pincode = $this->input->post('pincode_update_stock');


			$pincode_db = password_verify($pincode, $user_detail['pincode']);


			if ($pincode != $pincode_db) {
				$output['message'] = 'Pemilik PIN Code yang dimasukan tidak memiliki akses ini';
			} else if ($newStock >= 0) {
				$variant = $this->main_model->get_detail('product_variant', array('id' => $variant_id));

				$data_update = array('stock' => $newStock);
				$where = array('id' => $variant_id);
				$this->db->update('product_variant', $data_update, $where);

				$stock_histories = array(
					'prod_id'         => $variant['prod_id'],
					'variant_id'      => $variant_id,
					'prev_stock'      => $variant['stock'],
					'stock'           => $newStock,
					'qty'             => $newStock - $variant['stock'],
					'user_id'         => $this->session->userdata('webadmin_user_id'),
					'note'            => $note,
					'user_approve_id' => $user_approve_id
				);
				$this->db->insert('stock_histories', $stock_histories);
				$output = array(
					'status'  => 'Success',
					'message' => 'Data Stock Telah di Update'
				);
			} else {
				$output['message'] = 'Stok tidak boleh kosong';
			}
		}
		$this->output->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	function users_management() {
		$this->check_hak_akses('users_management');
		$crud = new grocery_CRUD();

		$crud->set_table('users')

		->set_subject('users');

		$crud->columns('user_name','user_fullname','user_level', 'user_photo');
		$crud->fields('user_name','user_pass','user_fullname','user_level');

		$crud->display_as('user_photo','Action');

		$crud->where('user_level !=','Superuser');

		$crud->set_field_upload('image','media/images');

		$crud->field_type('user_pass','password');

		$crud->field_type('user_level','dropdown',
			array('Administrator' => 'Owner', 'Staf_admin' => 'Staf Admin','Staf_kasir' => 'Staf Kasir'));

		$crud->callback_before_insert(array($this,'encrypt_password'));

		$crud->callback_before_update(array($this,'encrypt_password'));

    	// $crud->add_action('Edit', '#', 'administrator/main/edit_user','btn btn-primary btn-edit btn-crud');

    	// $crud->add_action('Delete', '', 'administrator/main/users_management/delete','btn btn-danger btn-delete btn-crud');

		$crud->callback_column('user_photo', array($this, 'callback_action_user'));

		$crud->callback_edit_field('user_pass',array($this,'decrypt_password'));

		$crud->unset_operations();
		$crud->unset_delete();

		$output = $crud->render();

        //$this->get_output($output);
		$data['output'] = $output;
		$this->load->view('administrator/page_user_list',$data);
	}

	function add_new_user() {
		$this->check_hak_akses('users_management');
		$data['output'] = null;
		$this->load->view('administrator/page_new_user',$data);
	}

	function delete_user() {
		$id = $this->input->post('id');
		$this->db->delete('users', array('id' => $id));
		$this->session->set_flashdata('message','<div class="alert alert-success">User berhasil dihapus </div>');
		redirect('administrator/main/users_management','refresh');
	}

	function callback_action_user($value, $row) {
		$btn = '<button type="button" class="btn btn-primary btn-sm btn-edit" title="form-edit" no="'.$row->id.'">Edit</button>';
		$btn .= ' <button type="button" class="btn btn-danger btn-sm btn-delete" title="form-delete" no="'.$row->id.'">Delete</button>';
		return $btn;
	}

	function add_user_process() {
		$this->check_hak_akses('users_management');
		$this->form_validation->set_rules('name', 'Nama', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('fullname', 'User Fullname', 'trim|required');
		$this->form_validation->set_rules('menu', 'Akses Menu');

		$password = $this->input->post('password');
		$menu = $this->input->post('menu');
		$pincode = $this->input->post('pincode');
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'user_name'     => $this->input->post('name'),
				/*'user_pass'     => $this->encrypt->encode($password),
				'pincode'       => $this->encrypt->encode($pincode),*/
				'user_pass'     => password_hash($password,PASSWORD_DEFAULT),
				'pincode'       => password_hash($pincode,PASSWORD_DEFAULT),
				'user_email'    => $this->input->post('email'),
				'user_fullname' => $this->input->post('fullname'),
				'akses_menu'    => implode(',', $menu),
				'user_level'    => $this->input->post('user_level')
			);
			$this->db->insert('users', $data);
			$this->session->set_flashdata('message','<div class="alert alert-success">User berhasil ditambahkan </div>');
			redirect('administrator/main/users_management','refresh');
		} else {
			redirect('administrator/main/add_new_user','refresh');
		}
	}

	function edit_user() {
		$this->check_hak_akses('users_management');
		$id = $this->input->post('id');
		$user = $this->main_model->get_detail('users', array('id' => $id));
		$pincode = $this->encrypt->decode($user['pincode']);
		$data = array(
			'output'     => null,
			'user'       => $user,
			'password'   => $this->encrypt->decode($user['user_pass']),
			'pincode'    => $pincode,
			'akses_menu' => explode(',', $user['akses_menu'])
		);
		$this->load->view('administrator/page_edit_user', $data);
	}

	function update_user_process() {
		$this->check_hak_akses('users_management');
		$this->form_validation->set_rules('name', 'Nama', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required');
		//$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('fullname', 'User Fullname', 'trim|required');
		$this->form_validation->set_rules('user_level', 'User Level', 'trim|required');
		$this->form_validation->set_rules('menu', 'Akses Menu');

		$user_id['id'] = $this->input->post('user_id');
		//$password = $this->input->post('password');
		//$pincode = $this->input->post('pincode');
		$menu = $this->input->post('menu');
		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'user_name'     => $this->input->post('name'),
				//'user_pass'     => $this->encrypt->encode($password),
				//'pincode'       => $this->encrypt->encode($pincode),
				'user_email'    => $this->input->post('email'),
				'user_fullname' => $this->input->post('fullname'),
				'user_level'    => $this->input->post('user_level'),
				'akses_menu'    => implode(',', $menu)
			);

			if(($this->input->post('password') != null) or ($this->input->post('password') != '') or ($this->input->post('pincode') != null) or ($this->input->post('pincode') != '') )
			{
				$data['user_pass'] = password_hash($this->input->post('password'),PASSWORD_DEFAULT);
				$data['pincode'] = password_hash($this->input->post('pincode'),PASSWORD_DEFAULT);
			}
			$this->db->update('users', $data, $user_id);
			//$array['pincode'] = $this->encrypt->encode($pincode);
			$this->session->set_userdata($array);
			$this->session->set_flashdata('message','<div class="alert alert-success">User berhasil dirubah </div>');
			redirect('administrator/main/users_management','refresh');
		} else {
			redirect('administrator/main/edit_user/'.$this->input->post('user_id'),'refresh');
		}
	}

	function users()
	{

		$crud = new grocery_CRUD();

		$crud->set_table('users')

		->set_subject('users');

		$crud->set_field_upload('image','media/images');

		$crud->field_type('user_pass','password');

		$crud->callback_before_insert(array($this,'encrypt_password'));

		$crud->callback_before_update(array($this,'encrypt_password'));

		$crud->callback_edit_field('user_pass',array($this,'decrypt_password'));

		$output = $crud->render();

		$this->get_output($output);
	}

	private function getReportPaymentDay()
	{
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');

		$this->db->select('O.id, PM.name AS payment_method, O.customer_id, C.name AS customer, O.name_customer, O.diskon, O.shipping_fee, O.subtotal, O.total, O.date_payment, O.order_datetime')
		->join('payment_method PM', 'O.payment_method_id = PM.id', 'left')
		->join('customer C', 'O.customer_id = C.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m-%d") = "' . $date . '"')
		->where('O.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}

		$reports = $this->db->get('orders O')->result();
		foreach ($reports as $report) {
			$modal = $this->db->select_sum('(P.price_production * OI.qty)', 'total')
			->join('product P', 'OI.prod_id = P.id', 'left')
			->where('OI.order_id', $report->id)
			->where('OI.order_status !=', 'Cancel')
			->get('orders_item OI')->row_array();
			$report->total_modal = $modal['total'];
		}
		$this->db->select('SUM(O.subtotal) AS subtotal, SUM(O.shipping_fee) AS total_shipping, SUM(O.diskon) AS total_diskon')
		->join('customer C', 'O.customer_id = C.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m-%d") = "' . $date . '"')
		->where('O.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}
		$summary = $this->db->get('orders O')->row_array();

		$this->db->select('SUM((P.price_production * OI.qty)) AS total_modal')
		->join('orders O', 'OI.order_id = O.id', 'left')
		->join('customer C', 'OI.customer_id = C.id', 'left')
		->join('product P', 'OI.prod_id = P.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m-%d") = "' . $date . '"')
		->where('O.order_status !=', 'Cancel')
		->where('OI.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}
		$modal = $this->db->get('orders_item OI')->row_array();
		$summary['total_modal'] = $modal['total_modal'];
		return compact('reports', 'summary');
	}

	function report_pembayaran_perday() {
		$this->check_hak_akses('report_pembayaran_perday');
		$data['output'] = null;
		$data['customer_types'] = $this->db->order_by('id', 'asc')->get('customer_type')->result();
		$this->load->view('administrator/page_report_pembayaran_perday',$data);

	}

	function report_pembayaran_perday_process() {
		$this->check_hak_akses('report_pembayaran_perday');
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');

		$data = array(
			'this_date'      => $date,
			'jenis_customer' => $jenis_customer,
			'output'         => null,
			'customer_types' => $this->db->order_by('id', 'asc')->get('customer_type')->result()
		);
		$data = array_merge($data, $this->getReportPaymentDay());
		$this->load->view('administrator/page_report_pembayaran_perday_result', $data);
	}

	public function report_pembayaran_perday_print_settle() {
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');
		if ($jenis_customer != 'All') {
			$customer_type = $this->db->get_where('customer_type', array('id' => $jenis_customer))->row_array();
			$tipe_pelanggan = ' Jenis Pelanggan ' . $customer_type['name'];
		} else {
			$tipe_pelanggan = ' Semua Pelanggan';
		}
		$this->db->select('PM.name AS payment_method')->select_sum('total', 'total')
		->from('orders O')
		->join('payment_method PM', 'O.payment_method_id = PM.id', 'left')
		->join('customer C', 'O.customer_id = C.id', 'left')
		->where('DATE_FORMAT(date_payment,"%Y-%m-%d") = "' . $date . '"')
		->where('O.order_status !=', 'Cancel')
		->where('order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}
		$reports = $this->db->group_by('payment_method_id')
		->order_by('PM.name', 'ASC')
		->get()->result();

		$title = 'Print Settle Tanggal ' . date('d-M-Y', strtotime($date)) . $tipe_pelanggan;
		$html = '
		<head>
		<title>' . $title . '</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		<style>
		td, th {
			font-size: 14px;
		}
		</style>
		</head>
		<div class="container">
		<h3 class="text-center">' . $title . '</h3>
		<div class="text-right" style="margin-bottom: 10px" id="button">
		<button onclick="printData()" class="btn btn-primary">Print</button>
		</div>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center" style="width: 130px">Methode Pembayaran</th>
		<th class="text-center">Total</th>
		</tr>
		</thead>
		<tbody>
		';
		$total = 0;
		foreach ($reports as $report) {
			$html .= '
			<tr nobr="true">
			<td>' . $report->payment_method . '</td>
			<td>Rp. <span class="pull-right">' . number_format($report->total, 0, '.', '.') . '</span></td>
			</tr>
			';
			$total += $report->total;
		}

		$html .= '
		<tr nobr="true">
		<th>Total Pembayaran</th>
		<th>Rp. <span class="pull-right">' . number_format($total, 0, '.', '.') . '</span></th>
		</tr>
		</tbody>
		</table>
		</div>
		<script>
		function printData() {
			window.print();
		}
		window.onbeforeprint = function() {
			document.getElementById(\'button\').style.display = \'none\';
		}
		window.onafterprint = function() {
			document.getElementById(\'button\').style.display = \'block\';
		}
		</script>
		';
		echo $html;
	}

	function report_pembayaran_perday_print()
	{
		$this->check_hak_akses('report_pembayaran_perday');
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');
		$data = array(
			'this_date'      => $date,
			'jenis_customer' => $jenis_customer,
			'output'         => null,
			'header'         => $this->main_model->get_detail('content',array('name' => 'nota')),
			'nama_toko'      => $this->config->item('tokomobile_online_shop'),
		);
		$data = array_merge($data, $this->getReportPaymentDay());
		$this->load->view('administrator/page_report_pembayaran_perday_print',$data);
	}

	function report_pembayaran_perday_eksport() {
		$this->check_hak_akses('report_pembayaran_perday');
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');
		$name_excel = 'Laporan Pembayaran ' . date('d M Y', strtotime($date));

		header('Cache-Control: must-revalidate');
		header('Pragma: must-revalidate');
		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: attachment; filename='.$name_excel.'.xls');

		$data = array(
			'this_date'      => $date,
			'jenis_customer' => $jenis_customer,
			'output'         => null,
			'header'         => $this->main_model->get_detail('content',array('name' => 'nota')),
			'nama_toko'      => $this->config->item('tokomobile_online_shop'),
		);
		$data = array_merge($data, $this->getReportPaymentDay());
		$this->load->view('administrator/page_report_pembayaran_perday_eksport', $data);
	}


	function report_per_day() {
		$this->check_hak_akses('report_per_day');
		$data['output'] = null;
		$this->load->view('administrator/page_report_per_day',$data);

	}

	function report_per_day_process()
	{
		$this->check_hak_akses('report_per_day');
		$data['output'] = null;
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');
		$data['this_date'] = $date;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m-%d') = '$date' AND order_payment = 'Paid'  AND jenis_customer = '$jenis_customer' ";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m-%d') = '$date' AND order_payment = 'Paid' ";
		}

		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_per_day_result',$data);
	}


	function report_per_day_print()
	{
		$this->check_hak_akses('report_per_day');
		$data['output'] = null;
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');
		$data['this_date'] = $date;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m-%d') = '$date' AND order_payment = 'Paid'  AND jenis_customer = '$jenis_customer' ";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m-%d') = '$date' AND order_payment = 'Paid' ";
		}

		$query = $this->db->query($query_clause);
		$data['nama_toko'] = $this->config->item('tokomobile_online_shop');
		$data['header'] = $this->main_model->get_detail('content',array('name' => 'nota'));
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_per_day_print',$data);
	}

	function report_per_day_eksport()
	{
		$this->check_hak_akses('report_per_day');
		$date = $this->input->post('date');
		$jenis_customer = $this->input->post('jenis_customer');
		$name_excel = "Laporan Pesanan ".date('d M Y', strtotime($date));

		header("Cache-Control: must-revalidate");
		header("Pragma: must-revalidate");
		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=".$name_excel.".xls");

		$data['output'] = null;
		$data['this_date'] = $date;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m-%d') = '$date' AND order_payment = 'Paid'  AND jenis_customer = '$jenis_customer' ";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m-%d') = '$date' AND order_payment = 'Paid' ";
		}

		$query = $this->db->query($query_clause);
		$data['nama_toko'] = $this->config->item('tokomobile_online_shop');
		$data['header'] = $this->main_model->get_detail('content',array('name' => 'nota'));
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_per_day_eksport',$data);
	}


	function report_pembayaran_permonth() {
		$this->check_hak_akses('report_pembayaran_perday');
		$data['output'] = null;
		$this->load->view('administrator/page_report_pembayaran_permonth', $data);
	}

	function get_report_pembayaran_permonth($page) {
		$this->check_hak_akses('report_pembayaran_perday');
		$limit = 500;
		$offset = $limit * ($page - 1);
		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');

		$this->db->select('O.id, PM.name AS payment_method, O.customer_id, C.name AS customer, O.name_customer, O.diskon, O.shipping_fee, O.subtotal, O.total, O.date_payment, O.order_datetime')
		->join('payment_method PM', 'O.payment_method_id = PM.id', 'left')
		->join('customer C', 'O.customer_id = C.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m") = "' . $month . '"')
		->where('O.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}

		$reports = $this->db->get('orders O', $limit, $offset)->result();
		foreach ($reports as $report) {
			$modal = $this->db->select_sum('(P.price_production * OI.qty)', 'total')
			->join('product P', 'OI.prod_id = P.id', 'left')
			->where('OI.order_id', $report->id)
			->where('OI.order_status !=', 'Cancel')
			->get('orders_item OI')->row_array();
			$report->total_modal = $modal['total'];
		}
		$summary = array();
		if ($page == 1) {
			$this->db->select('SUM(O.subtotal) AS subtotal, SUM(O.shipping_fee) AS total_shipping, SUM(O.diskon) AS total_diskon')
			->join('customer C', 'O.customer_id = C.id', 'left')
			->where('DATE_FORMAT(O.date_payment,"%Y-%m") = "' . $month . '"')
			->where('O.order_status !=', 'Cancel')
			->where('O.order_payment', 'Paid');
			if ($jenis_customer != 'All') {
				$this->db->where('C.jenis_customer', $jenis_customer);
			}
			$summary = $this->db->get('orders O')->row_array();

			$this->db->select('SUM((P.price_production * OI.qty)) AS total_modal')
			->join('orders O', 'OI.order_id = O.id', 'left')
			->join('customer C', 'OI.customer_id = C.id', 'left')
			->join('product P', 'OI.prod_id = P.id', 'left')
			->where('DATE_FORMAT(O.date_payment,"%Y-%m") = "' . $month . '"')
			->where('O.order_status !=', 'Cancel')
			->where('OI.order_status !=', 'Cancel')
			->where('O.order_payment', 'Paid');
			if ($jenis_customer != 'All') {
				$this->db->where('C.jenis_customer', $jenis_customer);
			}
			$modal = $this->db->get('orders_item OI')->row_array();
			$summary['total_modal'] = $modal['total_modal'];
		}

		$data = array(
			'limit'   => $limit,
			'reports' => $reports,
			'summary' => $summary
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	function get_detail_report_payment($id) {
		$order_items = $this->db->select_sum('(P.price_production * OI.qty)', 'total_modal')
		->select('OI.id, P.name_item, P.price_production, OI.qty')
		->join('product P', 'OI.prod_id = P.id', 'left')
		->where('OI.order_id', $id)
		->where('OI.order_status !=', 'Cancel')
		->group_by('OI.id')
		->get('orders_item OI')->result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($order_items));
	}

	private function getReportPaymentMonth()
	{
		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');

		$this->db->select('O.id, PM.name AS payment_method, O.customer_id, C.name AS customer, O.name_customer, O.diskon, O.shipping_fee, O.subtotal, O.total, O.date_payment, O.order_datetime')
		->join('payment_method PM', 'O.payment_method_id = PM.id', 'left')
		->join('customer C', 'O.customer_id = C.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m") = "' . $month . '"')
		->where('O.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}

		$reports = $this->db->get('orders O')->result();
		foreach ($reports as $report) {
			$modal = $this->db->select_sum('(P.price_production * OI.qty)', 'total')
			->join('product P', 'OI.prod_id = P.id', 'left')
			->where('OI.order_id', $report->id)
			->where('OI.order_status !=', 'Cancel')
			->get('orders_item OI')->row_array();
			$report->total_modal = $modal['total'];
		}
		$summary = array();
		$this->db->select('SUM(O.subtotal) AS subtotal, SUM(O.shipping_fee) AS total_shipping, SUM(O.diskon) AS total_diskon')
		->join('customer C', 'O.customer_id = C.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m") = "' . $month . '"')
		->where('O.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}
		$summary = $this->db->get('orders O')->row_array();

		$this->db->select('SUM((P.price_production * OI.qty)) AS total_modal')
		->join('orders O', 'OI.order_id = O.id', 'left')
		->join('customer C', 'OI.customer_id = C.id', 'left')
		->join('product P', 'OI.prod_id = P.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m") = "' . $month . '"')
		->where('O.order_status !=', 'Cancel')
		->where('OI.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		if ($jenis_customer != 'All') {
			$this->db->where('C.jenis_customer', $jenis_customer);
		}
		$modal = $this->db->get('orders_item OI')->row_array();
		$summary['total_modal'] = $modal['total_modal'];

		return compact('reports', 'summary');
	}

	function report_pembayaran_permonth_print()

	{
		$this->check_hak_akses('report_pembayaran_perday');

		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');

		$data = array(
			'this_month'     => $month,
			'jenis_customer' => $jenis_customer,
			'output'         => null,
			'header'         => $this->main_model->get_detail('content',array('name' => 'nota')),
			'nama_toko'      => $this->config->item('tokomobile_online_shop'),
		);
		$data = array_merge($data, $this->getReportPaymentMonth());

		$this->load->view('administrator/page_report_pembayaran_permonth_print', $data);

	}

	function report_pembayaran_permonth_eksport()
	{
		$this->check_hak_akses('report_pembayaran_perday');
		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');

		$name_excel = 'Laporan Pembayaran Bulan '.date('M Y', strtotime($month));

		header('Cache-Control: must-revalidate');
		header('Pragma: must-revalidate');

		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: attachment; filename='.$name_excel.'.xls');

		$data = array(
			'this_month'     => $month,
			'jenis_customer' => $jenis_customer,
			'output'         => null,
			'header'         => $this->main_model->get_detail('content',array('name' => 'nota')),
			'nama_toko'      => $this->config->item('tokomobile_online_shop'),
		);
		$data = array_merge($data, $this->getReportPaymentMonth());

		$this->load->view('administrator/page_report_pembayaran_permonth_eksport',$data);
	}


	function report_per_month()

	{
		$this->check_hak_akses('report_per_day');
		$data['output'] = null;
		$this->load->view('administrator/page_report_per_month',$data);
	}

	function report_per_month_process()

	{
		$this->check_hak_akses('report_per_day');
		$data['output'] = null;
		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');
		$data['this_month'] = $month;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' AND jenis_customer = '$jenis_customer'";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' ";
		}
		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_per_month_result',$data);
	}


	function report_per_month_print()
	{
		$this->check_hak_akses('report_per_day');
		$data['output'] = null;

		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');
		$data['header'] = $this->main_model->get_detail('content',array('name' => 'nota'));

		$data['this_month'] = $month;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' AND jenis_customer = '$jenis_customer'";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' ";
		}

		$query = $this->db->query($query_clause);

		$data['transaksi'] = $query;

		$this->load->view('administrator/page_report_per_month_print',$data);
	}


	function report_per_month_eksport()
	{
		$this->check_hak_akses('report_per_day');
		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');
		$name_excel = "Laporan Pesanan Bulan".date('M Y', strtotime($month));

		header("Cache-Control: must-revalidate");
		header("Pragma: must-revalidate");

		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=".$name_excel.".xls");

		$data['output'] = null;

		$data['header'] = $this->main_model->get_detail('content',array('name' => 'nota'));

		$data['this_month'] = $month;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' AND jenis_customer = '$jenis_customer'";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' ";
		}

		$query = $this->db->query($query_clause);

		$data['transaksi'] = $query;

		$this->load->view('administrator/page_report_per_month_eksport',$data);
	}


	function ekspedisi_jne_prov()
	{
		$this->check_hak_akses('Administrator');
		$data['prov'] = null;

		$data['kota'] = null;

		$crud = new grocery_CRUD();

		$crud->set_table('jne_provinsi')

		->set_subject('Data Provinsi');

		$crud->columns('provinsi_nama');

		$crud->add_action('Edit', '#', 'administrator/main/ekspedisi_jne_prov/edit','btn btn-primary btn-crud');

		$crud->add_action('Daftar Kota', '#', 'administrator/main/ekspedisi_jne_kota','btn btn-primary btn-crud');

		$output = $crud->render();

		$data['output'] = $output;

		$this->load->view('administrator/page_jne_list',$data);
	}
	function tarif_ekspedisi()
	{
		$this->check_hak_akses('Administrator');

		$crud = new grocery_CRUD();

		$crud->set_table('tarif')

		->set_subject('Data Tarif');

		$crud->columns('kota_kabupaten','kecamatan','reg','oke','yes');
		$crud->fields('kota_kabupaten','kecamatan','reg','oke','yes');

		$crud->add_action('Edit', '#', 'administrator/main/tarif_ekspedisi/edit','btn btn-primary btn-crud');

		//$crud->add_action('Daftar Kota', '#', 'administrator/main/ekspedisi_jne_kota','btn btn-primary btn-crud');

		$crud->unset_add();

		$output = $crud->render();

		$data['output'] = $output;

		$this->load->view('administrator/page_jne_list',$data);
	}


	function ekspedisi_jne_kota($prov_id = 1)
	{
		$this->check_hak_akses('Administrator');
		$data['prov'] = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $prov_id));

		$data['kota'] = null;

		$crud = new grocery_CRUD();

		$crud->set_table('jne_kota')

		->set_subject('Data Kota di Provinsi <b>'.$data['prov']['provinsi_nama'].'</b>');

		$crud->columns('kota_nama');

		$crud->fields('kota_id','kota_nama','kota_prov_id');

		$crud->add_action('Edit', '#', 'administrator/main/ekspedisi_jne_kota/'.$prov_id.'/edit','btn btn-primary btn-crud');

		//$crud->add_action('Daftar Kecamatan', '#', 'administrator/main/ekspedisi_jne','btn btn-primary btn-crud');

		$crud->where('kota_prov_id',$prov_id);

		$crud->change_field_type('kota_prov_id','hidden',$prov_id);

		$crud->change_field_type('kota_id','hidden');

		$crud->callback_add_field('kota_nama', array($this,"callback_add_field_jne"));

		$crud->callback_before_insert(array($this,'callback_before_insert_jne'));

		$crud->callback_edit_field('kota_nama', array($this,"callback_edit_field_jne"));

		$crud->callback_before_update(array($this,'callback_before_update_jne'));

		$output = $crud->render();

		$data['output'] = $output;

		$this->load->view('administrator/page_jne_list',$data);
	}



	function callback_add_field_jne()
	{
		return '<input type="text" maxlength="50" value="" name="kota_nama"><br/>Tarif<br/><input type="text" maxlength="50" value="" name="tarif" />';
	}



	function callback_edit_field_jne($value,$primary_key)
	{
		$data_tarif = $this->main_model->get_detail('jne_tarif',array('kota_tuju_id' => $primary_key));
		return '<input type="text" maxlength="50" value="'.$value.'" name="kota_nama"><br/>Tarif<br/><input type="text" maxlength="50" value="'.$data_tarif['reg'].'" name="tarif" />';
	}



	function callback_before_insert_jne($post_array)
	{

		$this->db->select_max('kota_id');
		$data_max_jne = $this->db->get('jne_kota')->row_array();
		$primary_key = $data_max_jne['kota_id'] + 1;
		$post_array['kota_id'] = $primary_key;


		// get_data_tarif
		$this->db->select_max('tarif_id');
		$data_max_tarif = $this->db->get('jne_tarif')->row_array();
		$primary_key_tarif = $data_max_tarif['tarif_id'] + 1;


		// get_data asal
		$data_jne = $this->main_model->get_list('jne_tarif')->row_array();


		// Insert to data Tarif
		$data_insert_tarif = array(

			'tarif_id' => $primary_key_tarif,

			'prov_asal_id' => $data_jne['prov_asal_id'],

			'kota_asal_id' => $data_jne['kota_asal_id'],

			'prov_tuju_id' => $post_array['kota_prov_id'],

			'kota_tuju_id' => $primary_key,

			'reg' => $post_array['tarif']

		);

		$this->db->insert('jne_tarif',$data_insert_tarif);
		return $post_array;

	}



	function callback_before_update_jne($post_array)
	{

		// Insert to data Tarif
		$data_update_tarif = array(

			'reg' => $post_array['tarif']

		);

		$where = array('kota_tuju_id' => $post_array['kota_id']);
		$this->db->update('jne_tarif',$data_update_tarif,$where);
		return $post_array;

	}



	function ekspedisi_jne($kota_id = 1)

	{
		$this->check_hak_akses('Administrator');
		$data['kota'] = $this->main_model->get_detail('jne_kota',array('kota_id' => $kota_id));

		$data['prov'] = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $data['kota']['kota_prov_id']));

		$crud = new grocery_CRUD();

		$crud->set_table('jne_tarif')

		->set_subject('Tarif JNE untuk Kota <b>'.$data['kota']['kota_nama'].'</b>');

		$crud->where('kota_tuju_id',$kota_id);

		$crud->display_as('prov_asal_id','Provinsi Asal');

		$crud->display_as('kota_asal_id','Kota Asal');

		$crud->display_as('prov_tuju_id','Provinsi Tujuan');

		$crud->display_as('kota_tuju_id','Kota Tujuan');

		$crud->display_as('reg','Tarif (REG)');

		$crud->columns('tarif_id','kecamatan','reg');

		$crud->fields('kecamatan','prov_asal_id','kota_asal_id','prov_tuju_id','kota_tuju_id','reg');

		$crud->change_field_type('prov_asal_id','hidden',6);

		$crud->change_field_type('kota_asal_id','hidden',93);

		$crud->change_field_type('prov_tuju_id','hidden',$data['kota']['kota_prov_id']);

		$crud->change_field_type('kota_tuju_id','hidden',$kota_id);

		$crud->add_action('Edit', '#', 'administrator/main/ekspedisi_jne/'.$kota_id.'/edit','btn btn-primary btn-crud');

		$crud->add_action('View', '#', 'administrator/main/ekspedisi_jne/'.$kota_id.'/read','btn btn-info btn-crud');

		$crud->set_field_upload('category_image','media/images');

		$crud->field_type('user_pass','password');

		$crud->callback_before_insert(array($this,'encrypt_password'));

		$crud->callback_before_update(array($this,'encrypt_password'));

		$crud->callback_edit_field('user_pass',array($this,'decrypt_password'));

		$output = $crud->render();

		$data['output'] = $output;

		$this->load->view('administrator/page_jne_list',$data);
	}


	function edit_profile()
	{

		$data['output'] = null;
		$this->load->view('administrator/page_edit_profile',$data);
	}



	function update_vertion()
	{

		$this->check_hak_akses('Administrator');
		$data['output'] = null;
		$data_version_last = $this->main_model->get_list('data_version_update',array('perpage' => 1,'offset' => 0),array('by' => 'id','sorting' => 'DESC'));

		$data_array_version_last = $data_version_last->row_array();

		$name_version_last = $data_array_version_last['name_version'];

		$name_version_new = $name_version_last + 0.1;

		$number_new_vertion = $name_version_new * 10;

		// file directory

		$file = 'http://tokomobile.co.id/development/application/update_version/version-'.$name_version_new.'.zip';

		$newfile = './application/new-version.zip';

		// copy file

		copy($file, $newfile);


		// Update file

		$zip = new ZipArchive;

		$output_file = "./application";

		$get_directory = "./application/new-version.zip";

		if ($zip->open($get_directory) === TRUE) {

			$zip->extractTo($output_file);

			$zip->close();

		} else {

			echo 'failed';

		}

		unlink('./application/new-version.zip');

		$data_new_version = array(



			'name_version' => $name_version_new,

			'date' => date("Y-m-d"),

			'status' => 'active',

			'number_version ' => $number_new_vertion,

		);

		$this->db->insert('data_version_update',$data_new_version);

		$this->session->set_flashdata('message','<div class="alert alert-success">Anda Telah Berhasil Melakukan Update Versi Yang Baru </div>');

		redirect('administrator/main');
	}



	function checked()
	{

		$checked = $this->input->post('checked_box');

		if($checked == 'checked')
		{

			$var_session = array('hide_popup' => TRUE);

			$this->session->set_userdata($var_session);
		}

	}







	function edit_profile_process()
	{

		$data['output'] = null;

		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

		$this->form_validation->set_rules('user_email','Email','required');

		$this->form_validation->set_rules('user_name','Username','required');

		$this_user = $this->main_model->get_list_where('users',array('user_name' => $this->input->post('user_name')));

		if($this->form_validation->run() === TRUE)
		{

			// $pincode = $this->input->post('pincode');
			$data_update = array(
				'user_email' => $this->input->post('user_email'),
				'user_name' => $this->input->post('user_name'),
			);

			/*if ($pincode != '') {
				$data_update['pincode'] = $this->encrypt->encode($pincode);
				$array['pincode'] = $pincode;
				$this->session->set_userdata( $array );
			}*/

			if(($this->input->post('user_pass') != null) or ($this->input->post('user_pass') != ''))
			{
				$data_update['user_pass'] = password_hash($this->input->post('user_pass'),PASSWORD_DEFAULT);
			}

			if(($this->input->post('pincode') != null) or ($this->input->post('pincode') != ''))
			{
				$data_update['pincode'] = password_hash($this->input->post('pincode'),PASSWORD_DEFAULT);
			}

			$where = array('id' => $this->session->userdata('webadmin_user_id'));
			$this->db->update('users',$data_update,$where);

			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah di update</div>');
			redirect('administrator/main/edit_profile');
		} else {

			$this->load->view('administrator/page_edit_profile',$data);
		}
	}



	function edit_info() {
		// $user_akses_menu = $this->session->userdata('user_akses_menu');
		$user_id = $this->session->userdata('webadmin_user_id');
		$user_login = $this->db->get_where('users', array('id' => $user_id))->row_array();
		$user_akses_menu = explode(',', $user_login['akses_menu']);

		if (in_array('edit_info/toko', $user_akses_menu) || in_array('home_slideshow', $user_akses_menu) || in_array('edit_info/stok', $user_akses_menu) || in_array('edit_info/image', $user_akses_menu) || in_array('edit_info/nota', $user_akses_menu) || in_array('edit_info/link', $user_akses_menu) || in_array('edit_info/aplikasi', $user_akses_menu) || in_array('ekspedisi', $user_akses_menu) || in_array('content_faq', $user_akses_menu)) {
			$data['output'] = null;
			$this->load->view('administrator/page_edit_info',$data);
		} else {
			echo "<script>alert('Akses tidak diijinkan');window.location.href ='".base_url('administrator/main')."';</script>";
		}

	}


	function edit_info_process($tab) {
		// $user_akses_menu = $this->session->userdata('user_akses_menu');

		$user_id = $this->session->userdata('webadmin_user_id');
		$user_login = $this->db->get_where('users', array('id' => $user_id))->row_array();
		$user_akses_menu = explode(',', $user_login['akses_menu']);
		if (in_array('edit_info/toko', $user_akses_menu) || in_array('home_slideshow', $user_akses_menu) || in_array('edit_info/stok', $user_akses_menu) || in_array('edit_info/image', $user_akses_menu) || in_array('edit_info/nota', $user_akses_menu) || in_array('edit_info/link', $user_akses_menu) || in_array('edit_info/aplikasi', $user_akses_menu) || in_array('ekspedisi', $user_akses_menu) || in_array('content_faq', $user_akses_menu)) {


			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');

			$set_stock = $this->input->post('view_stock');

			$set_name_image = $this->input->post('name_image');

			$set_status_aplication = $this->input->post('status_aplication');

			$set_message_off = $this->input->post('message_off');

			$set_tool_tips = $this->input->post('tool_tips');

			$set_due_date = $this->input->post('due_date').' '.$this->input->post('due_date_tipe');

			$status_due_date = $this->input->post('status_due_date');

			$set_data_shipping = $this->input->post('data_shipping');

			$format_ekspedisi = $this->input->post('format_ekspedisi');

			$show_id_print_nota = $this->input->post('show_id_print_nota');

			$show_estimasi_print_nota = $this->input->post('show_estimasi_print_nota');

			$aktif_logo = $this->input->post('aktif_logo');

			if ($tab == 'toko') {
    			// Update info toko
				$data_update = array('value' => $this->input->post('info_toko'));
				$where = array('name' => 'info');
				$this->db->update('content',$data_update,$where);

    			// Update info rekening
				$data_update = array('value' => $this->input->post('info_rekening'));
				$where = array('name' => 'rekening');
				$this->db->update('content',$data_update,$where);

    			// Update info kontak
				$data_update = array('value' => $this->input->post('info_kontak'));
				$where = array('name' => 'kontak');
				$this->db->update('content',$data_update,$where);

    			// Update Login Background Image
				$config['upload_path'] = './media/images';
				$config['allowed_types'] = '*';
				$field_name = 'background_login';
				$this->upload->initialize($config);
				$this->upload->do_upload($field_name);
				$data_file = $this->upload->data();
				$image_lama =  $this->input->post('background_lama');
				if ($data_file['file_name'] != null){
					$data_background_image[$field_name] = $data_file['file_name'];
					$data_update_background = array('value' => $data_background_image[$field_name]);
					$where = array('name' => 'login_background_image');
					$this->db->update('content',$data_update_background,$where);

					if (is_file('./media/images/'.$image_lama)){
						unlink('./media/images/'.$image_lama);
					}
				}

    			//HEADER COLOR
				$data_update = array('value' => $this->input->post('header_color'));
				$where = array('name' => 'header_color');
				$this->db->update('content', $data_update, $where);

    			//FONT COLOR
				$data_update = array('value' => $this->input->post('font_color'));
				$where = array('name' => 'font_color');
				$this->db->update('content', $data_update, $where);

    			//NOTIFIKASI COLOR
				$notifikasi = $this->input->post('notifikasi');
				$notifikasi = $notifikasi ? $notifikasi : [];
				$data_update = array('value' => implode(',', $notifikasi));
				$where = array('name' => 'notifikasi');
				$this->db->update('content', $data_update, $where);

    			//PENGATURAN NO WA
				$data_update = array('value' => $this->input->post('no_wa'));
				$where = array('name' => 'no_wa');
				$this->db->update('content', $data_update, $where);

				//PENGATURAN STATUS CREATE ORDER NON PELANGGAN
				$data_update = array('value' => $this->input->post('order_non_pelanggan_status'));
				$where = array('name' => 'order_non_pelanggan_status');
				$this->db->update('content', $data_update, $where);
			} else if ($tab == 'stok') {
    			// value setting stock
				$data_update = array('value' => $set_stock);
				$where = array('name' => 'stock_setting');
				$this->db->update('content',$data_update,$where);

    			// value setting stock limited
				$data_update = array('value' => $this->input->post('stok_limited'));
				$where = array('name' => 'stok_limited');
				$this->db->update('content',$data_update,$where);
			} else if ($tab == 'nota') {
    			// Show Data Shipping
				$data_update = array('value' => $set_data_shipping);
				$where = array('name' => 'shipping_show');
				$this->db->update('content',$data_update,$where);

				$data_update = array('value' => $format_ekspedisi);
				$where = array('name' => 'format_ekspedisi');
				$this->db->update('content',$data_update,$where);

				$data_update = array('value' => $show_id_print_nota);
				$where = array('name' => 'show_id_print_nota');
				$this->db->update('content',$data_update,$where);

				$data_update = array('value' => $show_estimasi_print_nota);
				$where = array('name' => 'show_estimasi_print_nota');
				$this->db->update('content',$data_update,$where);

				$data_update = array('value' => $this->input->post('show_price'));
				$where = array('name' => 'show_price_on_print_expedition');
				$this->db->update('content',$data_update,$where);

    			// Update aktif logo nota
				$data_update = array('value' => $aktif_logo);
				$where = array('name' => 'aktif_logo');
				$this->db->update('content',$data_update,$where);

    			// Update Header Nota
				$data_update = array('value' => $this->input->post('info_nota'));
				$where = array('name' => 'nota');
				$this->db->update('content',$data_update,$where);

    			// value footer
				$data_update = array('value' => $this->input->post('footer_nota'));
				$where = array('name' => 'footer');
				$this->db->update('content',$data_update,$where);

    			// Update Logo Nota
				$config['upload_path'] = './media/images';
				$config['allowed_types'] = '*';
				$field_name = 'logo_nota';
				$this->upload->initialize($config);
				$this->upload->do_upload($field_name);
				$data_file = $this->upload->data();
				$image_lama =  $this->input->post('logo_lama');
				if($data_file['file_name'] != null) {
					$data_update_logo = array('value' => $data_file['file_name']);
					$where = array('name' => 'image_nota');
					$this->db->update('content',$data_update_logo,$where);
					if(is_file('./media/images/'.$image_lama)){
						unlink('./media/images/'.$image_lama);
					}
					$name_image = 	$data_file['file_name'];

	    			// resize
					$config_res['image_library'] = 'gd2';
					$config_res['source_image'] = 'media/images/'.$name_image;
					$config_res['maintain_ratio'] = TRUE;
					$config_res['width'] = 150;
					$config_res['height'] = 100;
					$config_res['new_image'] = 'media/images/'.$name_image;
					$this->image_lib->initialize($config_res);
					$this->image_lib->resize();
				}
			} else if ($tab == 'image') {
    			// value name Image
				$data_update = array('value' => $set_name_image);

				$where = array('name' => 'name_img_setting');

				$this->db->update('content',$data_update,$where);
			} else if ($tab == 'link') {
    			// Link Android
				$data_update = array('value' => $this->input->post('link_android'));
				$where = array('name' => 'link_android');
				$this->db->update('content',$data_update,$where);

    			// Link Blackberry
				$data_update = array('value' => $this->input->post('link_blackberry'));
				$where = array('name' => 'link_blackberry');
				$this->db->update('content',$data_update,$where);
			} else if ($tab == 'aplikasi') {
    			// Update status due date
				$data_update = array('value' => $status_due_date);
				$where = array('name' => 'status_due_date');
				$this->db->update('content',$data_update,$where);

    			// Update due date
				$data_update = array('value' => $set_due_date);
				$where = array('name' => 'due_date_setting');
				$this->db->update('content',$data_update,$where);

    			// Update Jatuh Tempo Rekap & Dropship
				$jatuh_tempo_rekap_dropship = $this->input->post('jatuh_tempo_rekap_dropship');
				$jatuh_tempo_rekap_dropship_tipe = $this->input->post('jatuh_tempo_rekap_dropship_tipe');
				$data_update = array('value' => $jatuh_tempo_rekap_dropship . ' ' . $jatuh_tempo_rekap_dropship_tipe);
				$where = array('name' => 'jatuh_tempo_rekap_dropship');
				$this->db->update('content',$data_update,$where);

    			// Update Tool Tips
				$data_update = array('value' => $set_tool_tips);
				$where = array('name' => 'tool_tips');
				$this->db->update('content',$data_update,$where);

    			// value setting masuk aplikasi
				$data_update = array('value' => $this->input->post('langsung_dashboard'));
				$where = array('name' => 'langsung_dashboard');
				$this->db->update('content',$data_update,$where);

    			// value setting fitur rekap
				$data_update = array('value' => $this->input->post('fitur_rekap'));
				$where = array('name' => 'fitur_rekap');
				$this->db->update('content',$data_update,$where);

    			// Update Status Aplikasi
				$data_update = array('value' => $set_status_aplication);
				$where = array('name' => 'status_aplication');
				$this->db->update('content',$data_update,$where);

    			// Update Message Off
				$data_update = array('value' => $set_message_off);
				$where = array('name' => 'message_off');
				$this->db->update('content',$data_update,$where);

				$data_update = array('value' => $this->input->post('pajak_status'));
				$where = array('name' => 'pajak_status');
				$this->db->update('content',$data_update,$where);
			} else if ($tab == 'ekspedisi') {
    			//PENGATURAN JNE
				$data_update = array('value' => $this->input->post('jne_status'));
				$where = array('name' => 'jne_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN TIKI
				$data_update = array('value' => $this->input->post('tiki_status'));
				$where = array('name' => 'tiki_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN POS
				$data_update = array('value' => $this->input->post('pos_status'));
				$where = array('name' => 'pos_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN WAHANA
				$data_update = array('value' => $this->input->post('wahana_status'));
				$where = array('name' => 'wahana_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN J&T
				$data_update = array('value' => $this->input->post('jnt_status'));
				$where = array('name' => 'jnt_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN SICEPAT
				$data_update = array('value' => $this->input->post('sicepat_status'));
				$where = array('name' => 'sicepat_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN LION
				$data_update = array('value' => $this->input->post('lion_status'));
				$where = array('name' => 'lion_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN NON TARIF
				$data_update = array('value' => $this->input->post('non_tarif_status'));
				$where = array('name' => 'non_tarif_status');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN ORIGIN CITY
				$data_update = array('value' => $this->input->post('origin_city_id'));
				$where = array('name' => 'origin_city_id');
				$this->db->update('content',$data_update,$where);

    			//PENGATURAN ORIGIN CITY NAME
				$data_update = array('value' => $this->input->post('origin_city_name'));
				$where = array('name' => 'origin_city_name');
				$this->db->update('content',$data_update,$where);
			}

			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah di update</div>');

			redirect('administrator/main/edit_info/'.$tab);
		}
	}



	// FUNCTION BARUU //

	function update_status_data_pelanggan($id)
	{
		$this->check_hak_akses('customer');
		$data=$this->main_model->get_detail('customer',array('id' => $id));

		$status=$data['status'];

		if($status == 'Active')

		{

			$data=array('status'=>'Inactive');

			$where=array('id'=>$id);

			$this->main_model->update_status_data_pelanggan('customer',$data,$where);

		}

		else if($status == 'Moderate')

		{

			$data=array('status'=>'Active');

			$where=array('id'=>$id);

			$this->main_model->update_status_data_pelanggan('customer',$data,$where);

		}

		else if($status == 'Inactive')

		{

			$data=array('status'=>'Active');

			$where=array('id'=>$id);

			$this->main_model->update_status_data_pelanggan('customer',$data,$where);

		}

		redirect('administrator/main/customer');

	}

	public function data_refresh_header()

	{

		$data['data_header_order_unpaid'] = $this->main_model->get_list_where('orders',array('order_payment' => 'Unpaid'),null,array('by' => 'id','sorting' => 'DESC'));

		$data['data_header_order_process'] = $this->main_model->get_list_where('orders_item',array('order_status' => 'Keep', 'order_payment' => 'Unpaid'),null,array('by' => 'id','sorting' => 'DESC'));

		$data['data_header_confirmation'] =  $this->main_model->get_list_where('confirmation',array('status' => 'Pending'),null,array('by' => 'id','sorting' => 'DESC'));

		$data['data_header_status'] =  $this->main_model->get_list_where('customer',array('status' => 'Moderate'),null,array('by' => 'id','sorting' => 'DESC'));

		$this->load->view('administrator/includes/header_ajax',$data);

	}

	public function search_id_customer($query) {
		$data = $this->db->select('id,name')
		->like('name', $query)
		->get('customer', 25, 0)->result();

		$arr = array();
		foreach($data as $row) {
			$id = $row->id;
			$name = $row->name;
			$val = $name . ' ('.$id.')';
			$arr['query'] = $query;
			$arr['suggestions'][] = array(
				'value'	=> $val,
				'data'	=> $row->id
			);
		}
		echo json_encode($arr);
	}



	public function search_nama_tamu($query)

	{

		// cari di database

		$this->db->distinct();

		$this->db->select('customer_id,name_customer');

		$this->db->from('orders');

		$this->db->like('name_customer',$query);

		$this->db->where('order_payment','Paid');

		$this->db->where('order_status','Dropship');

		$data = $this->db->get();

		$arr=array();

		// format keluaran di dalam array

		foreach($data->result() as $row)

		{

			$id = 'Guest';

			$name = $row->name_customer;

			$val = $name." (".$id.")";

			$arr['query'] = $query;

			$arr['suggestions'][] = array(

				'value'	=>$val,

				'data'	=>$id

			);
		}


		// minimal PHP 5.2

		echo json_encode($arr);
	}



	function search_product_session()

	{

		if($this->input->post('prod_id') != '')
		{
			$prod_id = $this->input->post('prod_id');
		}
		else
		{
			$prod_id = $this->session->userdata('prod_id');
		}

		if($this->input->post('view_pages') != '')
		{
			if ($this->input->post('view_pages') != 'all') {
				$perpage = $this->input->post('view_pages');
			}else{

				$perpage = 1000;
			}
		} else {

			$perpage = 10;
		}

		$data_session = array( 'prod_id' => $prod_id, 'perpage' => $perpage);

		$this->session->set_userdata($data_session);

		redirect('administrator/main/search_product_stock');

	}



	function search_product_stock($offset = 0) {
		$data['output'] = null;
		$int_value = $this->session->userdata('prod_id');

		$this->db->select('*');
		$this->db->join('product', 'product.id = product_variant.prod_id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('product_variant.prod_id', $int_value);

		$data_total = $this->db->get('product_variant');
		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}
		$this->load->library('pagination');

		$config = array(
			'base_url'        => base_url().'administrator/main/search_product_stock',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$search_name = $this->main_model->get_detail('product_category',array('id'=> $int_value));
		$data['placeholder_cat'] = $search_name;

		$this->db->join('product_variant', 'prod_id = product.id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('product_variant.prod_id', $int_value);
		$data['list_stock'] = $this->main_model->get_list('product',  array('perpage' => $perpage, 'offset' => $offset),array('by' => 'product.id', 'sorting' => 'DESC'));

		$this->load->view('administrator/page_search_stock', $data);
	}

	function search_category_session() {
		if ($this->input->post('cat_id')) {
			$category = $this->input->post('cat_id');
		}

		if ($this->input->post('view_pages')) {
			if ($this->input->post('view_pages') != 'all') {
				$perpage = $this->input->post('view_pages');
			} else {
				$perpage = 1000;
			}
		} else {
			$perpage = 10;
		}

		$data_session = array('cat_pro' => $category, 'perpage' => $perpage);
		$this->session->set_userdata($data_session);
		redirect('administrator/main/search_category_stock');
	}

	function search_category_stock($offset = 0) {
		$data['output'] = null;
		$int_value = $this->session->userdata('cat_pro');
		$this->db->select('*');
		$this->db->join('product', 'product.id = product_variant.prod_id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('category_id', $int_value);
		$data_total = $this->db->get('product_variant');
		if ($this->session->userdata('perpage')) {
			$perpage = $this->session->userdata('perpage');
		} else {
			$perpage = 10;
		}
		$this->load->library('pagination');

		$config = array(
			'base_url'        => base_url().'administrator/main/search_category_stock',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$search_name = $this->main_model->get_detail('product_category',array('id'=> $int_value));
		$data['placeholder_cat'] = $search_name;

		$this->db->join('product_variant', 'prod_id = product.id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('category_id', $int_value);
		$data['list_stock'] = $this->main_model->get_list('product',  array('perpage' => $perpage, 'offset' => $offset),array('by' => 'product.id', 'sorting' => 'DESC'));
		$this->load->view('administrator/page_search_stock', $data);
	}


	function search_product_last_order_session()

	{

		if($this->input->post('name_product') != '')
		{
			$name_product = $this->input->post('name_product');
		}

		$data_session = array( 'name_product' => $name_product);
		$this->session->set_userdata($data_session);
		redirect('administrator/main/product_last_order_result');

	}

	function product_last_order_result($offset = 0){

		$data['output'] = null;
		$int_value = $this->session->userdata('name_product');
		$this->db->select('*');
		$this->db->join('orders_item', 'orders_item.prod_id = product.id ');
		$this->db->like('name_item',$int_value);
		$this->db->where('order_id', 0);
		$this->db->where('order_payment','Unpaid');

		$this->db->where('order_status','Keep');
		$this->db->group_by("prod_id");
		$data_total = $this->db->get('product');

		$perpage = 20;

		$this->load->library('pagination');

		$config = array (

			'base_url' => base_url().'administrator/main/product_last_order_result',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'uri_segment' => 4,

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		$this->db->select('*');
		$this->db->join('orders_item', 'orders_item.prod_id = product.id ');
		$this->db->like('name_item',$int_value);
		$this->db->where('order_id', 0);

		$this->db->where('order_payment','Unpaid');

		$this->db->where('order_status','Keep');
		$this->db->group_by("prod_id");
		$data['list_product'] = $this->db->get('product',$perpage,$offset);

		$data['output'] = null;

		$data['keyword'] = $int_value;

		$this->load->view('administrator/page_product_last_order',$data);

	}


	public function search_product_id($query)
	{


		// cari di database

		$this->db->select('id,name_item');

		$this->db->from('product');

		$this->db->like('name_item',$query);

		$this->db->not_like('status','delete');

		$data = $this->db->get();

		$arr=array();

		// format keluaran di dalam array

		foreach($data->result() as $row)

		{

			$arr['query'] = $query;

			$arr['suggestions'][] = array(

				'value'	=>$row->name_item,

				'data'	=>$row->id

			);

		}

		// minimal PHP 5.2

		echo json_encode($arr);

	}


	public function search_category_id($query)

	{

		// cari di database

		$this->db->select('id,name');

		$this->db->from('product_category');

		$this->db->like('name',$query);

		$this->db->where('status_category','publish');
		$data = $this->db->get();

		$arr=array();

		// format keluaran di dalam array

		foreach($data->result() as $row)

		{

			$arr['query'] = $query;

			$arr['suggestions'][] = array(

				'value'	=>$row->name,

				'data'	=>$row->id

			);

		}

		// minimal PHP 5.2
		echo json_encode($arr);
	}



	public function search_id_produk_item($query) {
		// cari di database

		$this->db->select('id,name_item');

		$this->db->from('product');

		$this->db->like('name_item',$query);

		$this->db->not_like('status','Delete');

		$data = $this->db->get();

		$arr=array();

		// format keluaran di dalam array

		foreach($data->result() as $row)
		{

			$arr['query'] = $query;
			$arr['suggestions'][] = array(

				'value'	=>$row->name_item,

				'data'	=>$row->id
			);
		}

		// minimal PHP 5.2

		echo json_encode($arr);

	}

	function master_data_session()
	{

		if($this->input->post('customer_name') != null){

			$cust_name = $this->input->post('customer_name');
		} else {

			$cust_name = null;
		}

		if($cust_name != '' )
		{
			$name = $this->input->post('customer_id');
		} else {

			$name = 0;
		}


		if($this->input->post('status_pembayaran') != null)
		{

			$bayar = $this->input->post('status_pembayaran');
		} else {

			$bayar =null;
		}

		if($this->input->post('status_pesanan') != null)
		{

			$pesan = $this->input->post('status_pesanan');

		} else {

			$pesan = null;
		}

		if($this->input->post('product_name') != null)
		{

			$pro_name = $this->input->post('product_name');
		} else {

			$pro_name = null;
		}

		if($pro_name != '')
		{

			$pro = $this->input->post('product_id');
		} else {

			$pro = null;
		}

		if($this->input->post('cari') != null)
		{

			$cari = $this->input->post('cari');
		} else {

			$cari = null;
		}

		if($this->input->post('radio_customer') != null)
		{

			$radio = $this->input->post('radio_customer');
		} else {

			$radio = null;
		}

		if($this->input->post('date_awal') != null)
		{

			$date_awal = $this->input->post('date_awal');
		} else {

			$date_awal = null;
		}

		if($this->input->post('date_akhir') != null)
		{

			$date_akhir = $this->input->post('date_akhir');
		} else {

			$date_akhir = null;
		}

		if($this->input->post('tampilan_halaman') != '')
		{

			if($this->input->post('tampilan_halaman') == '50')
			{
				$perpage = 50;
			}

			if($this->input->post('tampilan_halaman') == '250')
			{
				$perpage = 250;
			}

			if($this->input->post('tampilan_halaman') == '500')
			{
				$perpage = 500;
			}

			if($this->input->post('tampilan_halaman') == 'all')
			{
				$perpage = 1000;
			}

		} else {

			$perpage = 50;

		}

		$data_session = array(

			'name' => $name,

			'cust_name' => $cust_name,

			'product' => $pro,

			'bayar' => $bayar,

			'pesan' => $pesan,

			'cari' => $cari,

			'radio' => $radio,

			'date_awal' => $date_awal,

			'date_akhir' => $date_akhir,

			'product_name' => $pro_name,

			'perpage' => $perpage

		);


		$this->session->set_userdata($data_session);

		redirect('administrator/main/master_data_search');

	}

	function master_data_pesanan($offset=0)

	{

		$data['customer'] = $this->main_model->get_list_where('customer',array('status' => 'Active'));

		$data['product'] = $this->main_model->get_list_where('product',array('status' => 'Publish'));

		$data_session = array(

			'name' => null,

			'cust_name' => null,

			'product' => null,

			'bayar' => null,

			'pesan' => null,

			'cari' => null,

			'radio' => null,

			'date_awal' => null,

			'date_akhir' => null,

			'product_name' => null,

			'perpage' => null

		);

		$this->session->set_userdata($data_session);

		$data['output'] = null;
		$this->db->where('order_status !=', 'Cancel');
		$data['orders_item'] = $this->main_model->get_list('orders_item',null,array('by' => 'order_datetime','sorting' => 'DESC'));

		$data_total = $data['orders_item'];

		$perpage = 50;

		$this->load->library('pagination');

		$config = array (

			'base_url' => base_url().'administrator/main/master_data_pesanan',

			'per_page' => $perpage,

			'total_rows' => $data_total->num_rows(),

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

			'uri_segment' => 4

		);


		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;
		$this->db->where('order_status !=', 'Cancel');
		$data['orders_item'] = $this->main_model->get_list('orders_item',array('perpage' => $perpage,'offset' => $offset),array('by' => 'order_datetime','sorting' => 'DESC'));

		$this->load->view('administrator/page_master_data_pesanan',$data);
	}



	function master_data_search($offset=0)

	{
		$this->check_hak_akses('master_data_pesanan');
		$data['customer'] = $this->main_model->get_list_where('customer',array('status' => 'Active'));

		$data['product'] = $this->main_model->get_list_where('product',array('status' => 'Publish'));

		if($this->session->userdata('cari') == 'cari' )
		{

			if($this->session->userdata('pesan')!="all")
			{
				$this->db->where('order_status',$this->session->userdata('pesan'));
			}

			if($this->session->userdata('name') != null)
			{

				$this->db->where('customer_id',$this->session->userdata('name'));
			}

			if($this->session->userdata('radio') == 'tamu')
			{

				$this->db->where('customer_id','0');
			}

			if($this->session->userdata('radio') == 'customer' and $this->session->userdata('name') == 0)
			{

				$this->db->where('customer_id !=','0');
			}


			if($this->session->userdata('bayar') != "all")
			{

				$this->db->where('order_payment',$this->session->userdata('bayar'));
			}

			if($this->session->userdata('product') != null)
			{

				$this->db->where('prod_id',$this->session->userdata('product'));
			}

			if($this->session->userdata('date_awal')!=null && $this->session->userdata('date_akhir')!=null)
			{

				$date_awal1 = $this->session->userdata('date_awal')." 00:00:01";

				$date_akhir1 = $this->session->userdata('date_akhir')." 23:59:59";

				$this->db->where('order_datetime >=',$date_awal1);

				$this->db->where('order_datetime <=',$date_akhir1);
			}


			$this->db->where('order_status !=', 'Cancel');
			$data['orders_item'] = $this->db->get('orders_item');

			$data_total = $data['orders_item'];

			$perpage = $this->session->userdata('perpage');

			$this->load->library('pagination');

			$config = array (

				'base_url' => base_url().'administrator/main/master_data_search',

				'per_page' => $perpage,

				'total_rows' => $data_total->num_rows(),

				'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

				'full_tag_close' => '</div></div>',

				'num_tag_open' => '<li>',

				'num_tag_close' => '</li>',

				'prev_tag_open' => '<li>',

				'prev_tag_close' => '</li>',

				'next_tag_open' => '<li>',

				'next_tag_close' => '</li>',

				'last_tag_open' => '<li>',

				'last_tag_close' => '</li>',

				'first_tag_open' => '<li>',

				'first_tag_close' => '</li>',

				'cur_tag_open' => '<li class="active"><a href="#">',

				'cur_tag_close' => '</a></li>',

				'uri_segment' => 4

			);

			$this->pagination->initialize($config);

			$data['offset'] = $offset;

			$data['perpage'] = $perpage;

			if($this->session->userdata('pesan')!="all")
			{

				$this->db->where('order_status',$this->session->userdata('pesan'));
			}

			if($this->session->userdata('name') != null)
			{

				$this->db->where('customer_id',$this->session->userdata('name'));
			}

			if($this->session->userdata('radio') == 'tamu')
			{

				$this->db->where('customer_id','0');
			}

			if($this->session->userdata('radio') == 'customer' and $this->session->userdata('name') == 0)
			{

				$this->db->where('customer_id !=','0');
			}

			if($this->session->userdata('bayar') != "all")
			{

				$this->db->where('order_payment',$this->session->userdata('bayar'));
			}


			if($this->session->userdata('product') != null)
			{

				$this->db->where('prod_id',$this->session->userdata('product'));
			}

			if($this->session->userdata('date_awal')!=null && $this->session->userdata('date_akhir')!=null)
			{

				$date_awal1 = $this->session->userdata('date_awal')." 00:00:01";

				$date_akhir1 = $this->session->userdata('date_akhir')." 23:59:59";

				$this->db->where('order_datetime >=',$date_awal1);

				$this->db->where('order_datetime <=',$date_akhir1);
			}
			$this->db->where('order_status !=', 'Cancel');
			$data['orders_item'] = $this->db->get('orders_item',$perpage,$offset);

			$data['output'] = null;

			$data['arr'] = array(

				'customer_name' =>$this->session->userdata('cust_name'),

				'customer_id' =>$this->session->userdata('name'),

				'product_id' =>$this->session->userdata('product'),

				'radio_customer' =>$this->session->userdata('radio'),

				'product_name' =>$this->session->userdata('product_name'),

				'status_pembayaran' =>$this->session->userdata('bayar'),

				'status_pesanan' =>$this->session->userdata('pesan'),

				'date_awal' =>$this->session->userdata('date_awal'),

				'date_akhir' =>$this->session->userdata('date_akhir'),

				'perpage' => $perpage,

				'offset' => $offset,

			);

			$this->load->view('administrator/page_master_data_pesanan',$data);
		}
	}


	function get_version()
	{

		$data_version_now = $this->main_model->get_list('data_version_update',array('perpage' => 1,'offset' => 0),array('by' => 'id','sorting' => 'DESC'));
		$data_version = $data_version_now->row_array();

		$version_now = array(

			'id' => $data_version['id'],

			'version_now' => $data_version['name_version'],

			'version_number' => $data_version['number_version'],

			'date' => $data_version['date']

		);

		echo json_encode($version_now);

	}

	function summary_report_customer($customer_id = null,$status = null,$order_payment = null,$offset=0)
	{
		// $this->check_hak_akses('Administrator','Staf_admin');

		$data['output'] = null;

		$data['customer_id'] = $customer_id;

		$data_order_keep_unpaid = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Unpaid'));

		$qty_order_keep_unpaid = 0;

		foreach($data_order_keep_unpaid->result() as $items):

			$qty_order_keep_unpaid = $qty_order_keep_unpaid + $items->qty;

		endforeach;

		$data['order_keep_unpaid'] = $qty_order_keep_unpaid;


		$data_order_dropship_unpaid = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Dropship','order_payment' => 'Unpaid'));

		$qty_order_dropship_unpaid = 0;

		foreach($data_order_dropship_unpaid->result() as $items):

			$qty_order_dropship_unpaid = $qty_order_dropship_unpaid + $items->qty;

		endforeach;

		$data['order_dropship_unpaid'] = $qty_order_dropship_unpaid;

		$data['order_unpaid'] = $qty_order_dropship_unpaid + $qty_order_keep_unpaid;


		$data_order_keep_paid = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Paid'));

		$qty_order_keep_paid = 0;

		foreach($data_order_keep_paid->result() as $items):

			$qty_order_keep_paid = $qty_order_keep_paid + $items->qty;

		endforeach;

		$data['order_keep_paid'] = $qty_order_keep_paid;


		$data_order_dropship_paid = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Dropship','order_payment' => 'Paid'));

		$qty_order_dropship_paid = 0;

		foreach($data_order_dropship_paid->result() as $items):

			$qty_order_dropship_paid = $qty_order_dropship_paid + $items->qty;

		endforeach;

		$data['order_dropship_paid'] = $qty_order_dropship_paid;

		$data['order_paid'] = $qty_order_dropship_paid + $qty_order_keep_paid;
		$data['total_pesanan'] = $data['order_paid'] + $data['order_unpaid'];

		if($status == "keep")

		{

			if ($order_payment == "unpaid") {

				$data_total = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Unpaid'));

				$perpage = 25;

				$this->load->library('pagination');

				$config = array (

					'base_url' => base_url().'administrator/main/summary_report_customer/'.$customer_id.'/'.$status.'/'.$order_payment,

					'per_page' => $perpage,

					'total_rows' => $data_total->num_rows(),

					'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

					'full_tag_close' => '</div></div>',

					'num_tag_open' => '<li>',

					'num_tag_close' => '</li>',

					'prev_tag_open' => '<li>',

					'prev_tag_close' => '</li>',

					'next_tag_open' => '<li>',

					'next_tag_close' => '</li>',

					'last_tag_open' => '<li>',

					'last_tag_close' => '</li>',

					'first_tag_open' => '<li>',

					'first_tag_close' => '</li>',

					'cur_tag_open' => '<li class="active"><a href="#">',

					'cur_tag_close' => '</a></li>',

					'uri_segment' => 7

				);



				$this->pagination->initialize($config);

				$data['name_title'] = 'Item Pesanan Belum Lunas (Keep)';

				$data['pesanan'] = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Unpaid'),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'order_datetime','sorting' => 'DESC'));

			}elseif ($order_payment == "paid"){

				$data_total = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Paid'));

				$perpage = 25;

				$this->load->library('pagination');

				$config = array (

					'base_url' => base_url().'administrator/main/summary_report_customer/'.$customer_id.'/'.$status.'/'.$order_payment,

					'per_page' => $perpage,

					'total_rows' => $data_total->num_rows(),

					'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

					'full_tag_close' => '</div></div>',

					'num_tag_open' => '<li>',

					'num_tag_close' => '</li>',

					'prev_tag_open' => '<li>',

					'prev_tag_close' => '</li>',

					'next_tag_open' => '<li>',

					'next_tag_close' => '</li>',

					'last_tag_open' => '<li>',

					'last_tag_close' => '</li>',

					'first_tag_open' => '<li>',

					'first_tag_close' => '</li>',

					'cur_tag_open' => '<li class="active"><a href="#">',

					'cur_tag_close' => '</a></li>',

					'uri_segment' => 7

				);



				$this->pagination->initialize($config);

				$data['name_title'] = 'Item Pesanan Lunas (Keep)';

				$data['pesanan'] = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Keep','order_payment' => 'Paid'),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'order_datetime','sorting' => 'DESC'));
			}

			$data['offset'] = $offset;
			$data['perpage'] = $perpage;
			$this->load->view('administrator/page_summary_report_customer',$data);

		}

		elseif($status == "dropship")

		{
			if ($order_payment == "unpaid") {

				$data_total = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Dropship','order_payment' => 'Unpaid'));

				$perpage = 25;

				$this->load->library('pagination');

				$config = array (

					'base_url' => base_url().'administrator/main/summary_report_customer/'.$customer_id.'/'.$status.'/'.$order_payment,

					'per_page' => $perpage,

					'total_rows' => $data_total->num_rows(),

					'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

					'full_tag_close' => '</div></div>',

					'num_tag_open' => '<li>',

					'num_tag_close' => '</li>',

					'prev_tag_open' => '<li>',

					'prev_tag_close' => '</li>',

					'next_tag_open' => '<li>',

					'next_tag_close' => '</li>',

					'last_tag_open' => '<li>',

					'last_tag_close' => '</li>',

					'first_tag_open' => '<li>',

					'first_tag_close' => '</li>',

					'cur_tag_open' => '<li class="active"><a href="#">',

					'cur_tag_close' => '</a></li>',

					'uri_segment' => 7

				);



				$this->pagination->initialize($config);

				$data['name_title'] = 'Item Pesanan Belum Lunas (Dropship)';

				$data['pesanan'] = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Dropship','order_payment' => 'Unpaid'),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'order_datetime','sorting' => 'DESC'));

			}elseif ($order_payment == "paid"){

				$data_total = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Dropship','order_payment' => 'Paid'));

				$perpage = 25;

				$this->load->library('pagination');

				$config = array (

					'base_url' => base_url().'administrator/main/summary_report_customer/'.$customer_id.'/'.$status.'/'.$order_payment,

					'per_page' => $perpage,

					'total_rows' => $data_total->num_rows(),

					'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

					'full_tag_close' => '</div></div>',

					'num_tag_open' => '<li>',

					'num_tag_close' => '</li>',

					'prev_tag_open' => '<li>',

					'prev_tag_close' => '</li>',

					'next_tag_open' => '<li>',

					'next_tag_close' => '</li>',

					'last_tag_open' => '<li>',

					'last_tag_close' => '</li>',

					'first_tag_open' => '<li>',

					'first_tag_close' => '</li>',

					'cur_tag_open' => '<li class="active"><a href="#">',

					'cur_tag_close' => '</a></li>',

					'uri_segment' => 7

				);



				$this->pagination->initialize($config);

				$data['name_title'] = 'Item Pesanan Lunas (Dropship)';

				$data['pesanan'] = $this->main_model->get_list_where('orders_item',array('customer_id' => $customer_id,'order_status' => 'Dropship','order_payment' => 'Paid'),array('perpage' => $perpage, 'offset' => $offset),array('by' => 'order_datetime','sorting' => 'DESC'));
			}


			$data['offset'] = $offset;

			$data['perpage'] = $perpage;

			$this->load->view('administrator/page_summary_report_customer',$data);

		}

		else

		{

			$data['pesanan']= null;

			$data['arr']= array(

				'offset'=>$offset

			);

			$this->load->view('administrator/page_summary_report_customer',$data);

		}

	}

	function info_paket()

	{

		$data['output'] = null;

		$data['name_toko'] = $this->config->item('tokomobile_online_shop');

		$data['domain'] = $this->config->item('tokomobile_domain');

		$data['expired_date'] = $this->config->item('tokomobile_experied_date');

		$data['package'] = $this->config->item('tokomobile_package');

		$data['total_max_product'] = $this->total_max_product;

		$data['total_publish_product'] = $this->total_publish_product;

		$data['total_available_space_product'] = $this->total_available_space_product;

		$data['total_max_customer'] = $this->total_max_customer;

		$data['total_customer'] = $this->total_customer;

		$data['total_available_space_customer'] = $this->total_available_space_customer;

		$data['paket'] = $this->main_model->get_detail('data_license');

		$this->load->view('administrator/page_paket',$data);
	}


	function update_status_customer_all()
	{
		$this->check_hak_akses('customer');
		$status = $this->input->post('status');

		if($status == 'ACTIVE ALL')

		{

			$active = $this->main_model->get_list_where('customer',array('status !=' => 'Active'));

			if($active->result() != null)

			{

				foreach($active->result() as $update):

					$data_update = array('status'=>'Active');

					$where = array('id' => $update->id);

					$this->db->update('customer',$data_update,$where);

				endforeach;

				$this->session->set_flashdata('message','<div class="alert alert-success">Data status pelanggan berhasil diubah menjadi active</div>');

				redirect('administrator/main/customer');

			} else {

				$this->session->set_flashdata('message','<div class="alert alert-danger">Data status pelanggan tidak berhasil diubah,semua pelanggan sudah dalam status active</div>');

				redirect('administrator/main/customer');

			}

		}

		if($status == 'INACTIVE ALL')

		{

			$active = $this->main_model->get_list_where('customer',array('status !=' => 'Inactive'));

			if($active->result() != null)

			{

				foreach($active->result() as $update):

					$data_update = array('status'=>'Inactive');

					$where = array('id' => $update->id);

					$this->db->update('customer',$data_update,$where);

				endforeach;

				$this->session->set_flashdata('message','<div class="alert alert-success">Data status pelanggan berhasil diubah menjadi Inactive</div>');

				redirect('administrator/main/customer');

			} else {

				$this->session->set_flashdata('message','<div class="alert alert-danger">Data status pelanggan tidak berhasil diubah,semua pelanggan sudah dalam status Inactive</div>');

				redirect('administrator/main/customer');

			}

		}

		if($status == 'MODERATE ALL')

		{

			$active = $this->main_model->get_list_where('customer',array('status !=' => 'Moderate'));

			if($active->result() != null)

			{

				foreach($active->result() as $update):

					$data_update = array('status'=>'Moderate');

					$where = array('id' => $update->id);

					$this->db->update('customer',$data_update,$where);

				endforeach;

				$this->session->set_flashdata('message','<div class="alert alert-success">Data status pelanggan berhasil diubah menjadi moderate</div>');

				redirect('administrator/main/customer');

			} else {

				$this->session->set_flashdata('message','<div class="alert alert-danger">Data status pelanggan tidak berhasil diubah,semua pelanggan sudah dalam status moderate</div>');

				redirect('administrator/main/customer');

			}

		}

	}

	// END FUNCTION BARUU //


	// MENU KELUAR //
	public function logout()
	{

		$this->load->library('auth');
		$this->auth->logout();
	}


	//-------- CALLBACK OR OTHER FUNCTION BEGIN HERE ------//
	public function get_output($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_crud',$data);
	}


	public function get_dropship_ready($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_dropship_ready',$data);
	}


	public function get_customer_list($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_customer_list',$data);
	}


	public function get_product_list($output = null,$stat = null,$type = null)
	{

		$data['output'] = $output;
		$data['stat'] = $stat;
		$data['type'] = $type;
		$data ['max'] = $this->total_max_product;
		$data['publish'] = $this->total_publish_product ;
		$this->load->view('administrator/page_product_list',$data);
	}


	public function get_product_category($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_product_category',$data);
	}

	public function get_confirm_payment($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_confirm_payment',$data);
	}

	public function get_message_list($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_message_list',$data);
	}


	public function get_methode_pembayaran_list($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_methode_pembayaran',$data);
	}


	public function get_nota_list($output = null)
	{
		$data['output'] = $output;
		$this->load->view('administrator/page_nota_list',$data);
	}

	public function get_jne_list($output = null)
	{

		$data['output'] = $output;
		$this->load->view('administrator/page_jne_list',$data);
	}

	public function thumbnailer($value,$row)
	{
		return "<img src='".base_url()."media/images/medium/".$value."' width='75' />";
	}



	public function callback_customer_name($value, $row)
	{

		$data_customer = $this->main_model->get_detail('customer',array('id' => $value));
		if ($value != 0) {
			return $data_customer['name']." (".$data_customer['id'].")";
		}

	}


	public function callback_payment_method($value, $row)
	{

		$data_payment_method = $this->main_model->get_detail('payment_method',array('id' => $value));
		if ($value != 0) {
			return $data_payment_method['name'];
		}

	}



	public function callback_customer_dan_tamu($value, $row)
	{

		$data_customer = $this->main_model->get_detail('customer',array('id' => $value));

		if ($value != 0) {
			return $data_customer['name']." (".$data_customer['id'].")";
		} else {

			$data_tamu = $this->main_model->get_detail('orders_item',array('id' => $row->id));
			$tamu_name = $this->main_model->get_detail('orders',array('id' => $data_tamu['order_id']));
			return "<span style=\"color:#e23427;\"><strong>".$tamu_name['name_customer']." (Guest)</strong></span>";

		}
	}



	public function callback_customer_dan_tamu_name($value, $row)
	{

		$data_customer = $this->main_model->get_detail('customer',array('id' => $value));

		if ($value != 0) {

			return $data_customer['name']." (".$data_customer['id'].")";

		} else {

			$tamu_name = $this->main_model->get_detail('orders',array('id' => $row->id));
			return "<span style=\"color:#e23427;\">".$tamu_name['name_customer']." <strong> (Guest)</strong></span>";
		}

	}


	public function callback_variant_name($value, $row)
	{
		$data_variant = $this->main_model->get_detail('product_variant',array('id' => $value));
		return $data_variant['variant'];
	}



	function callback_add_field_img_product()
	{
		return 'Gunakan Button dibawah ini untuk mengunggah Gambar. Note : hindari penggunaan karakter <strong>Titik</strong> ( . ) , <strong>Kurung</strong> () , dan  <strong>Koma</strong> ( , ) pada nama File Gambar Anda, simbol yang kami sarankan antara lain <strong>Add</strong> @ , <strong>Strip</strong> - , <strong>Plus</strong> + , <strong>Underscore</strong> _ , dan <strong>Tilde</strong> ~ .';
	}


	public function encrypt_password($post_array, $primary_key = null)
	{
		$post_array['user_pass'] = $this->encrypt->encode($post_array['user_pass']);
		return $post_array;
	}

	public function decrypt_password($value)
	{

		$decrypted_password = $this->encrypt->decode($value);
		return "<input type='password' name='user_pass' value='$decrypted_password' />";
	}


	public function encrypt_password_customer($post_array, $primary_key = null)
	{
		$post_array['password'] = $this->encrypt->encode($post_array['password']);
		return $post_array;
	}


	public function decrypt_password_customer($value)
	{

		$decrypted_password = $this->encrypt->decode($value);
		return "<input type='text' name='password' value='$decrypted_password' />";
	}


	function insert_date_story($post_array,$primary_key)
	{

		$insert_date = array('datetime' => strtotime('now'));
		$where = array('id' => $primary_key);
		$this->db->update('member_story',$insert_date,$where);
		return true;
	}


	function column_change_datetime($value, $row)
	{

		return date('D, d M Y',$row->datetime);
	}


	function field_change_datetime($value ='', $primary_key = null)
	{

		return date('D, d M Y',$value);
	}

	public function _callback_webpage_url($value, $row)
	{

		return "00".$row->id;
	}

	function view_order($order_id = null)
	{

		if($order_id != null)
		{

			$data['order'] = $this->main_model->get_detail('orders',array('id' => $order_id));
			$data['order_item'] = $this->main_model->get_list_where('rel_prod_orders',array('order_id' => $order_id));
			$data['customer'] = $this->main_model->get_detail('customer',array('id' => $data['order']['customer_id']));
			$data['output'] = null;
			$this->load->view('administrator/view_order_detail',$data);
		}
	}


	function change_status_order($status = null, $order_id = null)
	{

		if( ($status != null) and ($order_id != null) )
		{
			$data_update = array('status' => $status);
			$where = array('id' => $order_id);
			$this->db->update('orders',$data_update,$where);
			$this->session->set_flashdata('message','<div class="alert alert-success">Order status has change "PAID" </div>');
			redirect('administrator/main/view_order/'.$order_id);
		}
	}

	function callback_add_product()
	{

		$return_add = '	<br/>
		<div class="well">
		<table cellpadding="10px" width="500px">
		<thead>
		<tr>
		<td colspan="8">
		<div class="alert alert-warning">
		Field Variant harus dichecked jika produk tidak memiliki variant silahkan diisi <strong>All variant</strong> kemudian checked checkbox yang ada disebelahnya.<br/>
		Karena Akan mempengaruhi stock serta order dari produk anda.
		</div>
		</td>
		</tr>
		<tr>
		<th>Check !</th>
		<th>Variant</th>
		<th>Stock</th>
		</tr>
		</thead>
		<tbody>'
		;


		for($i=0;$i<10;$i++)
		{


			$return_add .= '<tr>

			<td><input type="checkbox" class="form-control check_list" name="check_variant[]" onclick="check_variant()" value="'.$i.'" /></td>

			<td><input type="text" style="width:200px;" class="form-control" id="variant_name_'.$i.'" name="variant_name[]" /></td>

			<td><input type="number" style="width:150px;" class="form-control" id="stock_'.$i.'" name="stock[]" min="0"/></td>
			</tr>';
		}

		$return_add .= '</tbody></table></div>';
		return $return_add;
	}



	function callback_before_add_product($post_array) {

			// Char_replace

		$char = array(',',';','*','!','@','#','$','%','^','&','(',')','+','}','{','[',']','--','---');

		$replace = "-";

		$new_name = str_replace($char,$replace,$post_array['image']);

			// Char_replace name item

		$new_name_item = str_replace('-','_',$post_array['name_item']);

		$post_array['name_item'] = $new_name_item;
		$post_array['image'] = $new_name;
		return $post_array;

	}



	function callback_after_add_product($post_array,$primary_key)
	{


		$check = $post_array['check_variant'];
		$variant = $post_array['variant_name'];
		$stock = $post_array['stock'];

		for($i=0;$i<count($check);$i++)
		{

			$check_id = $check[$i];
			$data_insert = array(

				'prod_id' => $primary_key,
				'variant' => $variant[$check_id],
				'stock' => $stock[$check_id]
			);

			$this->db->insert('product_variant',$data_insert);
		}
		return true;
	}



	function callback_edit_product($value,$primary_key)
	{

		$data_variant = $this->main_model->get_list_where('product_variant',array('prod_id' => $primary_key));
		$return_edit = '

		<br/>
		<div class="well">
		<table cellpadding="10px" width="500px">
		<thead>
		<tr>
		<td colspan="8">
		<div class="alert alert-warning">
		Field Variant harus dichecked jika produk tidak memiliki variant silahkan diisi <strong>All variant</strong> kemudian checked checkbox yang ada disebelahnya.<br/>
		Karena Akan mempengaruhi stock serta order dari produk anda.
		</div>
		</td>
		</tr>


		<tr>
		<th>Check !</th>
		<th>Variant</th>
		<th>Stock</th>
		</tr>
		</thead>
		<tbody>';

		foreach($data_variant->result() as $variant) :

			$return_edit .= '<tr>

			<td><input type="checkbox" class="form-control"  name="update_check_variant['.$variant->id.']" checked="checked" value="'.$variant->id.'"/><input type="hidden" name="update_variant_id[]" value="'.$variant->id.'" /></td>
			<td><input type="text" style="width:200px;" class="form-control" value="'.$variant->variant.'" name="update_variant_name['.$variant->id.']" /></td>
			<td><input type="number" style="width:150px;"class="form-control"  value="'.$variant->stock.'" name="update_stock['.$variant->id.']" min="0"/></td>
			</tr>';
		endforeach;


		$field_nol = 10 - $data_variant->num_rows();

		for($i=0;$i<$field_nol;$i++)
		{

			$return_edit .= '<tr>
			<td><input type="checkbox" class="form-control check_list" name="check_variant[]" onclick="check_variant()" value="'.$i.'"/></td>
			<td><input type="text" style="width:200px;" class="form-control" id="variant_name_'.$i.'" name="variant_name[]" /></td>
			<td><input type="number" style="width:150px;" class="form-control" id="stock_'.$i.'" name="stock[]" min="0"/></td>
			</tr>';
		}

		$return_edit .= '</tbody></table></div>';
		return $return_edit;
	}



	function callback_before_edit_product($post_array)

	{

			// Char_replace image

		$char = array(',',';','*','!','@','#','$','%','^','&','(',')','+','}','{','[',']','--','---');

		$replace = "-";

		$new_image = str_replace($char,$replace,$post_array['image']);

		$new_image = str_replace('-.','.',$new_image);


			// Check data image OLD

		$data_old = $this->main_model->get_detail('product',array('id' => $post_array['id']));

		if($data_old['image'] != $post_array['image'])
		{

			$post_array['image'] = $new_image;
		}


			// Char_replace name item

		$new_name_item = str_replace('-','_',$post_array['name_item']);

		$post_array['name_item'] = $new_name_item;

		return $post_array;

	}



	function callback_after_edit_product($post_array,$primary_key)
	{


		// Parameter Edit
		$update_variant_id = $post_array['update_variant_id'];
		$update_check= $post_array['update_check_variant'];
		$update_variant = $post_array['update_variant_name'];
		$update_stock = $post_array['update_stock'];

		// Parameter Add
		$check = $post_array['check_variant'];
		$variant = $post_array['variant_name'];
		$stock = $post_array['stock'];

		// Update or Delete
		for($i=0;$i<count($update_variant_id);$i++)
		{

			$update_check_id = $update_variant_id[$i];
			$where = array('id' => $update_check_id);

			if($update_check[$update_check_id])
			{

				$data_update = array(

					'prod_id' => $primary_key,

					'variant' => $update_variant[$update_check_id],
					'stock' => $update_stock[$update_check_id],
				);

				$this->db->update('product_variant',$data_update,$where);

			} else {

				$this->db->delete('product_variant',$where);
			}
		}

		// Add
		for($i=0;$i<count($check);$i++)
		{

			$check_id = $check[$i];
			$data_insert = array(

				'prod_id' => $primary_key,
				'variant' => $variant[$check_id],
				'stock' => $stock[$check_id],
								//'category_id' => $category
			);

			$this->db->insert('product_variant',$data_insert);
		}
		return true;
	}



	public function callback_delete_product($primary_key)
	{

		// Delete image product
		$data_product = $this->main_model->get_detail('product',array('id' => $primary_key));

		unlink(PUBPATH.'media/images/original/'.$data_product['image']);

		unlink(PUBPATH.'media/images/thumb/'.$data_product['image']);

		unlink(PUBPATH.'media/images/medium/'.$data_product['image']);

		unlink(PUBPATH.'media/images/large/'.$data_product['image']);

		$this->db->delete('product_tags', array('product_id' => $primary_key));

		$this->db->query('ALTER TABLE product_tags AUTO_INCREMENT = 0');

		return $this->db->update('product',array('status' => 'Delete'),array('id' => $primary_key));

	}



	public function callback_delete_customer($primary_key)
	{

		// Delete all order of this member

		$this->db->delete('orders',array('customer_id' => $primary_key));

		$this->db->delete('orders_item',array('customer_id' => $primary_key));

		$this->db->delete('message',array('customer_id' => $primary_key));

		$this->db->delete('confirmation',array('customer_id' => $primary_key));

		$this->db->delete('customer',array('id' => $primary_key));
		return TRUE;

	}

	public function customer_id($value, $row)
	{

		if($row->id < 10)
		{

			$customer_id = "000".$row->id;
		} elseif($row->id < 100) {

			$customer_id = "00".$row->id;
		} elseif($row->id < 1000) {

			$customer_id = "0".$row->id;
		} else {

			$customer_id = $row->id;
		}

		return $customer_id;
	}


	function image_callback_after_upload($uploader_response,$field_info, $files_to_upload)
	{



        //Is only one file uploaded so it ok to use it with $uploader_response[0].
		$file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name;
		$thumbnail = $field_info->upload_path.'/thumb/'.$uploader_response[0]->name;
		$new_name= $uploader_response[0]->name;


		// Thumbs
		$config_res['image_library'] = 'gd2';
		$config_res['source_image'] = 'media/images/original/'.$uploader_response[0]->name;
		$config_res['maintain_ratio'] = FALSE;
		$config_res['width'] = 32;
		$config_res['height'] = 32;
		$config_res['new_image'] = 'media/images/thumb/'.$new_name;
		$this->image_lib->initialize($config_res);
		$this->image_lib->resize();



		// medium
		$config_res['image_library'] = 'gd2';
		$config_res['source_image'] = 'media/images/original/'.$uploader_response[0]->name;
		$config_res['maintain_ratio'] = TRUE;
		$config_res['width'] = 320;
		$config_res['height'] = 320;
		$config_res['new_image'] = 'media/images/medium/'.$new_name;

		$this->image_lib->initialize($config_res);
		$this->image_lib->resize();


		// large
		$config_res['image_library'] = 'gd2';
		$config_res['source_image'] = 'media/images/original/'.$uploader_response[0]->name;
		$config_res['maintain_ratio'] = TRUE;
		$config_res['width'] = 640;
		$config_res['height'] = 640;
		$config_res['new_image'] = 'media/images/large/'.$new_name;

		$this->image_lib->initialize($config_res);
		$this->image_lib->resize();


		// original
		$config_res['image_library'] = 'gd2';
		$config_res['source_image'] = 'media/images/original/'.$uploader_response[0]->name;
		$config_res['maintain_ratio'] = TRUE;
		$config_res['width'] = 1024;
		$config_res['height'] = 1024;
		$config_res['new_image'] = 'media/images/original/'.$new_name;

		$this->image_lib->initialize($config_res);
		$this->image_lib->resize();

		return true;
	}


	//--- END OF CALLBACK --//

	// TAMBAHAN CALLBACK //

	public function callback_customer_status($value, $row)
	{

		if($row->status == "Moderate"){

			if($this->total_available_space_customer != 0 || $this->total_max_customer == 'Unlimited')  {

				return "<a href='".base_url()."administrator/main/update_status_data_pelanggan/".$row->id."' class='btn btn-success btn-sm pelanggan-btn' style='font-size:11px; padding: 3px 10px;'>Active</a>";

			}

		} else if($row->status == "Active") {

			return "<a href='".base_url()."administrator/main/update_status_data_pelanggan/".$row->id."' class='btn btn-danger btn-sm pelanggan-btn' style='font-size:11px; padding: 3px 10px;'>Inactive</a>";

		} else {

			if($this->total_available_space_customer != 0 || $this->total_max_customer == 'Unlimited')  {

				return "<a href='".base_url()."administrator/main/update_status_data_pelanggan/".$row->id."' class='btn btn-success btn-sm pelanggan-btn' style='font-size:11px; padding: 3px 10px;'>Active</a>";

			}
		}
	}


	function image_callback_before_upload($files_to_upload,$field_info)
	{

		foreach($files_to_upload as $value);

		$name = $value['name'];

		if (!preg_match("/^[\w &.@~|\-]+$/",$name))
		{
			return "Periksa kembali file upload anda !!";
		} else {
			return true;
		}

	}

	function callback_customer_checked($value, $row)
	{
		return "<input type='checkbox' class='form-control check_list' name='check' value='".$row->id."'/>";
	}



	function order_process_to_paid()
	{

		$list_order = $this->input->post('list_order');
		$list_order = json_decode($list_order);

		if($list_order != null)
		{

			foreach($list_order as $order_id):

				$data_update = array('order_payment' => 'Paid');
				$where = array('id' => $order_id);
				$this->db->update('orders',$data_update,$where);

				$this->db->select('id, added_point, customer_id, subtotal, diskon, point');
				$order = $this->main_model->get_detail('orders', array('id' => $order_id));
				if ($order['added_point'] == 0) {

					$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
					if ($point_reward_status['value'] == 'on') {
						$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
						$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
						$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
						$point_customer = $customer['point'] + $total_point - $order['point'];

						$point_history = array(
							'customer_id' => $order['customer_id'],
							'point_prev'  => $customer['point'],
							'point_in'    => $total_point - $order['point'],
							'point_end'   => $point_customer,
							'order_id'    => $order['id'],
							'note'        => 'Mendapatkan point',
							'user_id'     => $this->session->userdata('webadmin_user_id'),
						);
						$this->db->insert('point_histories', $point_history);

						$this->db->where('id', $order['customer_id'])
						->update('customer', array('point' => $point_customer));
						$this->db->update('orders', array('added_point' => 1, 'get_point' => 1), $where);
					}
				}
				$subject = 'Status Pesanan';
				$content = 'Status pesanan Anda #' . $order_id . ' telah dijadikan lunas';
				$this->sendNotifikasi($data_confirmation['customer_id'], $subject, $content, 'order_status', $order_id);
			endforeach;

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah berhasil diubah</div>');
			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);

		}

	}

	function update_status_publish_to_unpublish()

	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		if($list_status != null)
		{

			foreach($list_status as $status_id):

				$data_update = array('status' => 'Unpublish');
				$where = array('id' => $status_id);
				$this->db->update('product',$data_update,$where);
			endforeach;

			$data_json['pesan'] = 'Success';

			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah berhasil diubah</div>');

			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);
		}

	}

	function update_status_all_delete_product()

	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		foreach($list_status as $status_id):

			$data_update = array('status' => 'Delete');
			$where = array('id' => $status_id);
			$this->db->update('product',$data_update,$where);

			$data_update_variant = array('available' => 'Delete');
			$where_variant = array('prod_id' => $status_id);
			$this->db->update('product_variant',$data_update_variant,$where_variant);

		endforeach;

		$data_json['pesan'] = 'Success';

		$this->session->set_flashdata('message','<div class="alert alert-success">Data yang anda pilih telah berhasil dihapus </div>');

		echo json_encode($data_json);

	}

	function update_status_unpublish_to_publish()
	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		if($list_status != null)
		{

			foreach($list_status as $status_id):
				$data_update = array('status' => 'Publish');
				$where = array('id' => $status_id);
				$this->db->update('product',$data_update,$where);
			endforeach;

			$data_json['pesan'] = 'Success';

			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah berhasil diubah</div>');

			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);

		}

	}

	function list_product_checked()

	{
		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		if($list_status != null)
		{

			foreach($list_status as $status_id):
				$pro_detail = $this->main_model->get_detail('product',array('id' =>$status_id));
				$detail = $pro_detail['product_type'];
				$name_product = $pro_detail['name_item'];
				$id_product = $status_id;
				$data_json['product_list'][]= array(
					'id' => $id_product,
					'name' => $name_product
				);
			endforeach;

			if ($detail == 'Ready Stock') {
				$category = $this->main_model->get_list_where('product_category',array('tipe' => 'PO', 'status_category' => 'Publish'));
			}elseif ($detail == 'PO') {
				$category = $this->main_model->get_list_where('product_category',array('tipe' => 'Ready Stock', 'status_category' => 'Publish'));
			}

			foreach($category->result() as $items):
				$name = $items->name;
				$id_category = $items->id;
				$data_json['category'][]= array(
					'id' => $id_category,
					'name' => $name
				);
			endforeach;
			$data_json['pesan'] = 'Success';
			echo json_encode($data_json);

		}else{
			$data_json['pesan'] = 'false';
			$data_json['alert'] = 'Silahkan Pilih Produk Yang Ingin Anda Ubah Terlebih Dahulu';
			echo json_encode($data_json);
		}
	}

	function update_status_customer_active()

	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		if($list_status != null)
		{

			foreach($list_status as $status_id):

				$data_update = array('status' => 'Active');
				$where = array('id' => $status_id);
				$this->db->update('customer',$data_update,$where);
			endforeach;

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah berhasil diubah</div>');
			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);
		}

	}

	function update_status_customer_inactive()

	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		if($list_status != null)
		{

			foreach($list_status as $status_id):

				$data_update = array('status' => 'Inactive');
				$where = array('id' => $status_id);
				$this->db->update('customer',$data_update,$where);
			endforeach;

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-success">Data telah berhasil diubah</div>');
			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);

		}

	}



	function update_status_customer_delete()

	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);

		if($list_status != null){

			foreach($list_status as $status_id):

				$where = array('id' => $status_id);

				$this->db->delete('customer',$where);
			endforeach;

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Data Berhasil Didelete</div>');
			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);

		}

	}

	function update_status_batal_pesanan()

	{

		$list_status = $this->input->post('order_item_id');
		$this->form_validation->set_rules('reason_cancel', 'Alasan Pembatalan', 'trim|required');
		if (!$this->form_validation->run()) {
			$this->session->set_flashdata('message', 'Alasan harus diisi');
			redirect('administrator/main/last_order_process', 'refresh');
		}
		if($list_status != null)
		{

			foreach($list_status as $status_id):

				$data_order_item = $this->main_model->get_detail('orders_item',array('id' => $status_id));

				// Re-stock
				$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $data_order_item['variant_id']));
				$restock = $data_order_item_product['stock'] + $data_order_item['qty'];
				$data_update = array('stock' => $restock);
				$where_update = array('id' => $data_order_item['variant_id']);
				$this->db->update('product_variant',$data_update,$where_update);

				$stock_histories = array(
					'prod_id'    => $data_order_item['prod_id'],
					'variant_id' => $data_order_item['variant_id'],
					'prev_stock' => $data_order_item_product['stock'],
					'stock'      => $restock,
					'qty'        => $data_order_item['qty'],
					'user_id'    => $this->session->userdata('webadmin_user_id'),
					'created_at' => date('Y-m-d H:i:s'),
					'note'       => $this->input->post('reason_cancel')
				);
				$this->db->insert('stock_histories', $stock_histories);

				$where_delete = array('id' => $status_id);
				$this->db->update('orders_item', array('order_status' => 'Cancel'), $where_delete);
			endforeach;

			$this->session->set_flashdata('message','<div class="alert alert-success">Pesanan telah dibatalkan</div>');
			redirect('administrator/main/last_order_process', 'refresh');

		} else {

			$this->session->set_flashdata('message','<div class="alert alert-danger">Silahkan Pilih data terlebih dahulu</div>');
			redirect('administrator/main/last_order_process', 'refresh');

		}
	}



	function update_status_delete_product()
	{

		$list_status = $this->input->post('list_status');
		$list_status = json_decode($list_status);
		if($list_status != 0)

		{

			foreach($list_status as $status_id):

				$data_update = array('status' => 'Delete');
				$where = array('id' => $status_id);
				$this->db->update('product',$data_update,$where);

				$data_update_variant = array('available' => 'Delete');
				$where_variant = array('prod_id' => $status_id);
				$this->db->update('product_variant',$data_update_variant,$where_variant);

			endforeach;

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Data Berhasil Didelete</div>');
			echo json_encode($data_json);

		} else {

			$data_json['pesan'] = 'Success';
			$this->session->set_flashdata('message','<div class="alert alert-danger">Pilih data terlebih dahulu</div>');
			echo json_encode($data_json);

		}

	}


	/*
	function update_product_type_ready()

	{

		$list_status = $this->input->post('list_status');

		$list_status = json_decode($list_status);

		if($list_status != null)

		{



				foreach($list_status as $status_id):

				$data_update = array('product_type' => 'Ready Stock');



				$where = array('id' => $status_id);



				$this->db->update('product',$data_update,$where);

				endforeach;

				$this->session->set_flashdata('message','<div class="alert alert-success">Product telah diubah ke Product Ready Stock</div>');

				$data_json['pesan'] = 'Success';

				echo json_encode($data_json);



		}

		else

		{

			$data_json['pesan'] = 'Success';

			$this->session->set_flashdata('message','<div class="alert alert-danger">Pilih data terlebih dahulu</div>');

			echo json_encode($data_json);

		}

	}
	*/

	function update_product_type()

	{

		$jmlh = $this->input->post('jmlh');
		$product_type = $this->input->post('product-type');
		$id_product = $this->input->post('id_product');
		$category_id = $this->input->post('category_product');

		for($i=0;$i<count($jmlh);$i++)
		{

			$inst_id = $i;
			$data_var = array(

				'product_type' => $product_type,

				'category_id' => $category_id[$inst_id]

			);

			$where = array('id' => $id_product[$inst_id] );
			$this->db->update('product',$data_var,$where);

		}

		if ($product_type == 'PO') {
			$this->session->set_flashdata('message','<div class="alert alert-success">Product telah diubah ke Product PO</div>');
			redirect('administrator/main/product/Ready_Stock/Publish/');
		}elseif ($product_type == 'Ready Stock') {
			$this->session->set_flashdata('message','<div class="alert alert-success">Product telah diubah ke Product Ready Stock</div>');
			redirect('administrator/main/product/PO/Publish/');
		}

	}



	function get_alamat_pengiriman()

	{

		$customer_id = $this->input->post('cust_id');

		$nama_toko = $this->client_name;

		$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id),array('perpage' => 1,'offset' => 0));
		$data_return['data_customer'] = $data_customer;
		$data_return['data_prov'] = $data_customer['prov_id'];
		$data_return['data_kota'] = $data_customer['kota_id'];
		$data_return['data_kecamatan'] = $data_customer['kecamatan_id'];
		$data_return['nama_toko'] = $nama_toko;

		$data_return['status'] = 'Success';

		echo json_encode($data_return);
	}

    // END TAMBAHAN CALLBACK //

    // Custom
	function check_hak_akses($menu) {
		$user_id = $this->session->userdata('webadmin_user_id');
		$user = $this->db->get_where('users', array('id' => $user_id))->row_array();
		$user_akses_menu = explode(',', $user['akses_menu']);

		if (!in_array($menu, $user_akses_menu)) {
			echo "<script>alert('Akses tidak diijinkan');window.location.href ='".base_url('administrator/main')."';</script>";
		}
	}


	// NEW FUNCTION CUSTOME //

	function resi()
	{

		$crud = new grocery_CRUD();

		$crud->set_table('resi')

		->set_subject('Resi');

		$crud->add_action('Edit', '#', 'administrator/main/resi/edit','btn btn-primary btn-crud');

		$crud->order_by('id','DESC');

		$data['output'] = $crud->render();

		$this->load->view('administrator/page_info_resi',$data);
	}


	function testimonial()
	{

		$crud = new grocery_CRUD();

		$crud->set_table('testimonial')

		->set_subject('Testimonial');

		$crud->add_action('Approve', '#', 'administrator/main/update_testimonial/Approve','btn btn-success btn-crud');

		$crud->add_action('Reject', '#', 'administrator/main/update_testimonial/Reject','btn btn-danger btn-crud');

		$crud->order_by('id','DESC');

		$data['output'] = $crud->render();

		$this->load->view('administrator/page_testimonial',$data);
	}

	function update_testimonial($slug,$id)
	{
		$data_update = array('status' => $slug );
		$where = array('id' => $id);
		$this->db->update('testimonial',$data_update,$where);

		$this->session->set_flashdata('message','<div class="alert alert-success">Status Testimonial telah berhasil diperbaharui </div>');

		redirect('administrator/main/testimonial');


	}

	function home_slideshow()
	{

		$crud = new grocery_CRUD();

		$crud->set_table('slideshow')

		->set_subject('Home slideshow');

		$crud->set_field_upload('image','media/images');

		$crud->unset_texteditor('link');

		$data['output'] = $crud->render();

		$this->load->view('administrator/page_home_slideshow',$data);
	}

	function cara_pesan()
	{

		$crud = new grocery_CRUD();

		$crud->set_table('content')

		->set_subject('Cara Pesan');

		$crud->fields('value');

		$crud->display_as('value','Konten');

		$crud->unset_add();

		$crud->unset_delete();

		$crud->where('name','web_cara_pesan');

		$crud->unset_back_to_list();

		$data['output'] = $crud->render();

		$this->load->view('administrator/page_cara_pesan',$data);
	}
	public function search_id_kecamatan($query)
	{

		// cari di database

		$this->db->select('coding,kecamatan');

		$this->db->from('tarif');

		$this->db->like('kecamatan',$query);

		$data = $this->db->get();

		$arr=array();

		// format keluaran di dalam array

		foreach($data->result() as $row)
		{

			$arr['query'] = $query;
			$arr['suggestions'][] = array(

				'value'	=>$row->kecamatan,

				'data'	=>$row->coding
			);
		}

		// minimal PHP 5.2

		echo json_encode($arr);

	}
	function get_tarif_price()
	{
		$harga_kg = $this->input->post('harga_kg');
		$kecamatan_id = $this->input->post('kecamatan_id');

		$detail = $this->main_model->get_detail('tarif',array('coding' => $kecamatan_id));
		$data_json = array('price' => $detail[''.$harga_kg.'']);

		echo json_encode($data_json);
	}
	function get_tarif_tipe()
	{
		$id_data = $this->input->post('id_data');
		$detail = $this->main_model->get_detail('tarif',array('coding' => $id_data));

		$data_json = array(
			'reg' => $detail['reg'],
			'oke' => $detail['oke'],
			'yes' => $detail['yes'],
		);
		echo json_encode($data_json);
	}

	function content_faq()
	{

		$crud = new grocery_CRUD();

		$crud->set_table('content')

		->set_subject('Content FAQ');

		$crud->fields('value');

		$crud->display_as('value','Konten');

		$crud->unset_add();

		$crud->unset_delete();

		$crud->where('name','faq');

		$crud->unset_back_to_list();

		$data['output'] = $crud->render();

		$this->load->view('administrator/page_faq',$data);
	}

	function add_new_customer()
	{
		$data['output'] = null;
		$this->load->view('administrator/page_add_customer',$data);
	}
	function edit_customer($id = null)
	{
		$data['output'] = null;
		$data['customertype'] = $this->db->order_by('id', 'asc')->get('customer_type')->result();
		$data['customer'] = $this->main_model->get_detail('customer',array('id' => $id));
		$data['provinces'] = $this->db->get('provinces')->result();
		$data['cities'] = $this->db->get_where('cities', array('province_id' => $data['customer']['prov_id']))->result();
		$data['subdistricts'] = $this->db->get_where('subdistricts', array('city_id' => $data['customer']['kota_id']))->result();
		$this->load->view('administrator/page_edit_customer',$data);
	}

	function add_customer_process() {
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$provinsi = $this->input->post('provinsi');
		$kota = $this->input->post('kota');
		$kecamatan = $this->input->post('kecamatan');
		$alamat = $this->input->post('alamat');
		$postcode = $this->input->post('postcode');
		$phone = $this->input->post('phone');
		$pinbb = $this->input->post('pinbb');
		$jenis_customer = $this->input->post('jenis_customer');
		$status = $this->input->post('status');

		$detail = $this->main_model->get_list_where('customer',array('email' => $email));
		if ($detail->num_rows() > 0) {
			$this->session->set_flashdata('message','<div class="alert alert-danger">Email sudah terdaftar pada sistem</div>');

			redirect('administrator/main/add_new_customer');
		} else {
			//$pass = $this->encrypt->encode($password);
			$pass = password_hash($password,PASSWORD_DEFAULT);
			$insert = array(
				'name'           => $name,
				'email'          => $email,
				'password'       => $pass,
				'address'        => $alamat,
				'prov_id'        => $provinsi ? $provinsi : 0,
				'kota_id'        => $kota ? $kota : 0,
				'kecamatan_id'   => $kecamatan ? $kecamatan : 0,
				'postcode'       => $postcode,
				'phone'          => $phone,
				'pin_bb'         => $pinbb,
				'jenis_customer' => $jenis_customer,
				'status'         => $status,
			);
			$this->db->insert('customer',$insert);

			if ($this->input->post('redirect_url') != '') {

				$this->session->set_flashdata('message','<div class="alert alert-success">Customer baru telah berhasil ditambahkan</div>');

				redirect($this->input->post('redirect_url'));
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-success">Customer baru telah berhasil ditambahkan</div>');

				redirect('administrator/main/customer');
			}
		}
	}

	/*function edit_customer_process() {
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$provinsi = $this->input->post('provinsi');
		$kota = $this->input->post('kota');
		$kecamatan = $this->input->post('kecamatan');
		$alamat = $this->input->post('alamat');
		$postcode = $this->input->post('postcode');
		$phone = $this->input->post('phone');
		$pinbb = $this->input->post('pinbb');
		$jenis_customer = $this->input->post('jenis_customer');
		$status = $this->input->post('status');
		$id_data = $this->input->post('id_data');

		$detail = $this->main_model->get_list_where('customer',array('email' => $email));
		$detail_email = $this->main_model->get_detail('customer',array('id' => $id_data));
		if ($detail->num_rows() > 0 and $detail_email['email'] != $email) {
			$this->session->set_flashdata('message','<div class="alert alert-danger">Email sudah terdaftar pada sistem</div>');

			redirect('administrator/main/edit_customer/'.$id_data);
		} else {
			$pass = $this->encrypt->encode($password);
			$update = array(
				'name'           => $name,
				'email'          => $email,
				'password'       => $pass,
				'address'        => $alamat,
				'prov_id'        => $provinsi ? $provinsi : 0,
				'kota_id'        => $kota ? $kota : 0,
				'kecamatan_id'   => $kecamatan ? $kecamatan : 0,
				'postcode'       => $postcode,
				'phone'          => $phone,
				'pin_bb'         => $pinbb,
				'jenis_customer' => $jenis_customer,
				'status'         => $status,
			);
			$where = array('id' => $id_data);
			$this->db->update('customer',$update,$where);

			if ($this->input->post('redirect_url') != '') {
				$this->session->set_flashdata('message','<div class="alert alert-success">Customer telah berhasil diedit</div>');
				redirect($this->input->post('redirect_url'));
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-success">Customer telah berhasil diedit</div>');
				redirect('administrator/main/customer');
			}
		}
	}*/

	function edit_customer_process() {
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		// $password = $this->input->post('password');
		$provinsi = $this->input->post('provinsi');
		$kota = $this->input->post('kota');
		$kecamatan = $this->input->post('kecamatan');
		$alamat = $this->input->post('alamat');
		$postcode = $this->input->post('postcode');
		$phone = $this->input->post('phone');
		$pinbb = $this->input->post('pinbb');
		$jenis_customer = $this->input->post('jenis_customer');
		$status = $this->input->post('status');
		$id_data = $this->input->post('id_data');

		$detail = $this->main_model->get_list_where('customer',array('email' => $email));
		$detail_email = $this->main_model->get_detail('customer',array('id' => $id_data));
		if ($detail->num_rows() > 0 and $detail_email['email'] != $email) {
			$this->session->set_flashdata('message','<div class="alert alert-danger">Email sudah terdaftar pada sistem</div>');

			redirect('administrator/main/edit_customer/'.$id_data);
		} else {
			$update = array(
				'name'           => $name,
				'email'          => $email,
				'address'        => $alamat,
				'prov_id'        => $provinsi ? $provinsi : 0,
				'kota_id'        => $kota ? $kota : 0,
				'kecamatan_id'   => $kecamatan ? $kecamatan : 0,
				'postcode'       => $postcode,
				'phone'          => $phone,
				'pin_bb'         => $pinbb,
				'jenis_customer' => $jenis_customer,
				'status'         => $status,
			);
			if(($this->input->post('password') != null) or ($this->input->post('password') != '') )
			{
				// $data_update['password'] = $this->encrypt->encode($this->input->post('password'));
				$update['password'] = password_hash($this->input->post('password'),PASSWORD_DEFAULT);
			}
			$where = array('id' => $id_data);
			$this->db->update('customer',$update,$where);

			if ($this->input->post('redirect_url') != '') {
				$this->session->set_flashdata('message','<div class="alert alert-success">Customer telah berhasil diedit</div>');
				redirect($this->input->post('redirect_url'));
			} else {
				$this->session->set_flashdata('message','<div class="alert alert-success">Customer telah berhasil diedit</div>');
				redirect('administrator/main/customer');
			}
		}
	}

	public function update_point_customer() {
		$id = $this->input->post('id');
		$point = $this->input->post('point');

		$customer = $this->main_model->get_detail('customer', array('id' => $id));
		$selisih_point = $customer['point'] - $point;

		$point_history = array(
			'customer_id' => $id,
			'point_prev'  => $customer['point'],
			'point_in'    => $selisih_point < 0 ? $selisih_point : 0,
			'point_out'   => $selisih_point > 0 ? $selisih_point : 0,
			'point_end'   => $point,
			'order_id'    => 0,
			'note'        => 'Perubahan point',
			'user_id'     => $this->session->userdata('webadmin_user_id'),
		);
		$this->db->insert('point_histories', $point_history);

		$this->db->where('id', $id)
		->update('customer', array('point' => $point));
		$response = array(
			'status'  => 'Success',
			'message' => 'Point berhasil diubah'
		);
		echo json_encode($response);
	}

	function delete_img_produk(){
		$prod_id = $this->input->post('prod_id');
		if ($prod_id == 0) {
			$uuid = explode('#', $this->input->post('qquuid'));
			//penambahan image
			$image = $uuid[1];
			$rel_image_id = $uuid[0];
			$urutan = $uuid[2];

			if($urutan != 1) {
				$detail = $this->main_model->get_detail('rel_produk_image',array('id' => $rel_image_id));

				$this->db->update('rel_produk_image',array('image' => ''),array('id' => $rel_image_id));
			} else {
				$detail = $this->main_model->get_detail('product',array('id' => $rel_image_id));

				$this->db->update('product',array('image' => ''),array('id' => $rel_image_id));
			}
		} else {
			$filename = $this->input->post('filename');
			$detail = $this->main_model->get_detail('rel_produk_image',array('prod_id' => $prod_id, 'image' => $filename));
			if (count($detail) == 0) {
				$detail = $this->main_model->get_detail('product',array('id' => $prod_id, 'image' => $filename));
				$this->db->update('rel_produk_image',array('image' => ''),array('id' => $prod_id, $image => $filename));
			} else {
				$this->db->update('product',array('image' => ''),array('id' => $prod_id));
			}
		}

		unlink('./media/images/original/'.$detail['image']);

		unlink('./media/images/medium/'.$detail['image']);

		unlink('./media/images/large/'.$detail['image']);

		unlink('./media/images/thumb/'.$detail['image']);

		$data_return['status'] = 'Success';

		echo json_encode($data_return);
	}

	function fcm_push_all($title, $msg, $id_produk) {
		$notif = array(
			'text'         => $msg,
			'title'        => $title,
			'id_produk'    => $id_produk,
			'vibrate'      => 'default',
			'sound'        => 'default',
			'type'         => 'produk',
			'click_action' => 'FCM_PLUGIN_ACTIVITY',
		);

		$headers = array(
			'Authorization: key= ' . $this->config->item('FCM_APIKEY'),
			'Content-Type: application/json'
		);

		$json_fcm = array(
			'to'               => $this->topics,
			'delay_while_idle' => true,
			'notification'     => $notif,
			'data'             => $notif
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_fcm));

		$result = curl_exec($ch );
		curl_close( $ch );
	}

	function fcm_push_single($reg_id, $type, $title, $msg, $id_produk, $id_pesan = null) {
		$notif = array(
			'text'			=> $msg,
			'title'         => $title,
			'id_produk'     => $id_produk,
			'vibrate'   	=> 'default',
			'sound'     	=> 'default',
			'type'			=> $type,
			'id_pesan'		=> $id_pesan,
			'click_action' => 'FCM_PLUGIN_ACTIVITY',
		);

		$headers = array(
			'Authorization: key= ' . $this->config->item('FCM_APIKEY'),
			'Content-Type: application/json'
		);

		$json_fcm = array(
			'registration_ids' => $reg_id,
			'delay_while_idle' => true,
			'notification'     => $notif,
			'data'             => $notif
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_fcm));

		$result = curl_exec($ch );
		curl_close( $ch );
	}

	function chatting($offset = null) {
		$data['output'] = null;

		$data_total = $this->db->select('COUNT(*) AS total')
		->from('chatting')
		->where('chatting.id IN (SELECT MAX(CT.id) FROM chatting CT GROUP BY CT.customer_id)')
		->get()->row_array();

		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url() . 'administrator/main/chatting',
			'per_page'        => $perpage,
			'total_rows'      => $data_total['total'],
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;

		$chats = $this->db->select('A.id, A.tanggal, A.customer_id, B.name, A.status,  (SELECT COUNT(C.id) FROM chatting C WHERE C.sender = "Customer" AND C.status = "Unread" AND C.customer_id = A.customer_id ) AS count_unread')
		->from('chatting A')
		->join('customer B', 'A.customer_id =  B.id', 'left')
		->where('A.id IN (SELECT MAX(CT.id) FROM chatting CT GROUP BY CT.customer_id)')
		->order_by('A.tanggal', 'DESC')
		->limit($perpage)
		->offset($offset)
		->get()->result();
		$data['chats'] = $chats;

		$this->load->view('administrator/page_list_chatting',$data);
	}

	function detail_chat($cust_id) {
		$data['output'] = null;
		$update = array('status' => 'Read');
		$where = array('sender' => 'Customer', 'customer_id' => $cust_id);
		$this->db->update('chatting', $update, $where);

		$this->db->order_by('tanggal', 'desc');
		$data['list_chat'] = $this->main_model->get_list_where('chatting',array('customer_id' => $cust_id,'status !=' => 'Delete'))->result();
		$data['customer'] = $this->main_model->get_detail('customer', array('id' => $cust_id));
		$this->load->view('administrator/page_detail_chat', $data);
	}

	function kirim_chat() {
		$pesan = $this->input->post('pesan');
		$customer = $this->input->post('customer');

		if ($pesan != null) {
			$insert = array(
				'customer_id' => $customer,
				'tanggal'     => date('Y-m-d H:i:s'),
				'pesan'       => $pesan,
				'sender'      => 'Admin',
				'status'      => 'Unread',
			);

			$chats_path = base_url('media/images/chats/');

			if (!file_exists($chats_path)) {
				mkdir('./media/images/chats');
			}

			if ($_FILES['image']['size'] > 0) {
				$config['upload_path'] = './media/images/chats/';
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size']  = '0';
				$config['max_width']  = '0';
				$config['max_height']  = '0';

				$this->upload->initialize($config);

				if ( ! $this->upload->do_upload('image')) {
					$error = $this->upload->display_errors('', '');
					$this->session->set_flashdata('message','<div class="alert alert-danger">'.$error.'</div>');
					redirect('administrator/main/detail_chat/'.$customer);
				} else {
					$data_file = $this->upload->data();
					$insert['image'] = $data_file['file_name'];

					$config_res['image_library'] = 'gd2';
					$config_res['source_image'] = 'media/images/chats/'.$data_file['file_name'];
					$config_res['maintain_ratio'] = FALSE;
					$config_res['quality'] = 70;
					$config_res['new_image'] = 'media/images/chats/'.$data_file['file_name'];
					$this->image_lib->initialize($config_res);
					$this->image_lib->resize();
				}
			}

			$this->db->insert('chatting', $insert);

			$this->session->set_flashdata('message','<div class="alert alert-success">Pesan Telah dikirimkan kepada customer</div>');
			//kirim notif chat ke customer//
			$fcm_customer = $this->db->get_where('t_fcm_customer', array('customer_id' => $customer));
			if ($fcm_customer->num_rows() > 0) {
				foreach ($fcm_customer->result() as $cust) {
					$reg_id = $cust->registration_id;
					array_push($this->registrationIds, $reg_id);
				}
				$title = 'Balasan Chatting Admin';
				$message = $pesan;
				$this->fcm_push_single($this->registrationIds, 'chat', $title, $message, 0);
			}

			redirect('administrator/main/detail_chat/'.$customer);
		} else {
			$this->session->set_flashdata('message','<div class="alert alert-danger">Tidak ada pesan yang dikirim, pastikan anda sudah mengisi kolom pesan</div>');
			redirect('administrator/main/detail_chat/'.$customer);
		}
	}
	function delete_chat($cust_id)
	{
		$delete = array('customer_id' => $cust_id);
		$this->db->delete('chatting',$delete);

		$this->session->set_flashdata('message','<div class="alert alert-danger">Tidak ada pesan yang dikirim, pastikan anda sudah mengisi kolom pesan</div>');

		redirect('administrator/main/detail_chat/'.$cust_id);
	}

	function chat_product() {
		$this->load->helper('text');
		$data_chat = $this->db->query("SELECT A.*, B.id AS prod_id, B.name_item, C.name AS customer_name FROM chat_product A LEFT JOIN product B ON A.prod_id = B.id LEFT JOIN customer C ON A.customer_id = C.id WHERE A.id IN(SELECT max(id) FROM chat_product GROUP by prod_id)")->result();

		foreach ($data_chat as $chat) {
			$where = array(
				'customer_id' => $chat->customer_id,
				'prod_id'     => $chat->prod_id,
				'sender'      => 'Customer',
				'read_chat'   => 0
			);
			$count_chat = $this->db->get_where('chat_product', $where)->num_rows();
			$chat->count_chat = $count_chat;
		}
		$data['output'] = null;
		$data['data_chat'] = $data_chat;
		$this->load->view('administrator/page_list_chat_product', $data);
	}

	function detail_chat_product($customer_id, $prod_id) {
		$update = array('read_chat' => 1);
		$where = array('sender' => 'Customer', 'customer_id' => $customer_id);
		$this->db->update('chat_product', $update, $where);

		$where = array(
			'prod_id' => $prod_id,
			'customer_id' => $customer_id,
		);
		$chat_product = $this->db->order_by('create_at', 'asc')->get_where('chat_product', $where)->result();

		$data_chat = array();
		foreach ($chat_product as $row) {
			$data_chat[] = array(
				'id'          => $row->id,
				'create_at'   => date('d-m-Y H:i', strtotime($row->create_at)),
				'customer_id' => $row->customer_id,
				'content'     => $row->content,
				'sender'      => $row->sender,
				'read_chat'   => $row->read_chat
			);
		}

		$product = $this->main_model->get_detail('product', array('id' => $prod_id));
		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		$list_price = $this->db->order_by('cust_type_id', 'desc')->get_where('product_price', array('prod_id' => $prod_id))->result();

		if (count($list_price) > 0) {
			$product_price = $this->db->get_where('product_price', array('prod_id' => $prod_id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
			if (count($product_price) > 0) {
				$price = $product_price['price'];
				$price_old = $product_price['old_price'];
			} else {
				$price = $list_price[0]->price;
				$price_old = $list_price[0]->old_price;
			}
		} else {

			if ($customer['jenis_customer'] == '1') {
				$price     = $product['price'];
				$price_old = $product['price_old'];
			} else {
				if ($product['price_old_luar'] == 0) {
					$price     = $product['price_luar'];
					$price_old = $product['price_old_luar'];
				} else {
					$price     = $product['price'];
					$price_old = $product['price_old_luar'];
				}
			}
		}

		$data_product = array(
			'id'           => $product['id'],
			'product_name' => $product['name_item'],
			'price'        => 'Rp. '.number_format($price, 0, ',', '.'),
			'img'          => base_url('media/images/thumb/'.$product['image'])
		);

		$data = array(
			'customer'     => $customer,
			'product'      => $data_product,
			'chat_product' => $data_chat,
			'output'       => null
		);

		$this->load->view('administrator/page_detail_chat_product', $data);
	}

	function reply_chat_product() {
		$content = $this->input->post('content');
		$customer = $this->input->post('customer');
		$prod_id = $this->input->post('prod_id');

		if ($content != null) {
			$insert = array(
				'customer_id' => $customer,
				'prod_id'     => $prod_id,
				'content'     => $content,
				'sender'      => 'Admin',
			);
			$this->db->insert('chat_product',$insert);

			$this->session->set_flashdata('message','<div class="alert alert-success">Pesan Telah dikirimkan kepada customer</div>');
			$fcm_customer = $this->db->get_where('t_fcm_customer', array('customer_id' => $customer));
			if ($fcm_customer->num_rows() > 0) {
				foreach($fcm_customer->result() as $cust) {
					$reg_id = $cust->registration_id;
					array_push($this->registrationIds, $reg_id);
				}
			}
			$title = 'Chat Produk';
			$this->fcm_push_single($this->registrationIds, 'chat_product', $title, $content, $prod_id);
			redirect('administrator/main/detail_chat_product/'.$customer.'/'.$prod_id);
		} else {
			$this->session->set_flashdata('message','<div class="alert alert-danger">Tidak ada pesan yang dikirim, pastikan anda sudah mengisi kolom pesan</div>');

			redirect('administrator/main/detail_chat_product/'.$customer.'/'.$prod_id);
		}
	}

	function delete_chat_product($cust_id, $prod_id) {
		$delete = array('customer_id' => $cust_id, 'prod_id' => $prod_id);
		$this->db->delete('chat_product', $delete);

		$this->session->set_flashdata('message','<div class="alert alert-danger">Tidak ada pesan yang dikirim, pastikan anda sudah mengisi kolom pesan</div>');

		redirect('administrator/main/chat_product');
	}

	function customerType() {
		$crud = new grocery_CRUD();

		$crud->set_table('customer_type')
		->order_by('id', 'asc')
		->set_subject('Tipe Customer')
		->unset_export()
		->unset_print()
		->unset_delete()
		->add_action('Delete', '#', 'administrator/main/delete_customertype','btn btn-danger btn-crud');
			// ->add_action('Edit', '#', 'administrator/main/customertype/edit','btn btn-success btn-crud');
		$data['output'] = $crud->render();
		$this->load->view('administrator/page_customer_type', $data);
	}

	function delete_customertype($id) {
		$this->db->delete('customer_type', array('id' => $id));
		$this->db->delete('product_price', array('cust_type_id' => $id));
		$this->db->delete('harga_grosir', array('cust_type_id' => $id));
		$this->session->set_flashdata('message','<div class="alert alert-success">Data custome type telah dihapus</div>');

		redirect('administrator/main/customerType');
	}

	function order_dropship() {
		$this->check_hak_akses('order_dropship');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Pesanan Dropship');
		$crud->where('order_payment','Unpaid');
		$crud->where('order_status','Dropship');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesan');
		$crud->display_as('shipping_from','From');
		$crud->display_as('shipping_to','To');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('shipping_status','Status Pengiriman');
		$crud->display_as('order_status','Jenis Pesanan');
		$crud->display_as('notes','Catatan');
		$crud->display_as('print_nota','Print Nota');
		$crud->display_as('print_ekspedisi','Print Ekspedisi');
		$crud->order_by('id','DESC');
		$crud->columns('id','customer_id','order_datetime','total','order_payment','shipping_status','order_status', 'notes', 'print_nota', 'print_ekspedisi');
		$crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-success btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_read();
		$crud->unset_delete();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->set_relation('customer_id', 'customer', 'name');
		$crud->callback_column($this->unique_field_name('customer_id'), function ($value, $row) {
			if ($row->customer_id != 0) {
				return $value . ' (' . $row->customer_id . ')';
			} else {
				return '<span style="color:#e23427;">' . $row->name_customer . ' <strong> (Guest)</strong></span>';
			}
		});
		$crud->callback_column('print_nota',array($this,'callback_print_nota'));
		$crud->callback_column('print_ekspedisi',array($this,'callback_print_ekspedisi'));

		$data['output'] = $crud->render();
		$data['order_payment'] = 'Dropship';
		$this->load->view('administrator/page_order',$data);
	}

	public function callback_print_nota($value) {
		$icon = $value == 1 ? 'fa-check text-success' : 'fa-ban text-danger';
		return '<div class="text-center"><i class="fa '.$icon.'"></i></div>';
	}

	public function callback_print_ekspedisi($value) {
		$icon = $value == 1 ? 'fa-check text-success' : 'fa-ban text-danger';
		return '<div class="text-center"><i class="fa '.$icon.'"></i></div>';
	}

	function report_autocancel() {
		$data['output'] = null;
		$this->load->view('administrator/page_report_autocancel', $data);
	}

	public function searchReportAutocancel() {
		$customer_id = $this->input->post('customer_id');
		$date_awal   = $this->input->post('date_awal');
		$date_akhir  = $this->input->post('date_akhir');
		$product_id  = $this->input->post('product_id');
		$perpage     = $this->input->post('perpage');
		$page        = $this->input->post('page');

		$offset  = $perpage * ($page - 1);

		$where_item_cancel = array();
		if ($customer_id) {
			$where_item_cancel['OIC.customer_id'] = $customer_id;
		}
		if ($product_id) {
			$where_item_cancel['OIC.prod_id'] = $product_id;
		}

		$this->db->select('COUNT(*) AS total')
		->from('orders_item_cancel OIC')
		->join('customer C', 'C.id = OIC.customer_id', 'left')
		->join('product', 'product.id = OIC.prod_id', 'left')
		->join('product_variant PV', 'PV.id = OIC.variant_id', 'left')
		->where($where_item_cancel)
		->where('DATE_FORMAT(OIC.order_datetime, "%Y-%m-%d") BETWEEN "' . $date_awal . '" AND "' . $date_akhir . '"')
		->order_by('order_datetime','DESC');
		$result_data = $this->db->get()->row_array();

		$this->db->select('OIC.id, OIC.customer_id, order_datetime, name_item, C.name AS customer, PV.variant, OIC.qty, OIC.price')
		->from('orders_item_cancel OIC')
		->join('customer C', 'C.id = OIC.customer_id', 'left')
		->join('product', 'product.id = OIC.prod_id', 'left')
		->join('product_variant PV', 'PV.id = OIC.variant_id', 'left')
		->where($where_item_cancel)
		->where('DATE_FORMAT(OIC.order_datetime, "%Y-%m-%d") BETWEEN "' . $date_awal . '" AND "' . $date_akhir . '"')
		->order_by('order_datetime','DESC');
		if ($perpage != 'all') {
			$this->db->limit($perpage, $offset);
		}
		$results = $this->db->get()->result();

		$total_page = $perpage == 'all' ? 1 : ceil($result_data['total'] / $perpage);
		$output = array(
			'results'    => $results,
			'total_data' => $result_data['total'],
			'total_page' => $total_page
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	function reset_report_autocancel() {
		$data_session = array(
			'customer_id'      => '',
			'customer_name'    => '',
			'date_awal'        => '',
			'date_akhir'       => '',
			'product_name'     => '',
			'product_id'       => '',
			'tampilan_halaman' => '',
		);
		$this->session->unset_userdata($data_session);
		redirect('administrator/main/report_autocancel');
	}

	function report_assets() {
		$jenis_laporan = $this->input->post('jenis_laporan');
		$array['jenis_laporan'] = $jenis_laporan;

		$this->session->set_userdata($array);

		$all_qty = 0;
		$all_modal = 0;

		$result_products = array();
		if ($jenis_laporan == 'category') {
			$categories = $this->db->get_where('product_category', array('status_category' => 'publish'));
			foreach ($categories->result() as $category) {
				$where_product = array(
					'category_id' => $category->id,
					'status'      => 'Publish'
				);
				$products = $this->db->get_where('product', $where_product);

				$total_modal_category = 0;
				$total_qty_category = 0;
				foreach ($products->result() as $product) {
					$where_variant = array(
						'available' => 'Tersedia',
						'prod_id'   => $product->id
					);
					$variants = $this->db->get_where('product_variant', $where_variant);

					$total_qty_product = 0;
					foreach ($variants->result() as $variant) {
						$total_qty_product += $variant->stock;
					}
					$total_modal_product = $total_qty_product * $product->price_production;
					$total_modal_category += $total_modal_product;
					$total_qty_category += $total_qty_product;
				}

				$result_products[] = array(
					'name'  => $category->name,
					'qty'   => $total_qty_category,
					'total' => $total_modal_category
				);
				$all_qty += $total_qty_category;
				$all_modal += $total_modal_category;
			}
		} else if ($jenis_laporan == 'product') {
			$where_product = array(
				'A.status'          => 'Publish',
				'B.status_category' => 'publish'
			);
			$this->db->select('A.*')->join('product_category B', 'A.category_id = B.id', 'left');
			$products = $this->db->get_where('product A', $where_product);

			foreach ($products->result() as $product) {
				$total_modal_product = 0;
				$total_qty_product = 0;

				$where_variant = array(
					'available' => 'Tersedia',
					'prod_id'   => $product->id
				);
				$variants = $this->db->get_where('product_variant', $where_variant);

				$list_variant = array();
				foreach ($variants->result() as $variant) {
					$list_variant[] = array(
						'name'  => $variant->variant,
						'qty'   => $variant->stock,
						'total' => $variant->stock * $product->price_production
					);
					$total_modal_variant = $variant->stock * $product->price_production;
					$total_modal_product += $total_modal_variant;
					$total_qty_product += $variant->stock;
				}
				$result_products[] = array(
					'name'         => $product->name_item,
					'list_variant' => $list_variant
				);
				$all_qty += $total_qty_product;
				$all_modal += $total_modal_product;
			}
		}

		$data['products'] = $result_products;
		$data['output'] = null;
		$data['all_qty'] = $all_qty;
		$data['all_modal'] = $all_modal;
		$this->load->view('administrator/page_report_assets', $data);
	}

	function reset_report_assets() {
		$data_session['jenis_laporan'] = '';
		$this->session->unset_userdata($data_session);
		redirect('administrator/main/report_assets');
	}

	public function report_asset_export() {
		$jenis_laporan = $this->input->post('jenis_laporan');
		$name_excel = $jenis_laporan == 'category' ? 'Product Category' : 'Product';

		header('Cache-Control: must-revalidate');
		header('Pragma: must-revalidate');
		header('Content-type: application/vnd.ms-excel');
		header('Content-disposition: attachment; filename=Laporan Assets '.$name_excel.'.xls');

		$all_qty = 0;
		$all_modal = 0;

		$result_products = array();
		if ($jenis_laporan == 'category') {
			$categories = $this->db->get_where('product_category', array('status_category' => 'publish'));
			foreach ($categories->result() as $category) {
				$where_product = array(
					'category_id' => $category->id,
					'status'      => 'Publish'
				);
				$products = $this->db->get_where('product', $where_product);

				$total_modal_category = 0;
				$total_qty_category = 0;
				foreach ($products->result() as $product) {
					$where_variant = array(
						'available' => 'Tersedia',
						'prod_id'   => $product->id
					);
					$variants = $this->db->get_where('product_variant', $where_variant);

					$total_qty_product = 0;
					foreach ($variants->result() as $variant) {
						$total_qty_product += $variant->stock;
					}
					$total_modal_product = $total_qty_product * $product->price_production;
					$total_modal_category += $total_modal_product;
					$total_qty_category += $total_qty_product;
				}

				$result_products[] = array(
					'name'  => $category->name,
					'qty'   => $total_qty_category,
					'total' => $total_modal_category
				);
				$all_qty += $total_qty_category;
				$all_modal += $total_modal_category;
			}
		} else if ($jenis_laporan == 'product') {
			$where_product = array(
				'A.status'          => 'Publish',
				'B.status_category' => 'publish'
			);
			$this->db->select('A.*')->join('product_category B', 'A.category_id = B.id', 'left');
			$products = $this->db->get_where('product A', $where_product);

			foreach ($products->result() as $product) {
				$total_modal_product = 0;
				$total_qty_product = 0;

				$where_variant = array(
					'available' => 'Tersedia',
					'prod_id'   => $product->id
				);
				$variants = $this->db->get_where('product_variant', $where_variant);

				$list_variant = array();
				foreach ($variants->result() as $variant) {
					$list_variant[] = array(
						'name'  => $variant->variant,
						'qty'   => $variant->stock,
						'total' => $variant->stock * $product->price_production
					);
					$total_modal_variant = $variant->stock * $product->price_production;
					$total_modal_product += $total_modal_variant;
					$total_qty_product += $variant->stock;
				}
				$result_products[] = array(
					'name'         => $product->name_item,
					'list_variant' => $list_variant
				);
				$all_qty += $total_qty_product;
				$all_modal += $total_modal_product;
			}
		}

		$data['products'] = $result_products;
		$data['output'] = null;
		$data['all_qty'] = $all_qty;
		$data['all_modal'] = $all_modal;
		$data['jenis_laporan'] = $jenis_laporan;
		$this->load->view('administrator/page_report_assets_export', $data);
	}

	function upload() {
		$data_value_img = $this->main_model->get_detail('content',array('name' => 'name_img_setting'));

		/*upload image logo */
		$config['upload_path'] = './media/images/original';
		$config['allowed_types'] = '*';
		if ($data_value_img['value'] == 2) {
			$config['file_name']	= date('YmdHis'). '_' . preg_replace(array('/[^\w@|\-]/i', '/[-]+/') , '_', $this->client_name).'.jpg';
		}

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('qqfile')){
			$error = array('error' => $this->upload->display_errors());
			echo $error['error'];
		} else {
			$data = $this->upload->data();
			$resp = array(
				'success' => true,
				'data'    => $data['file_name'],
			);
			echo json_encode($resp);
		}
	}

	public function get_foto($prod_id) {
		$product = $this->main_model->get_detail('product', array('id' => $prod_id));

		$data[] = array(
			'name'         => $product['image'],
			'uuid'         => $product['id'].'#'.$product['image'].'#1',
			'thumbnailUrl' => base_url('media/images/medium/'.$product['image'])
		);

		$image_pro = $this->main_model->get_list_where('rel_produk_image',array('prod_id'=> $prod_id));
		foreach ($image_pro->result() as $row) {
			if ($row->image != '') {
				$data[] = array(
					'name'         => $row->image,
					'uuid'         => $row->id.'#'.$row->image.'#'.$row->urutan,
					'thumbnailUrl' => base_url('media/images/medium/'.$row->image)
				);
			}
		}

		echo json_encode($data);
	}

	public function delete_foto() {
		$filename = $this->input->post('filename');
		unlink('./media/images/original/'.$filename);
		$resp = array(
			'success' => true,
		);
		echo json_encode($resp);
	}

	function update_image($prod_id) {
		$product = $this->main_model->get_detail('product', array('id' => $prod_id));
		$data_value_img = $this->main_model->get_detail('content',array('name' => 'name_img_setting'));

		/*upload image logo */
		$config['upload_path'] = './media/images/original';
		$config['allowed_types'] = '*';
		if ($data_value_img['value'] == 2) {
			$config['file_name']	= preg_replace(array('/[^\w@|\-]/i', '/[-]+/') , '_', $this->client_name).'.jpg';
		} else if ($data_value_img['value'] == 3) {
			$config['file_name']	= preg_replace(array('/[^\w@|\-]/i', '/[-]+/') , '_', $product['name_item']).'.jpg';
		}

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('qqfile')){
			$error = array('error' => $this->upload->display_errors());
			echo $error['error'];
		} else {
			$data = $this->upload->data();
			$name_image = $data['file_name'];
			if ($product['image'] == '') {
				$this->db->update('product', array('image' => $name_image), array('id' => $prod_id));
			} else {
				$rel_img = $this->db->get_where('rel_produk_image', array('prod_id' => $prod_id, 'image' => ''))->result();
				if (count($rel_img) > 0) {
					$this->db->update('rel_produk_image', array('image' => $name_image), array('id' => $rel_img[0]->id));
				} else {
					$list_img = $this->db->get_where('rel_produk_image', array('prod_id' => $prod_id))->num_rows();
					$data_image = array(
						'prod_id' => $prod_id,
						'image' => $name_image,
						'urutan' => $list_img + 2
					);
					$this->db->insert('rel_produk_image', $data_image);
				}
			}

			// Thumbs
			$config_res['image_library'] = 'gd2';
			$config_res['source_image'] = 'media/images/original/'.$name_image;
			$config_res['maintain_ratio'] = FALSE;
			$config_res['width'] = 100;
			$config_res['height'] = 100;
			$config_res['new_image'] = 'media/images/thumb/'.$name_image;
			$this->image_lib->initialize($config_res);
			$this->image_lib->resize();

			// medium
			$config_res['image_library'] = 'gd2';
			$config_res['source_image'] = 'media/images/original/'.$name_image;
			$config_res['maintain_ratio'] = TRUE;
			$config_res['width'] = 320;
			$config_res['height'] = 320;
			$config_res['new_image'] = 'media/images/medium/'.$name_image;
			$this->image_lib->initialize($config_res);
			$this->image_lib->resize();

			// large
			$config_res['image_library'] = 'gd2';
			$config_res['source_image'] = 'media/images/original/'.$name_image;
			$config_res['maintain_ratio'] = TRUE;
			$config_res['width'] = 640;
			$config_res['height'] = 640;
			$config_res['new_image'] = 'media/images/large/'.$name_image;

			$this->image_lib->initialize($config_res);
			$this->image_lib->resize();

			// original
			$config_res['image_library'] = 'gd2';
			$config_res['source_image'] = 'media/images/original/'.$name_image;
			$config_res['maintain_ratio'] = TRUE;
			$config_res['width'] = 1024;
			$config_res['height'] = 1024;
			$config_res['new_image'] = 'media/images/original/'.$name_image;

			$this->image_lib->initialize($config_res);
			$this->image_lib->resize();
			$resp = array(
				'success' => true,
				'data'    => $data['file_name'],
			);
			echo json_encode($resp);
		}
	}

	function get_cost_ekspedisi($destination_id,$weight,$courier,$tarif_tipe)
	{
		//$tarif_tipe = 'Paketpos Biasa';
		$base_url_api = "http://api.tokomobile.co.id/ongkir/development/api/";
		$origin_city_id = $this->main_model->get_detail('content',array('name' => 'origin_city_id'));
		$token_api = $this->config->item('tokomobile_token');
		$domain_api = $this->config->item('tokomobile_domain');
		$ch = curl_init();                    // Initiate cURL
		$url = $base_url_api."cost?token=".$token_api."&domain=".$domain_api."&origin_city_id=".$origin_city_id['value']."&weight=".$weight."&courier=".$courier."&satuan=kg&destination_type="."subdistrict"."&destination_id=".$destination_id;
	    //$url = $this->base_url_api."cost?token=".$this->config->item('tokomobile_token')."&domain=".$this->config->item('tokomobile_domain')."&province_id=".$prov_id;
		curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_POST, true);  // Tell cURL you want to post something
	    //curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&domain=".$this->domain."&province_id=".$prov_id."");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the output in string format
	    $output = curl_exec ($ch); // Execute
	    $x = json_decode($output);

		//var_dump($x);
		//print($x->result);
		//print($origin_city_id['value'].' '.$destination_id.' '.$weight.' '.$courier);
		//exit;
	    foreach($x->result as $a){

	    	foreach($a->costs as $b):

	    		if($b->service == $tarif_tipe)
	    		{
	    			foreach($b->cost as $c):

	    				$cost = $c->value;
	    			endforeach;
	    		}
	    	endforeach;
	    };

	    return $cost;
	}

	function update_print() {
		$field = $this->input->post('field');
		$update = array($field => 1);
		$id['id'] = $this->input->post('id');
		$this->db->update('orders', $update, $id);
		echo json_encode(array('success' => true));
	}

	public function check_pincode() {
		$code = $this->input->post('code');
		$user_level = $this->session->userdata('webadmin_user_level');

		$user_id = $this->session->userdata('webadmin_user_id');
		$user = $this->main_model->get_detail('users', array('id' => $user_id));
		// $pincode = $this->encrypt->decode($user['pincode']);
		$pincode = password_verify($code, $user['pincode']);

		if ($code == $pincode && $pincode != '') {
			$data['status'] = 'Success';
		} else {
			$data['status'] = 'Failed';
		}

		echo json_encode($data);
	}

	public function scan_order() {
		$this->check_hak_akses('scan_order');
		$data['output'] = null;
		$data['payment_method'] = $this->db->get('payment_method')->result();
		$this->load->view('administrator/page_scan_order', $data);
	}

	public function scan_check_order() {
		$this->check_hak_akses('scan_order');
		$order_id = $this->input->post('order_id');
		if ($order_id != null) {
			$order = $this->main_model->get_detail('orders', array('id' => $order_id));
			if (!empty($order)) {
				if ($order['order_payment'] == 'Unpaid') {
					$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
					$data = array(
						'status'        => 'Success',
						'order_id'      => $order_id,
						'customer_id'   => $customer['id'],
						'customer_name' => $customer['name'],
						'order_total'   => $order['total']
					);
					echo json_encode($data);
					exit();
				} else {
					$status = 'Failed';
					$message = '<div class="alert alert-danger">Pesanan telah lunas</div>';
				}
			} else {
				$status = 'Failed';
				$message = '<div class="alert alert-danger">Pesanan tidak ditemukan !</div>';
			}
		} else {
			$status = 'Failed';
			$message = '<div class="alert alert-danger">Order ID tidak boleh kosong !</div>';
		}
		$data = array(
			'status' => $status,
			'message' => $message,
		);
		echo json_encode($data);
	}

	public function scan_order_proccess() {
		$this->check_hak_akses('scan_order');
		$order_id = $this->input->post('order_id');
		$diskon = $this->input->post('diskon');
		$payment_method = $this->input->post('paymentMethod');
		if ($order_id != null) {
			$order = $this->main_model->get_detail('orders', array('id' => $order_id));
			if (!empty($order)) {
				if ($payment_method > 0) {
					$data_update_item = array('order_payment' => 'Paid');

					$total = $order['shipping_fee'] + $order['subtotal'] - $diskon;
					$data_update = array(
						'order_payment'     => 'Paid',
						'date_payment'      => date('Y-m-d H:i:s'),
						'payment_method_id' => $payment_method,
						'total'             => $total,
						'diskon'            => $diskon
					);

					$where_item = array('order_id' => $order_id);
					$where = array('id' => $order_id);

					$this->db->update('orders_item', $data_update_item, $where_item);
					$this->db->update('orders', $data_update, $where);


					if ($order['added_point'] == 0) {

						$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
						if ($point_reward_status['value'] == 'on') {
							$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
							$total_point = ($order['subtotal'] - $order['diskon']) / $nominal_to_point['value'];
							$customer = $this->main_model->get_detail('customer', array('id' => $order['customer_id']));
							$point_customer = $customer['point'] + $total_point - $order['point'];

							$point_history = array(
								'customer_id' => $order['customer_id'],
								'point_prev'  => $customer['point'],
								'point_in'    => $total_point - $order['point'],
								'point_end'   => $point_customer,
								'order_id'    => $order['id'],
								'note'        => 'Mendapatkan point',
								'user_id'     => $this->session->userdata('webadmin_user_id'),
							);
							$this->db->insert('point_histories', $point_history);

							$this->db->where('id', $order['customer_id'])
							->update('customer', array('point' => $point_customer));
							$this->db->update('orders', array('added_point' => 1, 'get_point' => 1), $where);
						}
					}

					$subject = 'Status Pesanan';
					$content = 'Status pesanan Anda #' . $order_id . ' telah dijadikan lunas';
					$this->sendNotifikasi($order['customer_id'], $subject, $content, 'order_status', $order_id);

					$status = 'Success';
					$message = '<div class="alert alert-success">Status Data Pesanan <strong>#'.$order_id.' </strong> telah dijadikan lunas !</div>';
				} else {
					$status = 'Failed';
					$message = '<div class="alert alert-danger">Methode pembayaran harus dipilih !</div>';
				}
			} else {
				$status = 'Failed';
				$message = '<div class="alert alert-danger">Pesanan tidak ditemukan !</div>';
			}
		} else {
			$status = 'Failed';
			$message = '<div class="alert alert-danger">Order ID tidak boleh kosong !</div>';
		}
		$data = array(
			'status' => $status,
			'message' => $message,
		);
		echo json_encode($data);
	}

	function __destruct() {
		if ($this->session->userdata('webadmin_login')) {
			$data_insert = array(
				'log_admin_id' => $this->session->userdata('webadmin_user_id'),
				'log_url'      => current_url(),
				'log_param'    => json_encode($this->input->post())
			);
			$this->db->insert('log_admin',$data_insert);
		}
	}

	public function getOrderKeep($page = 1) {
		$limit = 10;
		$offset  = $limit * ($page - 1);

		$orderTotal = $this->db->select('COUNT(id) AS total')
		->where('order_status', 'Keep')->where('order_payment', 'Unpaid')->where('order_id', 0)
		->get('orders_item')->row_array();

		$orderKeeps = $this->db->select('A.id, A.customer_id, B.name, C.name_item, D.variant, A.qty, A.subtotal')
		->join('customer B', 'A.customer_id = B.id', 'left')
		->join('product C', 'A.prod_id = C.id', 'left')
		->join('product_variant D', 'A.variant_id = D.id', 'left')
		->where('order_status', 'Keep')->where('order_payment', 'Unpaid')->where('order_id', 0)
		->order_by('A.id', 'desc')
		->get('orders_item A', $limit, $offset)->result();
		$data = array(
			'total'  => $orderTotal['total'],
			'orders' => $orderKeeps
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function getOrderRekap($page = 1) {
		$limit = 10;
		$offset  = $limit * ($page - 1);

		$orderTotal = $this->db->select('COUNT(id) AS total')
		->where('order_status', 'Keep')->where('order_payment', 'Unpaid')
		->get('orders')->row_array();

		$orderRekaps = $this->db->select('A.id, A.customer_id, B.name, A.total')
		->join('customer B', 'A.customer_id = B.id', 'left')
		->where('order_status', 'Keep')->where('order_payment', 'Unpaid')
		->order_by('A.id', 'desc')
		->get('orders A', $limit, $offset)->result();
		$data = array(
			'total'  => $orderTotal['total'],
			'orders' => $orderRekaps
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function getOrderDropship($page = 1) {
		$limit = 10;
		$offset  = $limit * ($page - 1);

		$orderTotal = $this->db->select('COUNT(id) AS total')
		->where('order_status', 'Dropship')->where('order_payment', 'Unpaid')
		->get('orders')->row_array();

		$orderDropships = $this->db->select('A.id, A.customer_id, B.name, A.total')
		->join('customer B', 'A.customer_id = B.id', 'left')
		->where('order_status', 'Dropship')->where('order_payment', 'Unpaid')
		->order_by('A.id', 'desc')
		->get('orders A', $limit, $offset)->result();
		$data = array(
			'total'  => $orderTotal['total'],
			'orders' => $orderDropships
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function getConfirmChat() {
		$confirmTotal = $this->db->select('COUNT(id) AS total')
		->where('status', 'Pending')
		->get('confirmation')->row_array();
		$chatTotal = $this->db->select('COUNT(id) AS total')
		->where('status', 'Unread')->where('sender', 'Customer')
		->get('chatting')->row_array();
		$chatProductTotal = $this->db->select('COUNT(id) AS total')
		->where('read_chat', 0)->where('sender', 'Customer')
		->get('chat_product')->row_array();

		$data = array(
			'confirm'     => $confirmTotal['total'],
			'chat'        => $chatTotal['total'],
			'chatProduct' => $chatProductTotal['total']
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function getOrderPiutang($page = 1) {
		$limit = 10;
		$offset  = $limit * ($page - 1);

		$orderTotal = $this->db->select('COUNT(id) AS total')
		->where('order_status', 'Piutang')->where('order_payment', 'Unpaid')
		->get('orders')->row_array();

		$orderPiutangs = $this->db->select('A.id, A.customer_id, B.name, A.total')
		->join('customer B', 'A.customer_id = B.id', 'left')
		->where('order_status', 'Piutang')->where('order_payment', 'Unpaid')
		->order_by('A.id', 'desc')
		->get('orders A', $limit, $offset)->result();
		$data = array(
			'total'  => $orderTotal['total'],
			'orders' => $orderPiutangs
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function order_detail_edit_diskon() {
		$order_id = $this->input->post('order_id');
		$diskon = $this->input->post('diskon');

		$where = array('id' => $order_id);
		$order = $this->main_model->get_detail('orders', $where);
		$total = $order['shipping_fee'] + $order['subtotal'] - $diskon;
		$order_update = array(
			'diskon' => $diskon,
			'total'  => $total
		);
		$this->db->where($where)->update('orders', $order_update);
		$this->session->set_flashdata('message','<div class="alert alert-success">Data telah diperbarui !</div>');
		redirect('administrator/main/order_detail/'.$order_id);
	}

	public function customer_types() {
		$customer_types = $this->db->order_by('id', 'desc')->get('customer_type')->result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($customer_types));
	}

	public function bank_account() {
		$this->check_hak_akses('bank_accounts');
		$crud = new grocery_CRUD();
		$crud->where('active', 1)
		->set_table('bank_accounts')
		->set_subject('Bank Account')
		->set_rules('nama_bank', 'Nama Bank', 'required')
		->set_rules('nomor_rekening', 'Nomor Rekening', 'required')
		->set_rules('nama_rekening', 'Nama Rekening', 'required')
		->unset_columns('active')
		->unset_fields('active')
		->add_action('Edit', '#', 'administrator/main/bank_account/edit', 'btn btn-primary btn-crud')
		->callback_delete(array($this, 'bank_account_delete'))
		->display_as('nama_bank', 'Nama Bank')
		->display_as('nomor_rekening', 'Nomor Rekening')
		->display_as('nama_rekening', 'Nama Rekening');
		$data['output'] = $crud->render();
		$this->load->view('administrator/page_bank_account', $data);
	}

	public function bank_account_delete($id) {
		$this->check_hak_akses('bank_accounts');
		$data_update = array('active' => 0);
		$where = array('id' => $id);
		$this->db->update('bank_accounts', $data_update, $where);
		return true;
	}

	public function supplier() {
		$this->check_hak_akses('suppliers');
		$crud = new grocery_CRUD();
		$crud->where('active', 1)
		->set_table('suppliers')
		->set_subject('Data Supplier')
		->set_rules('kode_supplier', 'Kode Supplier', 'required')
		->set_rules('nama_supplier', 'Nama Supplier', 'required')
		->set_rules('bank', 'Bank', 'required')
		->set_rules('no_rekening', 'No. Rekening (Nama)', 'required')
		->set_rules('telp', 'No. Telp', 'required')
		->set_rules('alamat', 'Alamat', 'required')
		->unset_columns('active')
		->unset_fields('active')
		->add_action('Edit', '#', 'administrator/main/supplier/edit', 'btn btn-primary btn-crud')
		->callback_delete(array($this, 'supplier_delete'))
		->display_as('kode_supplier', 'Kode Supplier')
		->display_as('nama_supplier', 'Nama Supplier')
		->display_as('no_rekening', 'No. Rekening (Nama)')
		->unset_texteditor('alamat');
		$data['output'] = $crud->render();
		$this->load->view('administrator/page_supplier', $data);
	}

	public function supplier_delete($id) {
		$this->check_hak_akses('suppliers');
		$data_update = array('active' => 0);
		$where = array('id' => $id);
		$this->db->update('suppliers', $data_update, $where);
		return true;
	}

	public function input_pengeluaran() {
		$this->check_hak_akses('input_pengeluaran');
		$crud = new grocery_CRUD();
		$state = $crud->getState();
		$where_biaya = $state == 'edit' ? array('active' => 1) : null;
		$crud->set_table('pengeluaran')
		->set_subject('Input Pengeluaran')
		->set_relation('jenis_biaya_id', 'jenis_biaya', 'nama', $where_biaya)
		->set_relation('user_id', 'users', 'user_fullname')
		->callback_column('nominal', function($value) {
			return 'Rp ' . number_format($value, 0, ',', '.');
		})
		->set_rules('tanggal', 'Tanggal', 'required')
		->set_rules('jenis_biaya_id', 'Jenis Biaya', 'required')
		->set_rules('catatan', 'Catatan', 'required')
		->set_rules('nominal', 'Nominal', 'required')
		->unset_add()
		->unset_delete()
		->unset_export()
		->unset_fields('user_id')
		->add_action('Delete', '#', 'administrator/main/delete_pengeluaran', 'btn btn-danger btn-crud delete-pengeluaran')
		->add_action('Edit', '#', 'administrator/main/input_pengeluaran/edit', 'btn btn-primary btn-crud')
		->display_as('jenis_biaya_id', 'Jenis Biaya')
		->display_as('user_id', 'User')
		->unset_texteditor('alamat');
		$data = array(
			'output' 	  => $crud->render(),
			'jenis_biaya' => $this->db->get_where('jenis_biaya', array('active' => 1))->result()
		);
		$this->load->view('administrator/page_input_pengeluaran', $data);
	}

	public function delete_pengeluaran($id) {
		$this->db->delete('pengeluaran', array('id' => $id));
		$this->session->set_flashdata('message', '<div class="alert alert-success">Pengeluaran berhasil dihapus</div>');
		redirect('administrator/main/input_pengeluaran', 'refresh');
	}

	public function add_jenis_biaya() {
		$this->check_hak_akses('input_pengeluaran');
		$this->form_validation->set_rules('jenis_biaya', 'Jenis Biaya', 'trim|required');
		if ($this->form_validation->run()) {
			$this->db->insert('jenis_biaya', array('nama' => $this->input->post('jenis_biaya')));
			$this->session->set_flashdata('message', '<div class="alert alert-success">Jenis biaya berhasil disimpan</div>');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Jenis biaya harus diisi</div>');
		}
		redirect('administrator/main/input_pengeluaran', 'refresh');
	}

	public function edit_jenis_biaya() {
		$this->check_hak_akses('input_pengeluaran');
		$this->form_validation->set_rules('jenis_biaya', 'Jenis Biaya', 'trim|required');
		if ($this->form_validation->run()) {
			$id = $this->input->post('jenis_biaya_id');
			$data['nama'] = $this->input->post('jenis_biaya');
			$this->db->update('jenis_biaya', $data, array('id' => $id));
			$this->session->set_flashdata('message', '<div class="alert alert-success">Jenis biaya berhasil diubah</div>');
		} else {
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Jenis biaya harus diisi</div>');
		}
		redirect('administrator/main/input_pengeluaran', 'refresh');
	}

	public function delete_jenis_biaya($id) {
		$this->check_hak_akses('input_pengeluaran');
		$data_update = array('active' => 0);
		$where = array('id' => $id);
		$this->db->update('jenis_biaya', $data_update, $where);
		$this->session->set_flashdata('message', '<div class="alert alert-success">Jenis biaya berhasil dihapus</div>');
		redirect('administrator/main/input_pengeluaran', 'refresh');
	}

	public function add_input_pengeluaran() {
		$this->check_hak_akses('input_pengeluaran');
		$this->form_validation->set_rules('date', 'Tanggal', 'trim|required');
		$this->form_validation->set_rules('jenis_biaya_id', 'Jenis Biaya', 'trim|required');
		$this->form_validation->set_rules('catatan', 'Catatan', 'trim|required');
		$this->form_validation->set_rules('nominal', 'Nominal', 'trim|required');

		if ($this->form_validation->run()) {
			$data = array(
				'tanggal'        => $this->input->post('date'),
				'jenis_biaya_id' => $this->input->post('jenis_biaya_id'),
				'catatan'        => $this->input->post('catatan'),
				'nominal'        => $this->input->post('nominal'),
				'user_id'        => $this->session->userdata('webadmin_user_id')
			);
			$this->db->insert('pengeluaran', $data);
			$this->session->set_flashdata('message', '<div class="alert alert-success">Pengeluaran berhasil disimpan</div>');
			redirect('administrator/main/input_pengeluaran', 'refresh');
		} else {
			$this->input_pengeluaran();
		}
	}

	public function delete_input_pengeluaran($id) {
		$this->check_hak_akses('input_pengeluaran');
		$this->db->delete('pengeluaran', array('id' => $id));
		$this->session->set_flashdata('message', '<div class="alert alert-success">Pengeluaran berhasil dihapus</div>');
		redirect('administrator/main/input_pengeluaran', 'refresh');
	}

	public function export_input_pengeluaran() {
		$this->check_hak_akses('input_pengeluaran');

		$pengeluaran = $this->db->select('P.tanggal, JB.nama AS jenis_biaya, P.catatan, P.nominal, U.user_fullname')
		->join('jenis_biaya JB', 'JB.id =  P.jenis_biaya_id', 'left')
		->join('users U', 'U.id =  P.user_id', 'left')
		->get('pengeluaran P')->result();

		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Input Pengeluaran');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Input Pengeluaran</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">Tanggal</th>
		<th align="center">Jenis Biaya</th>
		<th align="center">Catatan</th>
		<th align="center">Nominal</th>
		<th align="center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($pengeluaran as $item) {
			$html .= '
			<tr nobr="true">
			<td>' . date('d-m-Y', strtotime($item->tanggal)) . '</td>
			<td>' . $item->jenis_biaya . '</td>
			<td>' . $item->catatan . '</td>
			<td>Rp ' . number_format($item->nominal, 0, ',', '.') . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Input Pengeluaran.pdf', 'I');
	}

	public function searchCustomer() {
		$search = $this->input->get('search');
		$product_ids = $this->input->get('product_id');
		$customers = $this->db->select('id, name, jenis_customer')
		->like('name', $search)
		->get('customer', 25)->result();
		foreach ($customers as $customer) {
			$product_prices = [];
			foreach ($product_ids as $product_id) {
				$product = $this->db->select('price, price_luar')
				->get_where('product', array('id' => $product_id))->row_array();
				$variants = $this->db->select('id, variant, stock')
				->get_where('product_variant', array(
					'prod_id'  		 => $product_id,
					'available != '  => 'Delete'
				))->result();
				$prices = $this->db->order_by('cust_type_id', 'desc')
				->get_where('product_price', array('prod_id' => $product_id))->result();
				if (count($prices) > 0) {
					$product_price = $this->db->get_where('product_price', array(
						'prod_id'      => $product_id,
						'cust_type_id' => $customer->jenis_customer
					))->row_array();
					if (count($product_price) > 0) {
						$price = $product_price['price'];
					} else {
						$price = $prices[0]->price;
					}
				} else {
					if ($customer->jenis_customer == '1') {
						$price = $product['price'];
					} else {
						$price = $product['price_luar'];
					}
				}
				$product_prices[] = $price;
			}
			$customer->product_prices = $product_prices;
		}
		$this->output->set_content_type('application/json')
		->set_output(json_encode($customers));
	}

	public function searchProduct() {
		$search = $this->input->post('search');
		$customer_id = $this->input->post('customer_id');
		$customer = $this->db->select('jenis_customer')
		->get_where('customer', array('id' => $customer_id))->row_array();
		$products = $this->db->select('id, name_item AS name, price, price_luar, weight')
		->like('name_item', $search)
			// ->where('status', 'Publish')
		->order_by('datetime', 'DESC')
		->get('product', 25)->result();
		foreach ($products as $product) {
			$variants = $this->db->select('id, variant, stock')
			->get_where('product_variant', array(
				'prod_id'  		 => $product->id,
				'available != '  => 'Delete'
			))->result();
			$prices = $this->db->order_by('cust_type_id', 'desc')
			->get_where('product_price', array('prod_id' => $product->id))->result();
			if (count($prices) > 0) {
				$product_price = $this->db->get_where('product_price', array(
					'prod_id' => $product->id,
					'cust_type_id' => $customer['jenis_customer']
				))->row_array();
				if (count($product_price) > 0) {
					$price = $product_price['price'];
				} else {
					$price = $prices[0]->price;
				}
			} else {
				if ($customer['jenis_customer'] == '1') {
					$price = $product->price;
				} else {
					$price = $product->price_luar;
				}
			}
			$product->price = $price;
			$product->variants = $variants;
		}
		$this->output->set_content_type('application/json')
		->set_output(json_encode($products));
	}

	public function searchProductItem() {
		$search = $this->input->get('search');
		$this->db->select('id, name_item AS name')
		->from('product')
		->like('name_item', $search)
		->not_like('status', 'Delete')
		->limit(25);
		$products = $this->db->get()->result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($products));
	}

	public function searchCustomerItem() {
		$search = $this->input->get('search');
		$customers = $this->db->select('id, name')
		->like('name', $search)
		->get('customer', 25, 0)->result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($customers));
	}

	public function master_data_penjualan() {
		$this->check_hak_akses('master_data_penjualan');
		$data['output'] = null;
		$this->load->view('administrator/page_master_data_penjualan', $data);
	}

	public function getMasterPenjualan() {
		$this->check_hak_akses('master_data_penjualan');
		$output = $this->result_master_penjualan();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	private function result_master_penjualan() {
		$this->check_hak_akses('master_data_penjualan');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$sort_by = $this->input->post('sort_by');
		$customer_type_id = $this->input->post('customer_type_id');

		if ($date_type == 'date') {
			$where_date = 'DATE_FORMAT(OI.order_datetime, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
			$dateResult = date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo));
		} else {
			$where_date = 'DATE_FORMAT(OI.order_datetime, "%Y-%m") = "' . $month . '"';
			$dateResult = date('m/Y', strtotime($month));
		}

		$this->db->select('OI.customer_id, C.name, CT.name AS customer_type, SUM(qty) AS qty, SUM(subtotal) AS total')
		->from('orders_item OI')
		->join('customer C', 'OI.customer_id = C.id', 'INNER')
		->join('customer_type CT', 'C.jenis_customer = CT.id', 'LEFT')
		->where('order_payment', 'Paid')
		->where('order_status !=', 'Cancel')
		->where($where_date);
		$order = array(
			'max_qty'   => array('qty', 'DESC'),
			'min_qty'   => array('qty', 'ASC'),
			'max_order' => array('total', 'DESC'),
			'min_order' => array('total', 'ASC')
		);
		$this->db->where_in('C.jenis_customer', $customer_type_id)
		->group_by('OI.customer_id')
		->order_by(...$order[$sort_by]);
		$output = array(
			'dateResult' => $dateResult,
			'results'    => $this->db->get()->result()
		);
		return $output;
	}

	public function export_master_penjualan() {
		$this->check_hak_akses('master_data_penjualan');
		$output = $this->result_master_penjualan();
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Master Data Penjualan');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Master Data Penjualan</h1>
		<h2>' . $output['dateResult'] . '</h2>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">Rank</th>
		<th>Pembeli</th>
		<th>Jenis Customer</th>
		<th align="center">Qty</th>
		<th>Total Penjualan</th>
		</tr>
		</thead>
		<tbody>
		';
		$i = 1;
		foreach ($output['results'] as $result) {
			$html .= '
			<tr nobr="true">
			<td align="center">' . $i++ . '</td>
			<td>' . $result->name . '(' . $result->customer_id . ')' . '</td>
			<td>' . $result->customer_type . '</td>
			<td align="center">' . $result->qty . '</td>
			<td>Rp ' . number_format($result->total, 0, ',', '.') . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Master Data Penjualan.pdf', 'I');
	}

	public function print_master_penjualan() {
		$this->check_hak_akses('master_data_penjualan');
		$output = $this->result_master_penjualan();
		$html = '
		<head>
		<title>Master Data Penjualan ' . $output['dateResult'] . '</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">Master Data Penjualan</h2>
		<h3 class="text-center">' . $output['dateResult'] . '</h3>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">Rank</th>
		<th>Pembeli</th>
		<th>Jenis Customer</th>
		<th class="text-center">Qty</th>
		<th>Total Penjualan</th>
		</tr>
		</thead>
		<tbody>
		';
		$i = 1;
		foreach ($output['results'] as $result) {
			$html .= '
			<tr nobr="true">
			<td class="text-center">' . $i++ . '</td>
			<td>' . $result->name . '(' . $result->customer_id . ')' . '</td>
			<td>' . $result->customer_type . '</td>
			<td class="text-center">' . $result->qty . '</td>
			<td>Rp ' . number_format($result->total, 0, ',', '.') . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function report_omset() {
		$this->check_hak_akses('report_omset');
		$data['output'] = null;
		$this->load->view('administrator/page_report_omset', $data);
	}

	public function getCategories() {
		$categories = $this->db->get_where('product_category', array('status_category' => 'Publish'));
		$this->output->set_content_type('application/json')
		->set_output(json_encode($categories->result()));
	}

	public function getOmsetTable() {
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$customer_type_id = $this->input->post('customer_type_id');
		$category_id = $this->input->post('category_id');

		$this->db->select('DATE_FORMAT(OI.order_datetime, "%Y-%m-%d") AS date, C.jenis_customer, P.category_id, CT.name AS customer_type, PC.name AS category, SUM(OI.qty) as qty, SUM(OI.subtotal) as total')
		->from('orders_item OI')
		->join('customer C', 'OI.customer_id = C.id', 'LEFT')
		->join('customer_type CT', 'CT.id = C.jenis_customer', 'LEFT')
		->join('product P', 'OI.prod_id = P.id', 'LEFT')
		->join('product_category PC', 'PC.id = P.category_id', 'LEFT')
		->where('OI.order_payment', 'Paid')
		->where('OI.order_status !=', 'Cancel')
		->where_in('C.jenis_customer', $customer_type_id)
		->where_in('P.category_id', $category_id);
		if ($date_type == 'date') {
			$this->db->where('DATE_FORMAT(order_datetime, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"');
		} else {
			$this->db->where('DATE_FORMAT(order_datetime, "%Y-%m") = "' . $month . '"');
		}
		$this->db->group_by('DATE_FORMAT(OI.order_datetime, "%Y-%m-%d")')
		->group_by('C.jenis_customer')
		->group_by('P.category_id')
		->order_by('date', 'ASC')
		->order_by('C.jenis_customer', 'ASC')
		->order_by('P.category_id', 'ASC');
		$results = $this->db->get()->result();

		$this->output->set_content_type('application/json')
		->set_output(json_encode($results));
	}

	private function getStatDiagramOmset() {
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$data_type = $this->input->post('data_type');
		$customer_type_id = $this->input->post('customer_type_id');
		$category_id = $this->input->post('category_id');

		$this->db->select('DATE_FORMAT(OI.order_datetime, "%Y-%m-%d") AS date, SUM(OI.subtotal) as total');
		$this->db->from('orders_item OI')
		->where('OI.order_status !=', 'Cancel')
		->where('OI.order_payment', 'Paid');
		if ($data_type == 'product') {
			$this->db->select('P.category_id')
			->join('product P', 'OI.prod_id = P.id', 'LEFT')
			->where_in('P.category_id', $category_id)
			->group_by('P.category_id');
		} else {
			$this->db->select('C.jenis_customer')
			->join('customer C', 'OI.customer_id = C.id', 'LEFT')
			->where_in('C.jenis_customer', $customer_type_id)
			->group_by('C.jenis_customer');
		}
		if ($date_type == 'date') {
			$this->db->where('DATE_FORMAT(order_datetime, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"')
			->group_by('DATE_FORMAT(OI.order_datetime, "%Y-%m-%d")')
			->order_by('date', 'ASC');
		} else {
			$this->db->where('DATE_FORMAT(order_datetime, "%Y-%m") = "' . $month . '"')
			->group_by('DATE_FORMAT(OI.order_datetime, "%Y-%m")')
			->order_by('date', 'ASC');
		}
		return $this->db->get()->result();
	}

	public function getOmsetStatistic() {
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$data_type = $this->input->post('data_type');
		$customer_type_id = $this->input->post('customer_type_id');
		$category_id = $this->input->post('category_id');

		$results = $this->getStatDiagramOmset();
		$labels = array();
		$data = array();
		if ($date_type == 'date') {
			foreach ($results as $result) {
				$date = date('d-m-y', strtotime($result->date));
				if (!in_array($date, $labels)) {
					$labels[] = $date;
				}
			}
		} else {
			$labels[] = date('M Y', strtotime($month));
		}
		$datasets = array();
		if ($data_type == 'product') {
			$categories = $this->db->where_in('id', $category_id)
			->get('product_category')->result();
			$i = 0;
			foreach ($categories as $category) {
				$datasets[$i]['label'] = $category->name;
				$j = 0;
				foreach ($labels as $label) {
					$datasets[$i]['data'][$j] = 0;
					foreach ($results as $result) {
						if ($date_type == 'date') {
							$date = date('d-m-y', strtotime($result->date));
						} else {
							$date = date('M Y', strtotime($result->date));
						}
						if ($label == $date) {
							if ($category->id == $result->category_id) {
								$datasets[$i]['data'][$j] = (int)$result->total;
							}
						}
					}
					$j++;
				}
				$i++;
			}
		} else {
			$customer_types = $this->db->where_in('id', $customer_type_id)
			->get('customer_type')->result();
			$i = 0;
			foreach ($customer_types as $type) {
				$datasets[$i]['label'] = $type->name;
				$j = 0;
				foreach ($labels as $label) {
					$datasets[$i]['data'][$j] = 0;
					foreach ($results as $result) {
						if ($date_type == 'date') {
							$date = date('d-m-y', strtotime($result->date));
						} else {
							$date = date('M Y', strtotime($result->date));
						}
						if ($label == $date) {
							if ($type->id == $result->jenis_customer) {
								$datasets[$i]['data'][$j] = (int)$result->total;
							}
						}
					}
					$j++;
				}
				$i++;
			}
		}
		$dateFrom = date('d M', strtotime($dateFrom));
		$dateTo = date('d M Y', strtotime($dateTo));
		$month = date('M Y', strtotime($month));
		$title = $date_type == 'date' ? $dateFrom .' - ' . $dateTo : $month;
		$output = array(
			'labels'   => $labels,
			'datasets' => $datasets,
			'title'    => ['Statistik Laporan', $title]
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	public function getOmsetDiagram() {
		$month = $this->input->post('month');
		$data_type = $this->input->post('data_type');
		$customer_type_id = $this->input->post('customer_type_id');
		$category_id = $this->input->post('category_id');

		$results = $this->getStatDiagramOmset();
		$labels = array();
		if ($data_type == 'product') {
			$items = $this->db->where_in('id', $category_id)
			->get('product_category')->result();
		} else {
			$items = $this->db->where_in('id', $customer_type_id)
			->get('customer_type')->result();
		}
		foreach ($items as $item) {
			$labels[] = $item->name;
		}
		$data = array();
		foreach ($results as $result) {
			$data[] = (int)$result->total;
		}
		$datasets[0]['data'] = $data;
		$month = date('M Y', strtotime($month));
		$output = array(
			'labels'   => $labels,
			'datasets' => $datasets,
			'title'    => ['Diagram Penjualan', 'Bulan ' . $month]
		);
		$this->output->set_content_type('application/json')
		->set_output(json_encode($output));
	}

	public function report_pengeluaran() {
		$this->check_hak_akses('report_pengeluaran');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$this->form_validation->set_rules('date_type', 'Tipe Tanggal', 'trim|required');
		if ($date_type == 'date') {
			$this->form_validation->set_rules('dateFrom', 'Tanggal Dari', 'trim|required');
			$this->form_validation->set_rules('dateTo', 'Tanggal Sampai', 'trim|required');
		} else {
			$this->form_validation->set_rules('month', 'Bulan', 'trim|required');
		}

		$date_format = '';
		$data = array(
			'output'           => null,
			'data_pengeluaran' => array(),
			'date_type'        => $date_type,
			'dateFrom'         => $dateFrom,
			'dateTo'           => $dateTo,
			'month'            => $month
		);
		if ($this->form_validation->run()) {
			$report_pengeluaran = $this->get_report_pengeluaran();
			$date_format = $report_pengeluaran['date_format'];
			$data['data_pengeluaran'] = $report_pengeluaran['data_pengeluaran'];
		}
		$data['date_format'] = $date_format;
		$this->load->view('administrator/page_report_pengeluaran', $data);
	}

	private function get_report_pengeluaran() {
		$this->check_hak_akses('report_pengeluaran');
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		if ($date_type == 'date') {
			$this->db->where('DATE_FORMAT(tanggal, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"');
			$date_format = date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo));
		} else {
			$this->db->where('DATE_FORMAT(tanggal, "%Y-%m") = "' . $month . '"');
			$date_format = date('m/Y', strtotime($month));
		}
		$data['date_format'] = $date_format;
		$data['data_pengeluaran'] = $this->db->select('DATE_FORMAT(tanggal, "%d-%m-%Y") AS tanggal, JB.nama AS jenis_biaya, catatan, nominal')
		->join('jenis_biaya JB', 'JB.id = pengeluaran.jenis_biaya_id', 'left')
		->order_by('tanggal', 'ASC')
		->get('pengeluaran')->result();
		return $data;
	}

	public function export_report_pengeluaran() {
		$this->check_hak_akses('report_pengeluaran');
		$report_pengeluaran = $this->get_report_pengeluaran();
		$date_type = $this->input->post('date_type');
		$date_format = $date_type == 'month' ? 'Bulan' : 'Tanggal';
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Laporan Pengeluaran');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Laporan Pengeluaran</h1>
		<h2>Per ' . $date_format . '</h2>
		<h2> ' . $report_pengeluaran['date_format'] . '</h2>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">No.</th>
		<th align="center">Tanggal</th>
		<th align="center">Jenis Biaya</th>
		<th align="center">Catatan</th>
		<th align="center">Nominal</th>
		</tr>
		</thead>
		<tbody>
		';
		$i = 1;
		$total = 0;
		foreach ($report_pengeluaran['data_pengeluaran'] as $result) {
			$html .= '
			<tr nobr="true">
			<td align="center">' . $i++ . '</td>
			<td align="center">' . $result->tanggal . '</td>
			<td>' . $result->jenis_biaya . '</td>
			<td>' . $result->catatan . '</td>
			<td>Rp ' . number_format($result->nominal, 0, ',', '.') . '</td>
			</tr>
			';
			$total += $result->nominal;
		}
		$html .= '
		</tbody>
		<tfoot>
		<tr>
		<th colspan="4" class="text-right">TOTAL</th>
		<th>Rp. ' . number_format($total, 0, '.', '.') . '</th>
		</tr>
		</tfoot>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Laporan Pengeluaran.pdf', 'I');
	}

	public function print_report_pengeluaran() {
		$this->check_hak_akses('report_pengeluaran');
		$report_pengeluaran = $this->get_report_pengeluaran();
		$date_type = $this->input->post('date_type');
		$date_format = $date_type == 'month' ? 'Bulan' : 'Tanggal';
		$html = '
		<head>
		<title>Laporan Pengeluaran ' . $report_pengeluaran['date_format'] . '</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h1>Laporan Pengeluaran</h1>
		<h2>Per ' . $date_format . '</h2>
		<h2> ' . $report_pengeluaran['date_format'] . '</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th align="center">No.</th>
		<th align="center">Tanggal</th>
		<th align="center">Jenis Biaya</th>
		<th align="center">Catatan</th>
		<th align="center">Nominal</th>
		</tr>
		</thead>
		<tbody>
		';
		$i = 1;
		$total = 0;
		foreach ($report_pengeluaran['data_pengeluaran'] as $result) {
			$html .= '
			<tr nobr="true">
			<td align="center">' . $i++ . '</td>
			<td align="center">' . $result->tanggal . '</td>
			<td>' . $result->jenis_biaya . '</td>
			<td>' . $result->catatan . '</td>
			<td>Rp ' . number_format($result->nominal, 0, ',', '.') . '</td>
			</tr>
			';
			$total += $result->nominal;
		}
		$html .= '
		</tbody>
		<tfoot>
		<tr>
		<th colspan="4" align="right">TOTAL</th>
		<th>Rp. ' . number_format($total, 0, '.', '.') . '</th>
		</tr>
		</tfoot>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function pembelian() {
		$this->check_hak_akses('pembelian');
		$this->session->unset_userdata('form_pembelian');
		$data['output'] = null;
		$data['form_pembelian'] = array(
			'purchase_date_type' => '',
			'payment_date_type'  => '',
			'purchaseDateFrom'   => '',
			'purchaseDateTo'     => '',
			'purchase_month'     => '',
			'no_invoice'         => '',
			'supplier'           => '',
			'product'            => '',
			'purchase_status'    => '',
			'payment_status'     => '',
			'paymentDateFrom'    => '',
			'paymentDateTo'      => '',
			'payment_month'      => '',
		);
		$data['title'] = 'Data Pembelian';
		$data['action'] = base_url('administrator/main/pembelian_process');
		$data['reset_link'] = base_url('administrator/main/pembelian');
		$this->load->view('administrator/page_pembelian', $data);
	}

	public function pembelian_process() {
		$purchase_date_type = $this->input->post('purchase_date_type');
		$payment_date_type = $this->input->post('payment_date_type');
		if ($purchase_date_type == 'date') {
			$this->form_validation->set_rules('purchaseDateFrom', 'Tanggal Pembelian', 'trim|required');
			$this->form_validation->set_rules('purchaseDateTo', 'Tanggal Pembelian', 'trim|required');
		} else {
			$this->form_validation->set_rules('purchase_month', 'Bulan Pembelian', 'trim|required');
		}

		if ($payment_date_type == 'date') {
			$this->form_validation->set_rules('paymentDateFrom', 'Tanggal Pembayaran', 'trim|required');
			$this->form_validation->set_rules('paymentDateTo', 'Tanggal Pembayaran', 'trim|required');
		} else if ($payment_date_type == 'month') {
			$this->form_validation->set_rules('payment_month', 'Bulan Pembayaran', 'trim|required');
		}

		if ($this->form_validation->run()) {
			$array = array(
				'purchase_date_type' => $this->input->post('purchase_date_type'),
				'payment_date_type'  => $this->input->post('payment_date_type'),
				'purchaseDateFrom'   => $this->input->post('purchaseDateFrom'),
				'purchaseDateTo'     => $this->input->post('purchaseDateTo'),
				'purchase_month'     => $this->input->post('purchase_month'),
				'no_invoice'         => $this->input->post('no_invoice'),
				'supplier'           => $this->input->post('supplier'),
				'product'            => $this->input->post('product'),
				'purchase_status'    => $this->input->post('purchase_status'),
				'payment_status'     => $this->input->post('payment_status'),
				'paymentDateFrom'    => $this->input->post('paymentDateFrom'),
				'paymentDateTo'      => $this->input->post('paymentDateTo'),
				'payment_month'      => $this->input->post('payment_month'),
			);
			$this->session->set_userdata(array('form_pembelian' => $array));
			redirect('administrator/main/result_pembelian', 'refresh');
		} else {
			$this->pembelian();
		}
	}

	private function get_pembelian() {
		$form_pembelian = $this->session->userdata('form_pembelian');
		$purchase_date_type = $form_pembelian['purchase_date_type'];
		$payment_date_type = $form_pembelian['payment_date_type'];
		$purchaseDateFrom = $form_pembelian['purchaseDateFrom'];
		$purchaseDateTo = $form_pembelian['purchaseDateTo'];
		$purchase_month = $form_pembelian['purchase_month'];
		$no_invoice = $form_pembelian['no_invoice'];
		$supplier = $form_pembelian['supplier'];
		$product = $form_pembelian['product'];
		$purchase_status = $form_pembelian['purchase_status'];
		$payment_status = $form_pembelian['payment_status'];
		$paymentDateFrom = $form_pembelian['paymentDateFrom'];
		$paymentDateTo = $form_pembelian['paymentDateTo'];
		$payment_month = $form_pembelian['payment_month'];
		$this->db->select('purchases.id, purchases.no_invoice, purchase_date, name_item, purchases.qty, purchases.total, nama_supplier, purchase_status, payment_status, payment_date, user_fullname')
		->from('purchases')
		->join('product', 'product.id = purchases.product_id', 'LEFT')
		->join('suppliers', 'suppliers.id = purchases.supplier_id', 'LEFT')
		->join('users', 'users.id = purchases.user_id', 'LEFT')
		->like('purchases.no_invoice', $no_invoice, 'BOTH')
		->like('suppliers.nama_supplier', $supplier, 'BOTH')
		->like('product.name_item', $product, 'BOTH')
		->where('payment_status', $payment_status)
		->where('purchase_status', $purchase_status);
		if ($purchase_date_type == 'date') {
			$this->db->where('DATE_FORMAT(purchase_date, "%Y-%m-%d") BETWEEN "' . $purchaseDateFrom . '" AND "' . $purchaseDateTo . '"');
		} else if ($purchase_date_type == 'month') {
			$this->db->where('DATE_FORMAT(purchase_date, "%Y-%m") = "' . $purchase_month . '"');
		}
		if ($payment_date_type == 'date') {
			$this->db->where('DATE_FORMAT(payment_date, "%Y-%m-%d") BETWEEN "' . $paymentDateFrom . '" AND "' . $paymentDateTo . '"');
		} else if ($payment_date_type == 'month') {
			$this->db->where('DATE_FORMAT(payment_date, "%Y-%m") = "' . $payment_month . '"');
		}
		return $this->db->get()->result();
	}

	public function result_pembelian() {
		$this->check_hak_akses('pembelian');
		$form_pembelian = $this->session->userdata('form_pembelian');
		$data['output'] = null;
		$data['action'] = base_url('administrator/main/pembelian_process');
		$data['reset_link'] = base_url('administrator/main/pembelian');
		$data['results'] = $this->get_pembelian();
		$data['form_pembelian'] = $form_pembelian;
		$this->load->view('administrator/page_pembelian_result', $data);
	}

	public function pembelian_update_payment() {
		$this->check_hak_akses('pembelian');
		$this->form_validation->set_rules('purchase_id', 'ID Pembelian', 'trim|required');
		$this->form_validation->set_rules('payment_status', 'Status Pembayaran', 'trim|required');
		if ($this->form_validation->run()) {
			$id = $this->input->post('purchase_id');
			$payment_status = $this->input->post('payment_status');
			if ($payment_status == 'Lunas') {
				$this->db->update('purchases', array(
					'payment_status' => 'Lunas',
					'payment_date'    => date('Y-m-d H:i:s')
				), array('id' => $id));
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success">Data pembelian berhasil disimpan</div>');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->result_pembelian();
		}
	}

	public function export_pembelian() {
		$this->check_hak_akses('pembelian');
		$results = $this->get_pembelian();
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Data Pembelian');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Data Pembelian</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">No. Invoice</th>
		<th align="center">Tanggal Pembelian</th>
		<th align="center">Nama Produk</th>
		<th align="center">Nama Supplier</th>
		<th align="center">Status Pembelian</th>
		<th align="center">Status Pembayaran</th>
		<th align="center">Tanggal Pembayaran</th>
		<th align="center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($results as $item) {
			if ($item->payment_date) {
				$payment_date = date('d-m-Y', strtotime($item->payment_date)) . '<br>' . date('H:i:s', strtotime($item->payment_date));
			} else {
				$payment_date = '<div align="center"><b>&ndash;</b></div>';
			}
			$html .= '
			<tr nobr="true">
			<td align="center">' . $item->no_invoice . '</td>
			<td>
			' . date('d-m-Y', strtotime($item->purchase_date)) . '<br>
			' . date('H:i:s', strtotime($item->purchase_date)) . '
			</td>
			<td>' . $item->name_item . '</td>
			<td>' . $item->nama_supplier . '</td>
			<td align="center">' . $item->purchase_status . '</td>
			<td align="center">' . $item->payment_status . '</td>
			<td>' . $payment_date . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Data Pembelian.pdf', 'I');
	}

	public function print_pembelian() {
		$this->check_hak_akses('pembelian');
		$results = $this->get_pembelian();
		$html = '
		<head>
		<title>Data Pembelian</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">Data Pembelian</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">No. Invoice</th>
		<th class="text-center">Tanggal Pembelian</th>
		<th class="text-center">Nama Produk</th>
		<th class="text-center">Nama Supplier</th>
		<th class="text-center">Status Pembelian</th>
		<th class="text-center">Status Pembayaran</th>
		<th class="text-center">Tanggal Pembayaran</th>
		<th class="text-center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($results as $item) {
			if ($item->payment_date) {
				$payment_date = date('d-m-Y', strtotime($item->payment_date)) . '<br>' . date('H:i:s', strtotime($item->payment_date));
			} else {
				$payment_date = '<div class="text-center"><b>&ndash;</b></div>';
			}
			$html .= '
			<tr nobr="true">
			<td class="text-center">' . $item->no_invoice . '</td>
			<td>
			' . date('d-m-Y', strtotime($item->purchase_date)) . '<br>
			' . date('H:i:s', strtotime($item->purchase_date)) . '
			</td>
			<td>' . $item->name_item . '</td>
			<td>' . $item->nama_supplier . '</td>
			<td class="text-center">' . $item->purchase_status . '</td>
			<td class="text-center">' . $item->payment_status . '</td>
			<td>' . $payment_date . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function report_pembelian() {
		$this->check_hak_akses('report_pembelian');
		$this->session->unset_userdata('form_pembelian');
		$data['output'] = null;
		$data['form_pembelian'] = array(
			'purchase_date_type' => '',
			'payment_date_type'  => '',
			'purchaseDateFrom'   => '',
			'purchaseDateTo'     => '',
			'purchase_month'     => '',
			'no_invoice'         => '',
			'supplier'           => '',
			'product'            => '',
			'purchase_status'    => '',
			'payment_status'     => '',
			'paymentDateFrom'    => '',
			'paymentDateTo'      => '',
			'payment_month'      => '',
		);
		$data['title'] = 'Laporan Pembelian';
		$data['action'] = base_url('administrator/main/report_pembelian_process');
		$data['reset_link'] = base_url('administrator/main/report_pembelian');
		$this->load->view('administrator/page_pembelian', $data);
	}

	public function report_pembelian_process() {
		$purchase_date_type = $this->input->post('purchase_date_type');
		$payment_date_type = $this->input->post('payment_date_type');
		if ($purchase_date_type == 'date') {
			$this->form_validation->set_rules('purchaseDateFrom', 'Tanggal Pembelian', 'trim|required');
			$this->form_validation->set_rules('purchaseDateTo', 'Tanggal Pembelian', 'trim|required');
		} else {
			$this->form_validation->set_rules('purchase_month', 'Bulan Pembelian', 'trim|required');
		}

		if ($payment_date_type == 'date') {
			$this->form_validation->set_rules('paymentDateFrom', 'Tanggal Pembayaran', 'trim|required');
			$this->form_validation->set_rules('paymentDateTo', 'Tanggal Pembayaran', 'trim|required');
		} else if ($payment_date_type == 'month') {
			$this->form_validation->set_rules('payment_month', 'Bulan Pembayaran', 'trim|required');
		}

		if ($this->form_validation->run()) {
			$array = array(
				'purchase_date_type' => $this->input->post('purchase_date_type'),
				'payment_date_type'  => $this->input->post('payment_date_type'),
				'purchaseDateFrom'   => $this->input->post('purchaseDateFrom'),
				'purchaseDateTo'     => $this->input->post('purchaseDateTo'),
				'purchase_month'     => $this->input->post('purchase_month'),
				'no_invoice'         => $this->input->post('no_invoice'),
				'supplier'           => $this->input->post('supplier'),
				'product'            => $this->input->post('product'),
				'purchase_status'    => $this->input->post('purchase_status'),
				'payment_status'     => $this->input->post('payment_status'),
				'paymentDateFrom'    => $this->input->post('paymentDateFrom'),
				'paymentDateTo'      => $this->input->post('paymentDateTo'),
				'payment_month'      => $this->input->post('payment_month'),
			);
			$this->session->set_userdata(array('form_pembelian' => $array));
			redirect('administrator/main/result_report_pembelian', 'refresh');
		} else {
			$this->report_pembelian();
		}
	}

	public function result_report_pembelian() {
		$this->check_hak_akses('report_pembelian');
		$form_pembelian = $this->session->userdata('form_pembelian');
		$data['output'] = null;
		$data['action'] = base_url('administrator/main/report_pembelian_process');
		$data['reset_link'] = base_url('administrator/main/report_pembelian');
		$data['results'] = $this->get_pembelian();
		$data['form_pembelian'] = $form_pembelian;
		$this->load->view('administrator/page_report_pembelian_result', $data);
	}

	public function export_report_pembelian() {
		$this->check_hak_akses('report_pembelian');
		$results = $this->get_pembelian();
		$this->load->library('tcpdf');
		$pdf = new Tcpdf('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Laporan Pembelian');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Laporan Pembelian</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">No.</th>
		<th align="center">Tanggal Pembelian</th>
		<th align="center">No. Invoice</th>
		<th align="center">Nama Produk</th>
		<th align="center">Nama Supplier</th>
		<th align="center">Qty</th>
		<th align="center">Total</th>
		<th align="center">Status Pembelian</th>
		<th align="center">Status Pembayaran</th>
		<th align="center">Tanggal Pembayaran</th>
		<th align="center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		$no = 1;
		foreach ($results as $item) {
			if ($item->payment_date) {
				$payment_date = date('d-m-Y', strtotime($item->payment_date)) . '<br>' . date('H:i:s', strtotime($item->payment_date));
			} else {
				$payment_date = '<div align="center"><b>&ndash;</b></div>';
			}
			$html .= '
			<tr nobr="true">
			<td align="center">' . $no++ . '</td>
			<td>
			' . date('d-m-Y', strtotime($item->purchase_date)) . '<br>
			' . date('H:i:s', strtotime($item->purchase_date)) . '
			</td>
			<td align="center">' . $item->no_invoice . '</td>
			<td>' . $item->name_item . '</td>
			<td>' . $item->nama_supplier . '</td>
			<td align="center">' . $item->qty . '</td>
			<td align="center">
			Rp. ' . number_format($item->total, 0, '.', '.') . '
			</td>
			<td align="center">' . $item->purchase_status . '</td>
			<td align="center">' . $item->payment_status . '</td>
			<td>' . $payment_date . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Laporan Pembelian.pdf', 'I');
	}

	public function print_report_pembelian() {
		$this->check_hak_akses('report_pembelian');
		$results = $this->get_pembelian();
		$html = '
		<head>
		<title>Laporan Pembelian</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">Laporan Pembelian</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">No.</th>
		<th class="text-center">Tanggal Pembelian</th>
		<th class="text-center">No. Invoice</th>
		<th class="text-center">Nama Produk</th>
		<th class="text-center">Nama Supplier</th>
		<th class="text-center">Qty</th>
		<th class="text-center">Total</th>
		<th class="text-center">Status Pembelian</th>
		<th class="text-center">Status Pembayaran</th>
		<th class="text-center">Tanggal Pembayaran</th>
		<th class="text-center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		$no = 1;
		foreach ($results as $item) {
			if ($item->payment_date) {
				$payment_date = date('d-m-Y', strtotime($item->payment_date)) . '<br>' . date('H:i:s', strtotime($item->payment_date));
			} else {
				$payment_date = '<div class="text-center"><b>&ndash;</b></div>';
			}
			$html .= '
			<tr nobr="true">
			<td class="text-center">' . $no++ . '</td>
			<td>
			' . date('d-m-Y', strtotime($item->purchase_date)) . '<br>
			' . date('H:i:s', strtotime($item->purchase_date)) . '
			</td>
			<td class="text-center">' . $item->no_invoice . '</td>
			<td>' . $item->name_item . '</td>
			<td>' . $item->nama_supplier . '</td>
			<td class="text-center">' . $item->qty . '</td>
			<td class="text-center">
			Rp. ' . number_format($item->total, 0, '.', '.') . '
			</td>
			<td class="text-center">' . $item->purchase_status . '</td>
			<td class="text-center">' . $item->payment_status . '</td>
			<td>' . $payment_date . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function laporan_autocancel() {
		$this->check_hak_akses('laporan_autocancel');
		$data['output'] = null;
		$this->load->view('administrator/page_laporan_autocancel', $data);
	}

	public function getLaporanAutocancel() {
		$data = $this->laporan_autocancel_result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	private function laporan_autocancel_result() {
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$sort = $this->input->post('sort');
		$sort = explode('-', $sort);
		$sort_arr = array(
			'kontribusi_keep'  => '(total_cancel + total_lunas)',
			'cancel'           => 'total_cancel',
			'persen_cancel'    => 'total_cancel',
			'kontribusi_lunas' => 'total_lunas',
			'lunas'            => 'total_lunas',
			'persen_lunas'     => 'total_lunas',
		);
		if (count($sort) > 1) {
			$sort_key = $sort_arr[$sort[0]];
			$sort_value = $sort[1];
		}
		$customer_type_id = $this->input->post('customer_type_id');

		if ($date_type == 'date') {
			$where = 'DATE_FORMAT(order_datetime, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
		} else {
			$where = 'DATE_FORMAT(order_datetime, "%Y-%m") = "' . $month . '"';
		}

		$this->db->select('C.id, C.name, CT.name AS customer_type,
			(SELECT COUNT(*) FROM orders_item_cancel WHERE customer_id = C.id AND ' . $where . ') AS total_cancel,
			(SELECT COUNT(*) FROM orders_item WHERE customer_id = C.id AND order_payment = "Paid" AND ' . $where . ') AS total_lunas')
		->from('customer C')
		->join('customer_type CT', 'CT.id = C.jenis_customer', 'left')
		->where_in('C.jenis_customer', $customer_type_id);
		if (count($sort) > 1) {
			$this->db->order_by($sort_key, $sort_value);
		}
		$results = $this->db->get()->result();
		$outputs = array();
		$all_cancel = 0;
		$all_lunas = 0;
		foreach ($results as $result) {
			if ($result->total_cancel > 0 || $result->total_lunas > 0) {
				$total_keep = $result->total_cancel + $result->total_lunas;
				$result->total_keep = $total_keep;
				$persen_cancel = ($result->total_cancel / $total_keep) * 100;
				$persen_lunas = ($result->total_lunas / $total_keep) * 100;
				$result->persen_cancel = round($persen_cancel, 2) . '%';
				$result->persen_lunas = round($persen_lunas, 2) . '%';
				array_push($outputs, $result);
				$all_cancel += $result->total_cancel;
				$all_lunas += $result->total_lunas;
			}
		}
		$all_keep = $all_cancel + $all_lunas;
		foreach ($outputs as $key => $output) {
			$kontribusi_keep = ($outputs[$key]->total_keep / $all_keep) * 100;
			$outputs[$key]->kontribusi_keep = round($kontribusi_keep, 2) . '%';
			$kontribusi_lunas = ($outputs[$key]->total_lunas / $all_lunas) * 100;
			$outputs[$key]->kontribusi_lunas = round($kontribusi_lunas, 2) . '%';
		}
		return array(
			'results'    => $outputs,
			'all_keep'   => $all_keep,
			'all_cancel' => $all_cancel,
			'all_lunas'  => $all_lunas
		);
	}

	public function export_laporan_autocancel() {
		$this->check_hak_akses('laporan_autocancel');
		$data = $this->laporan_autocancel_result();
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Laporan Autocancel');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Laporan Autocancel</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">Nama</th>
		<th align="center">Jenis Customer</th>
		<th align="center">Total Keep</th>
		<th align="center">Jumlah Cancel</th>
		<th align="center">Persen Cancel</th>
		<th align="center">Jumlah Lunas</th>
		<th align="center">Persen Lunas</th>
		<th align="center">Kontribusi Keep</th>
		<th align="center">Kontribusi Lunas</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($data['results'] as $item) {
			$html .= '
			<tr nobr="true">
			<td>' . $item->name . ' (' . $item->id . ')</td>
			<td align="center">' . $item->customer_type . '</td>
			<td align="center">' . $item->total_keep . '</td>
			<td align="center">' . $item->total_cancel . '</td>
			<td align="center">' . $item->persen_cancel . '</td>
			<td align="center">' . $item->total_lunas . '</td>
			<td align="center">' . $item->persen_lunas . '</td>
			<td align="center">' . $item->kontribusi_keep . '</td>
			<td align="center">' . $item->kontribusi_lunas . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		<tfoot>
		<tr>
		<th align="center" colSpan="2">GRAND TOTAL</th>
		<th align="center">' . $data['all_keep'] . '</th>
		<th align="center">' . $data['all_cancel'] . '</th>
		<th align="center"></th>
		<th align="center">' . $data['all_lunas'] . '</th>
		<th align="center"></th>
		<th align="center"></th>
		<th align="center"></th>
		</tr>
		</tfoot>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Laporan Autocancel.pdf', 'I');
	}

	public function print_laporan_autocancel() {
		$this->check_hak_akses('laporan_autocancel');
		$data = $this->laporan_autocancel_result();
		$html = '
		<head>
		<title>Laporan Autocancel</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">Laporan Autocancel</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">Nama</th>
		<th class="text-center">Jenis Customer</th>
		<th class="text-center">Total Keep</th>
		<th class="text-center">Jumlah Cancel</th>
		<th class="text-center">Persen Cancel</th>
		<th class="text-center">Jumlah Lunas</th>
		<th class="text-center">Persen Lunas</th>
		<th class="text-center">Kontribusi Keep</th>
		<th class="text-center">Kontribusi Lunas</th>
		</tr>
		</thead>
		<tbody>
		';
		foreach ($data['results'] as $item) {
			$html .= '
			<tr nobr="true">
			<td>' . $item->name . ' (' . $item->id . ')</td>
			<td class="text-center">' . $item->customer_type . '</td>
			<td class="text-center">' . $item->total_keep . '</td>
			<td class="text-center">' . $item->total_cancel . '</td>
			<td class="text-center">' . $item->persen_cancel . '</td>
			<td class="text-center">' . $item->total_lunas . '</td>
			<td class="text-center">' . $item->persen_lunas . '</td>
			<td class="text-center">' . $item->kontribusi_keep . '</td>
			<td class="text-center">' . $item->kontribusi_lunas . '</td>
			</tr>
			';
		}
		$html .= '
		<tr>
		<th class="text-center" colSpan="2">GRAND TOTAL</th>
		<th class="text-center">' . $data['all_keep'] . '</th>
		<th class="text-center">' . $data['all_cancel'] . '</th>
		<th class="text-center"></th>
		<th class="text-center">' . $data['all_lunas'] . '</th>
		<th class="text-center"></th>
		<th class="text-center"></th>
		<th class="text-center"></th>
		</tr>
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function report_piutang() {
		$this->check_hak_akses('report_piutang');
		$data['output'] = null;
		$this->load->view('administrator/page_report_piutang', $data);
	}

	private function report_piutang_result() {
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$order_id = $this->input->post('order_id');
		$customer = $this->input->post('customer');
		$product = $this->input->post('product');

		if ($date_type == 'date') {
			$where = 'DATE_FORMAT(OI.order_datetime, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
		} else {
			$where = 'DATE_FORMAT(OI.order_datetime, "%Y-%m") = "' . $month . '"';
		}

		$this->db->select('OI.id, OI.order_datetime, OI.order_id, C.name AS customer, P.name_item, OI.qty, OI.subtotal, OI.order_payment, U.user_fullname')
		->from('orders_item OI')
		->join('customer C', 'C.id = OI.customer_id', 'left')
		->join('orders O', 'O.id = OI.order_id', 'left')
		->join('product P', 'P.id = OI.prod_id', 'left')
		->join('users U', 'U.id = O.user_id', 'left')
		->where($where)
		->where('O.order_status', 'Piutang')
		->like('order_id', $order_id, 'BOTH')
		->like('C.name', $customer, 'BOTH')
		->like('P.name_item', $product, 'BOTH')
		->order_by('OI.id', 'DESC');
		return $this->db->get()->result();
	}

	public function getReportPiutang() {
		$data = $this->report_piutang_result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function export_report_piutang() {
		$this->check_hak_akses('report_piutang');
		$results = $this->report_piutang_result();
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Laporan Piutang');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Laporan Piutang</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">No</th>
		<th align="center">Tanggal Pesanan</th>
		<th align="center">ID Pesanan</th>
		<th align="center">Nama Pelanggan</th>
		<th align="center">Nama Produk</th>
		<th align="center">Qty</th>
		<th align="center">Total</th>
		<th align="center">Status Pesanan</th>
		<th align="center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		$no = 1;
		foreach ($results as $item) {
			$html .= '
			<tr nobr="true">
			<td align="center">' . $no++ . '</td>
			<td>' . $item->order_datetime . '</td>
			<td align="center">' . $item->order_id . '</td>
			<td>' . $item->customer . '</td>
			<td>' . $item->name_item . '</td>
			<td align="center">' . $item->qty . '</td>
			<td>Rp. ' . number_format($item->subtotal, 0, '.', '.') . '</td>
			<td align="center">' . $item->order_payment . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Laporan Piutang.pdf', 'I');
	}

	public function print_report_piutang() {
		$this->check_hak_akses('report_piutang');
		$results = $this->report_piutang_result();
		$html = '
		<head>
		<title>Laporan Piutang</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">Laporan Piutang</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">No</th>
		<th class="text-center">Tanggal Pesanan</th>
		<th class="text-center">ID Pesanan</th>
		<th class="text-center">Nama Pelanggan</th>
		<th class="text-center">Nama Produk</th>
		<th class="text-center">Qty</th>
		<th class="text-center">Total</th>
		<th class="text-center">Status Pesanan</th>
		<th class="text-center">User</th>
		</tr>
		</thead>
		<tbody>
		';
		$no = 1;
		foreach ($results as $item) {
			$html .= '
			<tr nobr="true">
			<td class="text-center">' . $no++ . '</td>
			<td>' . $item->order_datetime . '</td>
			<td class="text-center">' . $item->order_id . '</td>
			<td>' . $item->customer . '</td>
			<td>' . $item->name_item . '</td>
			<td class="text-center">' . $item->qty . '</td>
			<td>Rp. ' . number_format($item->subtotal, 0, '.', '.') . '</td>
			<td class="text-center">' . $item->order_payment . '</td>
			<td>' . $item->user_fullname . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function report_stock() {
		$this->check_hak_akses('report_stock');
		$data['output'] = null;
		$this->load->view('administrator/page_report_stock', $data);
	}

	private function report_stock_result() {
		$dateFrom = $this->input->post('dateFrom');
		$dateTo = $this->input->post('dateTo');
		$date_type = $this->input->post('date_type');
		$month = $this->input->post('month');
		$user = $this->input->post('user');
		$userApprove = $this->input->post('userApprove');
		$product = $this->input->post('product');

		if ($date_type == 'date') {
			$where = 'DATE_FORMAT(SH.created_at, "%Y-%m-%d") BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"';
		} else {
			$where = 'DATE_FORMAT(SH.created_at, "%Y-%m") = "' . $month . '"';
		}

		$this->db->select('SH.created_at, P.name_item, PV.variant, SH.prev_stock, SH.stock, SH.qty, U.user_fullname, SH.note, users.user_fullname AS user_approve')
		->from('stock_histories SH')
		->join('product_variant PV', 'PV.id = SH.variant_id', 'left')
		->join('product P', 'P.id = SH.prod_id', 'left')
		->join('users U', 'U.id = SH.user_id', 'left')
		->join('users', 'users.id = SH.user_approve_id', 'left')
		->where($where)
		->like('U.user_fullname', $user, 'BOTH')
		->like('P.name_item', $product, 'BOTH')
		->order_by('SH.created_at', 'ASC');
		return $this->db->get()->result();
	}

	public function getReportStock() {
		$data = $this->report_stock_result();
		$this->output->set_content_type('application/json')
		->set_output(json_encode($data));
	}

	public function export_report_stock() {
		$this->check_hak_akses('report_stock');
		$results = $this->report_stock_result();
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle('Record Data Perubahan');
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$html = '<h1>Record Data Perubahan</h1>';
		$html .= '
		<table border="1" cellpadding="5">
		<thead>
		<tr>
		<th align="center">No</th>
		<th align="center">Waktu Perubahan</th>
		<th align="center">Produk</th>
		<th align="center">Variant</th>
		<th align="center">Qty Awal</th>
		<th align="center">Qty</th>
		<th align="center">Qty Akhir</th>
		<th align="center">User</th>
		<th align="center">Catatan</th>
		<th align="center">User Approve</th>
		</tr>
		</thead>
		<tbody>
		';
		$no = 1;
		foreach ($results as $item) {
			$date = date('d-m-Y', strtotime($item->created_at));
			$date .= '<br>'. date('H:i:s', strtotime($item->created_at));
			$mark = $item->qty > 0 ? '+' : '';
			$html .= '
			<tr nobr="true">
			<td align="center">' . $no++ . '</td>
			<td>' . $date . '</td>
			<td>' . $item->name_item . '</td>
			<td>' . $item->variant . '</td>
			<td align="center">' . $item->prev_stock . '</td>
			<td align="center">' . $mark . $item->qty . '</td>
			<td align="center">' . $item->stock . '</td>
			<td>' . $item->user_fullname . '</td>
			<td>' . $item->note . '</td>
			<td>' . $item->user_approve . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		';
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('Record Data Perubahan.pdf', 'I');
	}

	public function print_report_stock() {
		$this->check_hak_akses('report_stock');
		$results = $this->report_stock_result();
		$html = '
		<head>
		<title>Record Data Perubahan</title>
		<link href="'. base_url('application/views/administrator/assets/css/bootstrap.css') .'" rel="stylesheet">
		</head>
		<h2 class="text-center">Record Data Perubahan</h2>';
		$html .= '
		<table class="table table-bordered" cellpadding="5">
		<thead>
		<tr>
		<th class="text-center">No</th>
		<th class="text-center">Waktu Perubahan</th>
		<th class="text-center">Produk</th>
		<th class="text-center">Variant</th>
		<th class="text-center">Qty Awal</th>
		<th class="text-center">Qty</th>
		<th class="text-center">Qty Akhir</th>
		<th class="text-center">User</th>
		<th class="text-center">Catatan</th>
		<th class="text-center">User Approve</th>
		</tr>
		</thead>
		<tbody>
		';
		$no = 1;
		foreach ($results as $item) {
			$date = date('d-m-Y', strtotime($item->created_at));
			$date .= '<br>'. date('H:i:s', strtotime($item->created_at));
			$mark = $item->qty > 0 ? '+' : '';
			$html .= '
			<tr nobr="true">
			<td align="center">' . $no++ . '</td>
			<td>' . $date . '</td>
			<td>' . $item->name_item . '</td>
			<td>' . $item->variant . '</td>
			<td align="center">' . $item->prev_stock . '</td>
			<td align="center">' . $mark . $item->qty . '</td>
			<td align="center">' . $item->stock . '</td>
			<td>' . $item->user_fullname . '</td>
			<td>' . $item->note . '</td>
			<td>' . $item->user_approve . '</td>
			</tr>
			';
		}
		$html .= '
		</tbody>
		</table>
		<script>window.print()</script>
		';
		echo $html;
	}

	public function report_neraca() {
		$this->check_hak_akses('report_neraca');
		$data['output'] = null;
		$this->load->view('administrator/page_report_neraca', $data);
	}

	public function report_neraca_export() {
		$data = $this->get_neraca_data();
		$html = $this->load->view('administrator/page_report_neraca_process', $data, TRUE);
		$month = $this->input->post('month');
		$title = 'Laporan Neraca ' . date('M Y', strtotime($month));
		$this->load->library('tcpdf');
		$pdf = new Tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetTitle($title);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output($title . '.pdf', 'I');
	}

	public function report_neraca_process() {
		$data = $this->get_neraca_data();
		$this->load->view('administrator/page_report_neraca_process', $data);
	}

	public function insertModal($month) {
		$this->db->select('OI.id, OI.qty, P.price_production')
		->from('orders_item OI')
		->join('orders O', 'O.id = OI.order_id', 'left')
		->join('product P', 'P.id = OI.prod_id', 'left')
		->where('DATE_FORMAT(O.order_datetime, "%Y-%m") = "' . $month . '"')
		->where('O.order_payment', 'Paid')
		->where('O.order_status !=', 'Cancel')
		->where('OI.subtotal_modal', 0);
		$orders_items = $this->db->get()->result();

		foreach ($orders_items as $item) {
			$data = array(
				'subtotal_modal' => $item->price_production * $item->qty,
			);
			$where = array('id' => $item->id);
			$this->db->update('orders_item', $data, $where);
		}
	}

	private function stockAwal($month) {
		$prev_month = date('Y-m', strtotime($month .  ' - 1 month'));
		$neraca = $this->db->where('DATE_FORMAT(date, "%Y-%m") = "' . $prev_month . '"')
		->get('neraca')->row_array();
		if (empty($neraca)) {
			$assets = $this->db->select('P.price_production * SUM(PV.stock) AS total')
			->from('product P')
			->join('product_category PC', 'P.category_id = PC.id', 'left')
			->join('product_variant PV', 'P.id = PV.prod_id', 'left')
			->where('P.status', 'Publish')
			->where('PC.status_category', 'publish')
			->where('PV.available', 'Tersedia')
			->group_by('P.id')
			->get()->result();
			$total_assets = 0;
			foreach ($assets as $asset) {
				$total_assets += $asset->total;
			}

			$purchase = $this->db->select('SUM(total) AS grand_total')
			->where('DATE_FORMAT(purchase_date, "%Y-%m") >= "' . $month . '"')
			->get('purchases')->row_array();

			$this->db->select('SUM((P.price_production * OI.qty)) AS total_modal')
			->join('orders O', 'OI.order_id = O.id', 'left')
			->join('customer C', 'OI.customer_id = C.id', 'left')
			->join('product P', 'OI.prod_id = P.id', 'left')
			->where('DATE_FORMAT(O.date_payment,"%Y-%m") >= "' . $month . '"')
			->where('O.order_status !=', 'Cancel')
			->where('OI.order_status !=', 'Cancel')
			->where('O.order_payment', 'Paid');
			$hpp = $this->db->get('orders_item OI')->row_array();

			$stock_akhir = $total_assets - $purchase['grand_total'] + $hpp['total_modal'];
			$this->db->insert('neraca', array(
				'date'        => $prev_month . '-01',
				'stock_akhir' => $stock_akhir,
			));
			return $stock_akhir;
		} else {
			return $neraca['stock_akhir'];
		}
	}

	public function get_neraca_data() {
		$month = $this->input->post('month');
		$month = date('Y-m', strtotime($month));

		$persediaan_awal = $this->stockAwal($month);

		$this->insertModal($month);

		$this->db->select('customer_type.name AS customer_type, SUM(OI.subtotal) AS nominal, SUM(qty) AS total_qty')
		->from('orders_item OI')
		->join('customer', 'customer.id = OI.customer_id', 'left')
		->join('customer_type', 'customer.jenis_customer = customer_type.id', 'left')
		->join('orders O', 'O.id = OI.order_id', 'left')
		->where('DATE_FORMAT(O.order_datetime, "%Y-%m") = "' . $month . '"')
		->where('O.order_payment', 'Paid')
		->where('O.order_status !=', 'Cancel')
		->group_by('customer.jenis_customer')
		->order_by('customer.jenis_customer', 'ASC');
		$sales = $this->db->get()->result();

		$total_qty_sales = 0;
		$total_nominal_sales = 0;
		foreach ($sales as $sale) {
			$total_qty_sales += $sale->total_qty;
			$total_nominal_sales += $sale->nominal;
		}

		$this->db->select('jenis_biaya.nama AS jenis_biaya, SUM(nominal) AS total_nominal')
		->from('pengeluaran')
		->join('jenis_biaya', 'jenis_biaya.id = pengeluaran.jenis_biaya_id')
		->where('DATE_FORMAT(tanggal, "%Y-%m") = "' . $month . '"')
		->group_by('jenis_biaya_id')
		->order_by('jenis_biaya_id', 'ASC');
		$pengeluaran = $this->db->get()->result();

		$purchase = $this->db->select('SUM(total) AS grand_total, SUM(qty) AS total_qty')
		->where('DATE_FORMAT(purchase_date, "%Y-%m") = "' . $month . '"')
		->get('purchases')->row_array();

		$this->db->select('SUM((P.price_production * OI.qty)) AS total_modal')
		->join('orders O', 'OI.order_id = O.id', 'left')
		->join('customer C', 'OI.customer_id = C.id', 'left')
		->join('product P', 'OI.prod_id = P.id', 'left')
		->where('DATE_FORMAT(O.date_payment,"%Y-%m-%d") = "' . $month . '"')
		->where('O.order_status !=', 'Cancel')
		->where('OI.order_status !=', 'Cancel')
		->where('O.order_payment', 'Paid');
		$hpp = $this->db->get('orders_item OI')->row_array();

		$hutang = $this->db->select('SUM(total) AS grand_total, SUM(qty) AS total_qty')
		->where('DATE_FORMAT(purchase_date, "%Y-%m") = "' . $month . '"')
		->where('payment_status', 'Belum Lunas')
		->get('purchases')->row_array();

		$piutang = $this->db->select('SUM(OI.subtotal) AS nominal, SUM(qty) AS total_qty')
		->join('orders O', 'O.id = OI.order_id', 'left')
		->where('DATE_FORMAT(O.order_datetime, "%Y-%m") = "' . $month . '"')
		->where('O.order_status', 'Piutang')
		->where('O.order_payment', 'Unpaid')
		->get('orders_item OI')->row_array();

		$modal = $this->db->select_sum('subtotal_modal', 'total')
		->join('orders O', 'O.id = OI.order_id', 'left')
		->where('DATE_FORMAT(O.order_datetime, "%Y-%m") = "' . $month . '"')
		->where('O.order_payment', 'Paid')
		->where('O.order_status !=', 'Cancel')
		->get('orders_item OI')->row_array();

		$stock_end = $persediaan_awal + $purchase['grand_total'] - $hpp['total_modal'];

		$data = array(
			'month'               => date('M Y', strtotime($month)),
			'sales'               => $sales,
			'total_qty_sales'     => $total_qty_sales,
			'total_nominal_sales' => $total_nominal_sales,
			'pengeluaran'         => $pengeluaran,
			'purchase'            => $purchase,
			'hutang'              => $hutang,
			'piutang'             => $piutang,
			'modal'               => $modal['total'],
			'stock_end'           => $stock_end,
			'persediaan_awal'     => $persediaan_awal,
		);
		return $data;
	}

	public function cara_order() {
		$this->check_hak_akses('cara_order');
		$crud = new grocery_CRUD();
		$crud->set_table('cara_order')
		->set_subject('Cara Order')
		->unset_read()
		->unset_export()
		->unset_print()
		->display_as('title', 'Judul')
		->display_as('content', 'Konten')
		->order_by('id', 'DESC');
		$output = $crud->render();
		$data['output'] = $output;
		$this->load->view('administrator/page_cara_order', $data);
	}

	public function update_cara_order() {
		$videos = $this->input->post('videos');
		$this->db->delete('cara_order', array('type' => 'Video'));
		foreach ($videos as $video) {
			$cara_order = array(
				'type' => 'Video',
				'link' => $video
			);
			$this->db->insert('cara_order', $cara_order);
		}
		redirect('administrator/main/cara_order', 'refresh');
	}

	public function get_image_cara_order() {
		$cara_order = $this->db->order_by('id', 'ASC')
		->get_where('cara_order', array('type' => 'Image'))->result();
		foreach ($cara_order as $row) {
			if ($row->link) {
				$data[] = array(
					'name'         => $row->link,
					'uuid'         => $row->id . '#' . $row->link,
					'thumbnailUrl' => base_url('media/images/' . $row->link)
				);
			}
		}

		echo json_encode($data);
	}

	public function upload_image_cara_order() {

		/*upload image logo */
		$config['upload_path'] = './media/images';
		$config['allowed_types'] = '*';

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('qqfile')) {
			$error = array('error' => $this->upload->display_errors());
			echo $error['error'];
		} else {
			$data = $this->upload->data();
			$cara_order = array(
				'type' => 'Image',
				'link' => $data['file_name']
			);
			$this->db->insert('cara_order', $cara_order);
			$resp = array(
				'success' => true,
				'data'    => $data['file_name'],
			);
			echo json_encode($resp);
		}
	}

	public function delete_image_cara_order() {
		$filename = $this->input->post('filename');
		if ($filename == 'undefined') {
			$qquuid = $this->input->post('qquuid');
			$filename = explode('#', $qquuid)[1];
		}
		$this->db->delete('cara_order', array('type' => 'Image', 'link' => $filename));
		unlink('./media/images/' . $filename);
		$resp = array('success' => true);
		echo json_encode($resp);
	}

	public function cities($province_id = NULL) {
		if ($province_id) {
			$this->db->where('province_id', $province_id);
		}
		$cities = $this->db->get('cities')->result();
		echo json_encode($cities);
	}

	public function subdistricts($city_id = NULL) {
		if ($city_id) {
			$this->db->where('city_id', $city_id);
		}
		$subdistricts = $this->db->get('subdistricts')->result();
		echo json_encode($subdistricts);
	}

	public function name_tag() {
		$this->check_hak_akses('name_tag');
		$data['tags'] = $this->db->get('name_tag')->result();
		$data['output'] = null;
		$this->load->view('administrator/page_name_tag', $data);
	}

	function delete_tag($id) {
		$this->check_hak_akses('name_tag');
		if ($id == 1) {
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Tag ini tidak boleh dihapus !</div>');
		} else {
			$delete = array('id' => $id);
			$this->db->delete('name_tag', $delete);
			$this->db->delete('product_tags', array('tag_id' => $id));
			$this->db->query('ALTER TABLE product_tags AUTO_INCREMENT = 0');
			$this->session->set_flashdata('message','<div class="alert alert-success">Tag berhasil dihapus</div>');
		}
		redirect('administrator/main/name_tag');
	}

	function save_tag() {
		$this->check_hak_akses('name_tag');
		$this->form_validation->set_rules('name_tag', 'Name Tag', 'trim|required');
		if ($this->form_validation->run()) {
			$data['name'] = $this->input->post('name_tag');
			$this->db->insert('name_tag', $data);
			$this->session->set_flashdata('message','<div class="alert alert-success">Tag berhasil disimpan</div>');
			redirect('administrator/main/name_tag');
		} else {
			$this->name_tag();
		}
	}

	function update_tag() {
		$this->check_hak_akses('name_tag');
		$this->form_validation->set_rules('edit_name_tag', 'Name Tag', 'trim|required');
		$this->form_validation->set_rules('id', 'ID Tag', 'trim|required');
		if ($this->form_validation->run()) {
			$id = $this->input->post('id');
			$data['name'] = $this->input->post('edit_name_tag');
			$this->db->update('name_tag', $data, array('id' => $id));
			$this->session->set_flashdata('message','<div class="alert alert-success">Tag berhasil diubah</div>');
			redirect('administrator/main/name_tag');
		} else {
			$this->name_tag();
		}
	}

	public function discount() {
		$this->check_hak_akses('discount');
		$discounts = $this->db->select('id, title, active')->get('discounts')->result();
		$data = array(
			'output'    => null,
			'discounts' => $discounts,
		);

		$this->load->view('administrator/page_discount', $data);
	}

	public function add_discount() {
		$this->form_validation->set_rules('title', 'Judul', 'trim|required');
		$this->form_validation->set_rules('from_date', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('to_date', 'Tanggal Berakhir', 'trim|required');
		$this->form_validation->set_rules('min_qty', 'Qty Minimal', 'trim|required');
		$this->form_validation->set_rules('max_qty', 'Qty Maksimal', 'trim|required');
		$this->form_validation->set_rules('discount_type', 'Tipe Diskon', 'trim|required');
		$this->form_validation->set_rules('amount', 'Jumlah Diskon', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');

		if ($this->form_validation->run()) {
			$post = $this->input->post();
			$customer_types = implode('|', $post['customer_types']);
			$name_tags = implode('|', $post['name_tags']);
			$categories = implode('|', $post['categories']);
			$data = array(
				'customer_types'     => count($customer_types) > 0 ? $customer_types : '',
				'product_tags'       => count($name_tags) > 0 ? $name_tags : '',
				'product_categories' => count($categories) > 0 ? $categories : '',
				'title'              => $post['title'],
				'from_date'          => $post['from_date'],
				'to_date'            => $post['to_date'],
				'min_qty'            => $post['min_qty'],
				'max_qty'            => $post['max_qty'],
				'discount_type'      => $post['discount_type'],
				'amount'             => $post['amount'],
				'active'             => $post['status'],
			);
			$this->db->insert('discounts', $data);
			$id = $this->db->insert_id();
			if ($post['status'] == 1) {
				$this->db->update('discounts', array('active' => 0), array('id !=' => $id));
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil di disimpan</div>');
			redirect('administrator/main/discount', 'refresh');
		} else {
			$categories = $this->db->get_where('product_category', array('status_category' => 'publish'));
			$name_tags = $this->db->get_where('name_tag', array('id !=' => 1));
			$data = array(
				'output'                    => null,
				'customer_types'            => $this->db->get('customer_type')->result(),
				'name_tags'                 => $name_tags->result(),
				'categories'                => $categories->result(),
				'title'                     => set_value('title'),
				'from_date'                 => set_value('from_date'),
				'to_date'                   => set_value('to_date'),
				'min_qty'                   => set_value('min_qty'),
				'max_qty'                   => set_value('max_qty'),
				'customer_type_discount'    => [],
				'tag_discount'              => [],
				'product_category_discount' => [],
				'discount_type'             => set_value('discount_type'),
				'amount'                    => set_value('amount'),
				'status'                    => set_value('status'),
			);
			$this->load->view('administrator/page_form_discount', $data);
		}
	}

	public function update_discount_status($id, $active) {
		$this->db->update('discounts', array('active' => !$active), array('id' => $id));
		if ($active == 0) {
			$this->db->update('discounts', array('active' => 0), array('id !=' => $id));
		}
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil di diubah</div>');
		redirect('administrator/main/discount', 'refresh');
	}

	public function edit_discount($id) {
		$this->form_validation->set_rules('title', 'Judul', 'trim|required');
		$this->form_validation->set_rules('from_date', 'Tanggal Mulai', 'trim|required');
		$this->form_validation->set_rules('to_date', 'Tanggal Berakhir', 'trim|required');
		$this->form_validation->set_rules('min_qty', 'Qty Minimal', 'trim|required');
		$this->form_validation->set_rules('max_qty', 'Qty Maksimal', 'trim|required');
		$this->form_validation->set_rules('discount_type', 'Tipe Diskon', 'trim|required');
		$this->form_validation->set_rules('amount', 'Jumlah Diskon', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');

		if ($this->form_validation->run()) {
			$post = $this->input->post();
			$customer_types = !empty($post['customer_types']) ? $post['customer_types'] : [];
			$name_tags = !empty($post['name_tags']) ? $post['name_tags'] : [];
			$categories = !empty($post['categories']) ? $post['categories'] : [];
			$data = array(
				'customer_types'     => implode('|', $customer_types),
				'product_tags'       => implode('|', $name_tags),
				'product_categories' => implode('|', $categories),
				'title'              => $post['title'],
				'from_date'          => $post['from_date'],
				'to_date'            => $post['to_date'],
				'min_qty'            => $post['min_qty'],
				'max_qty'            => $post['max_qty'],
				'discount_type'      => $post['discount_type'],
				'amount'             => $post['amount'],
				'active'             => $post['status'],
			);
			$this->db->update('discounts', $data, array('id' => $id));
			if ($post['status'] == 1) {
				$this->db->update('discounts', array('active' => 0), array('id !=' => $id));
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil di disimpan</div>');
			redirect('administrator/main/discount', 'refresh');
		} else {
			$discount = $this->db->get_where('discounts', array('id' => $id))->row_array();
			$categories = $this->db->get_where('product_category', array('status_category' => 'publish'));
			$name_tags = $this->db->get_where('name_tag', array('id !=' => 1));
			$data = array(
				'output'                    => null,
				'customer_types'            => $this->db->get('customer_type')->result(),
				'name_tags'                 => $name_tags->result(),
				'categories'                => $categories->result(),
				'title'                     => $discount['title'],
				'from_date'                 => $discount['from_date'],
				'to_date'                   => $discount['to_date'],
				'min_qty'                   => $discount['min_qty'],
				'max_qty'                   => $discount['max_qty'],
				'customer_type_discount'    => explode('|', $discount['customer_types']),
				'tag_discount'              => explode('|', $discount['product_tags']),
				'product_category_discount' => explode('|', $discount['product_categories']),
				'discount_type'             => $discount['discount_type'],
				'amount'                    => $discount['amount'],
				'status'                    => $discount['active'],
			);
			$this->load->view('administrator/page_form_discount', $data);
		}
	}

	public function delete_discount($id) {
		$this->db->delete('discounts', array('id' => $id));
		$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil di dihapus</div>');
		redirect('administrator/main/discount', 'refresh');
	}

	public function point_reward() {
		$this->check_hak_akses('point_reward');
		$point_reward_status = $this->main_model->get_detail('content', array('name' => 'point_reward_status'));
		$nominal_to_point = $this->main_model->get_detail('content', array('name' => 'nominal_to_point'));
		$point_to_nominal = $this->main_model->get_detail('content', array('name' => 'point_to_nominal'));
		$expired_point = $this->main_model->get_detail('content',array('name' => 'expired_point'));

		$data = array(
			'output'              => null,
			'point_reward_status' => $point_reward_status['value'],
			'nominal_to_point'    => $nominal_to_point['value'],
			'point_to_nominal'    => $point_to_nominal['value'],
			'expired_point'    	  => $expired_point['value'],
		);

		$this->load->view('administrator/page_point_reward', $data);
	}

	public function update_point_reward() {
		$this->check_hak_akses('point_reward');

		if(isset($_POST['submitreset']))
		{
			$data_update = array('point' => 0);
			$where = array('status' => 'Active');
			$this->db->update('customer',$data_update,$where);

			$this->session->set_flashdata('message', '<div class="alert alert-danger">Point Berhasil diReset</div>');
			redirect('administrator/main/point_reward', 'refresh');
		}else{

			$post = $this->input->post();
			$this->db->where('name', 'point_reward_status')
			->update('content', array('value' => $post['point_reward_status']));

			$this->db->where('name', 'nominal_to_point')
			->update('content', array('value' => $post['nominal_to_point']));

			$this->db->where('name', 'point_to_nominal')
			->update('content', array('value' => $post['point_to_nominal']));

			$this->db->where('name', 'expired_point')
			->update('content', array('value' => $post['expired_point']));

			$this->session->set_flashdata('message', '<div class="alert alert-success">Data berhasil di update</div>');
			redirect('administrator/main/point_reward', 'refresh');

		}


	}

	public function delete_background() {
		$where = array('name' => 'login_background_image');
		$login_background_image = $this->main_model->get_detail('content', $where);
		$file = './media/images/' . $login_background_image['value'];
		if (file_exists($file)) {
			unlink($file);
		}
		$this->db->update('content', array('value' => ''), $where);
	}
	
//UPDATE NEW VERSION 2

	function restok_stok($offset = 0){
		$data['output'] = null;

		$this->db->select('*');
		$this->db->join('product', 'product.id = product_variant.prod_id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$data_total = $this->db->get('product_variant');
		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}

		$this->load->library('pagination');

		$url = 'restok_stok';
		$uri = 4;

		$this->pagging_setting($url,$perpage,$data_total->num_rows(),$uri);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$this->db->join('product_variant', 'prod_id = product.id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->order_by('product.id', 'DESC');
						// $data['list_item'] = $this->db->get('product');

		$data['list_item'] = $this->main_model->get_list('product', array('perpage' => $perpage, 'offset' => $offset),array('by' => 'product.id', 'sorting' => 'DESC'));

		$this->load->view('administrator/page_restok_stock',$data);
	}

					//Pagination function
	function pagging_setting($url = null, $perpage = null, $rows = null, $uri = null)
	{
		$config = array (

			'base_url' => base_url().'administrator/main/'.$url,

			'per_page' => $perpage,

			'total_rows' => $rows,

			'full_tag_open' => '<div class="pagination-wrapper text-right"> <div class="pagination">',

			'full_tag_close' => '</div></div>',

			'num_tag_open' => '<li>',

			'num_tag_close' => '</li>',

			'prev_tag_open' => '<li>',

			'prev_tag_close' => '</li>',

			'next_tag_open' => '<li>',

			'next_tag_close' => '</li>',

			'last_tag_open' => '<li>',

			'last_tag_close' => '</li>',

			'first_tag_open' => '<li>',

			'first_tag_close' => '</li>',

			'cur_tag_open' => '<li class="active"><a href="#">',

			'cur_tag_close' => '</a></li>',

			'uri_segment' => $uri 

		);

		return $this->pagination->initialize($config);
	}

	function stock_update_restok()
	{

		$items_id = $this->input->post('item_id');

		$stock = $this->input->post('stock');

		for($i = 0; $i < count($items_id); $i++)
		{

			$data_items = $this->main_model->get_detail('product_variant',array('id' => $items_id[$i]));

			if( ($stock[$i] != null) and (is_numeric($stock[$i]) == 1) )
			{

				$stock_new = $stock[$i];

				$newStock = $data_items['stock'] + $stock_new;

				if ($stock_new != '') {
					$data_update = array('stock' => $newStock);

					$where = array('id' => $items_id[$i]);

					$this->db->update('product_variant',$data_update,$where);

					$stock_histories = array(
						'prod_id'         => $data_items['prod_id'],
						'variant_id'      => $items_id[$i],
						'prev_stock'      => $data_items['stock'],
						'stock'           => $newStock,
						'qty'             => '+'.$stock_new,
						'user_id'         => $this->session->userdata('webadmin_user_id'),
						'note'            => "Re-stock Produk",
						'user_approve_id' => $this->session->userdata('webadmin_user_id')
					);
					$this->db->insert('stock_histories', $stock_histories);
				}

			}	
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Data Stock Telah di Tambah</div>');

		if($this->session->userdata('prod_id') != null)
		{
			redirect('administrator/main/search_product_restok');
		} else {

			redirect('administrator/main/restok_stok');
		}

						// redirect('administrator/main/restok_stok');
	}

	function search_product_session_restok()

	{

		if($this->input->post('prod_id') != '')
		{
			$prod_id = $this->input->post('prod_id');
		}
		else
		{
			$prod_id = $this->session->userdata('prod_id');
		}

		if($this->input->post('view_pages') != '')
		{
			if ($this->input->post('view_pages') != 'all') {
				$perpage = $this->input->post('view_pages');
			}else{

				$perpage = 1000;
			}
		} else {

			$perpage = 10;
		}

		$data_session = array( 'prod_id' => $prod_id, 'perpage' => $perpage);

		$this->session->set_userdata($data_session);

		redirect('administrator/main/search_product_restok');

	}



	function search_product_restok($offset = 0) {
		$data['output'] = null;
		$int_value = $this->session->userdata('prod_id');

		$this->db->select('*');
		$this->db->join('product', 'product.id = product_variant.prod_id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('product_variant.prod_id', $int_value);

		$data_total = $this->db->get('product_variant');
		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}
		$this->load->library('pagination');

		$config = array(
			'base_url'        => base_url().'administrator/main/search_product_stock',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$search_name = $this->main_model->get_detail('product_category',array('id'=> $int_value));
		$data['placeholder_cat'] = $search_name;

		$this->db->join('product_variant', 'prod_id = product.id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('product_variant.prod_id', $int_value);
						// $data['list_stock'] = $this->main_model->get_list('product',  array('perpage' => $perpage, 'offset' => $offset),array('by' => 'product.id', 'sorting' => 'DESC'));

		$this->db->order_by('product.id', 'DESC');
						// $this->db->limit(10);
		$data['list_item'] = $this->db->get('product');

		$this->load->view('administrator/page_restok_stock', $data);
	}

	function opname_stok($offset = 0){
		$data['output'] = null;

		$this->db->select('*');
		$this->db->join('product', 'product.id = product_variant.prod_id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$data_total = $this->db->get('product_variant');
		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}

		$this->load->library('pagination');

		$url = 'opname_stok';
		$uri = 4;

		$this->pagging_setting($url,$perpage,$data_total->num_rows(),$uri);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$this->db->join('product_variant', 'prod_id = product.id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->order_by('product.id', 'DESC');
						// $data['list_item'] = $this->db->get('product');

		$data['list_item'] = $this->main_model->get_list('product', array('perpage' => $perpage, 'offset' => $offset),array('by' => 'product.id', 'sorting' => 'DESC'));

		$this->load->view('administrator/page_opname_stock',$data);
	}

	function stock_update_opname()
	{

		$items_id = $this->input->post('item_id');

		$stock = $this->input->post('stock');

		for($i = 0; $i < count($items_id); $i++)
		{

			$data_items = $this->main_model->get_detail('product_variant',array('id' => $items_id[$i]));

			if( ($stock[$i] != null) and (is_numeric($stock[$i]) == 1) )
			{

				$stock_new = $stock[$i];

				$data_update = array('stock' => $stock_new);

				$where = array('id' => $items_id[$i]);

				$this->db->update('product_variant',$data_update,$where);

				$stock_histories = array(
					'prod_id'         => $data_items['prod_id'],
					'variant_id'      => $items_id[$i],
					'prev_stock'      => $stock_new,
					'stock'           => $stock_new,
					'qty'             => $stock_new,
					'user_id'         => $this->session->userdata('webadmin_user_id'),
					'note'            => "Opname stok Produk",
					'user_approve_id' => $this->session->userdata('webadmin_user_id')
				);
				$this->db->insert('stock_histories', $stock_histories);

			}	
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Data Stock Telah di Ubah</div>');

		if($this->session->userdata('prod_id') != null)
		{
			redirect('administrator/main/search_product_opname');
		} else {

			redirect('administrator/main/opname_stok');
		}

	}

	function search_product_session_opname()
	{

		if($this->input->post('prod_id') != '')
		{
			$prod_id = $this->input->post('prod_id');
		}
		else
		{
			$prod_id = $this->session->userdata('prod_id');
		}

		if($this->input->post('view_pages') != '')
		{
			if ($this->input->post('view_pages') != 'all') {
				$perpage = $this->input->post('view_pages');
			}else{

				$perpage = 1000;
			}
		} else {

			$perpage = 10;
		}

		$data_session = array( 'prod_id' => $prod_id, 'perpage' => $perpage);

		$this->session->set_userdata($data_session);

		redirect('administrator/main/search_product_opname');

	}

	function search_product_opname($offset = 0) {
		$data['output'] = null;
		$int_value = $this->session->userdata('prod_id');

		$this->db->select('*');
		$this->db->join('product', 'product.id = product_variant.prod_id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('product_variant.prod_id', $int_value);

		$data_total = $this->db->get('product_variant');
		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}
		$this->load->library('pagination');

		$config = array(
			'base_url'        => base_url().'administrator/main/search_product_stock',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$search_name = $this->main_model->get_detail('product_category',array('id'=> $int_value));
		$data['placeholder_cat'] = $search_name;

		$this->db->join('product_variant', 'prod_id = product.id');
		$this->db->where('status !=', 'Delete');
		$this->db->where('available !=', 'Delete');
		$this->db->where('product_variant.prod_id', $int_value);
		$this->db->order_by('product.id', 'DESC');
		$data['list_item'] = $this->db->get('product');

		$this->load->view('administrator/page_opname_stock', $data);
	}


	function limit_customer($offset = null) {
		$data_session = array(
			'name'         => null,
			'cust_name'    => null,
			'tamu_name'    => null,
			'no_nota'      => null,
			'status'      => null,
			'ekspedisi'      => null,
			'radio'        => null,
			'date_payment' => null
		);

		$this->session->set_userdata($data_session);

		$data['output'] = null;
		$data_total = $this->db->select('COUNT(*) AS total')
		->get_where('customer', array('status' => 'Active'))->row_array();

		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url() . 'administrator/main/limit_customer',
			'per_page'        => $perpage,
			'total_rows'      => $data_total['total'],
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$data['arr'] = array(
			'radio' => $this->session->userdata('radio'),
		);

						// $customer = $this->main_model->get_list_where('customer', array('status' => 'Active'), array('perpage' => $perpage, 'offset' => $offset), array('by' => 'id', 'sorting' => 'DESC'))->result();

						// $query_clause = "SELECT customer.id AS id_customer, customer.name AS nama_customer, customer_type.name AS tipe_customer, customer.keep_limit
						// FROM customer
						// INNER JOIN customer_type ON customer.jenis_customer=customer_type.id";

		$this->db->select('customer.id AS `id_customer`, customer.name AS `nama_customer`, customer_type.name AS `tipe_customer`, customer.keep_limit', FALSE);
		$this->db->from('customer');
		$this->db->join('customer_type', 'customer_type.id = customer.jenis_customer');
		$data['customer'] = $this->db->get()->result();


						// $query = $this->db->query($query_clause)->result();
						// $data['customer'] = $query;
		$this->load->view('administrator/page_limit_customer', $data);
	}


	function search_limit_customer_session() {


		if ($cat_pelanggan = $this->input->post('radio_customer') == "customer") {
			$cat_pelanggan = 'customer';
		} elseif ($cat_pelanggan = $this->input->post('radio_customer') == "tamu") {
			$cat_pelanggan = 'tamu';
		}

		if($this->input->post('customer_name') != null){
			$cust_name = $this->input->post('customer_name');
			$tamu_name = null;
		} else {
			$cust_name = null;
		}

		if($cust_name != '' ) {
			$name = $this->input->post('customer_id');
		} else {
			$name = null;
		}

		if($this->input->post('tamu_name') != null){
			$tamu_name = $this->input->post('tamu_name');
			$cust_name = null;
			$name = null;
		} else {
			$tamu_name = null;
		}

		if($this->input->post('no_nota') != null) {
			$no_nota = $this->input->post('no_nota');
		} else {
			$no_nota = null;
		}

		if($this->input->post('status') != null) {
			$status = $this->input->post('status');
		} else {
			$status = null;
		}

		if($this->input->post('ekspedisi') != null) {
			$ekspedisi = $this->input->post('ekspedisi');
		} else {
			$ekspedisi = null;
		}

		$date_payment = $this->input->post('date_payment');
		if ($date_payment == null) {
			$date_payment = null;
		} else {
			$date_payment - date('Y-m-d', strtotime($date_payment));
		}

		if($this->input->post('radio_customer') != null) {
			$radio = $this->input->post('radio_customer');
		} else {
			$radio = null;
		}

		$data_session = array(
			'name'         => $name,
			'cat'          => $cat_pelanggan,
			'cust_name'    => $cust_name,
			'tamu_name'    => $tamu_name,
			'no_nota'      => $no_nota,
			'status'      => $status,
			'ekspedisi'      => $ekspedisi,
			'date_payment' => $date_payment,
			'radio'        => $radio
		);

		$this->session->set_userdata($data_session);
		redirect('administrator/main/search_limit_customer_value');

	}



	function search_limit_customer_value($offset = 0) {
		if($this->session->userdata('cat') == 'customer') {
			$this->db->where('id !=', 0);
		} elseif ($this->session->userdata('cat') == 'tamu') {
			$this->db->where('customer_id', 0);
		}

		if ($this->session->userdata('name')) {
			$this->db->where('id',$this->session->userdata('name'));
		}

		if ($this->session->userdata('tamu_name')) {
			$this->db->where('name_customer',$this->session->userdata('tamu_name'));
		}

		if ($this->session->userdata('no_nota')) {
			$this->db->where('id',$this->session->userdata('no_nota'));
		}
		if ($this->session->userdata('status')) {
			$this->db->where('shipping_status',$this->session->userdata('status'));
		}

		if ($this->session->userdata('ekspedisi')) {
			$this->db->where('ekspedisi',$this->session->userdata('ekspedisi'));
		}

		if ($this->session->userdata('date_payment')) {
			$this->db->where("DATE_FORMAT(date_payment, '%Y-%m-%d') = ",$this->session->userdata('date_payment'));
		}

		$data['customer'] = $this->db->get_where('customer', array('status' => 'Active'));
		$data_total = $data['customer'];
		$perpage = 25;
		$this->load->library('pagination');
		$config = array (
			'base_url'        => base_url().'administrator/main/search_limit_customer_value',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);

		$data['offset'] = $offset;

		$data['perpage'] = $perpage;

		$this->db->select('customer.id AS `id_customer`, customer.name AS `nama_customer`, customer_type.name AS `tipe_customer`, customer.keep_limit', FALSE);
		$this->db->from('customer');
		$this->db->join('customer_type', 'customer_type.id = customer.jenis_customer');

		if($this->session->userdata('cat') == 'customer') {
			$this->db->where('customer.id !=', 0);
		} elseif ($this->session->userdata('cat') == 'tamu') {
			$this->db->where('customer.id', 0);
		}

		if($this->session->userdata('name')) {
			$this->db->where('customer.id',$this->session->userdata('name'));
		}

						// if($this->session->userdata('tamu_name')) {
						// 	$this->db->like('name_customer',$this->session->userdata('tamu_name'));
						// }

						// if($this->session->userdata('no_nota')) {
						// 	$this->db->where('id',$this->session->userdata('no_nota'));
						// }

						// if($this->session->userdata('status')) {
						// 	$this->db->where('shipping_status',$this->session->userdata('status'));
						// }

						// if($this->session->userdata('ekspedisi')) {
						// 	$this->db->where('ekspedisi',$this->session->userdata('ekspedisi'));
						// }

						// if ($this->session->userdata('date_payment')) {
						// 	$this->db->where("DATE_FORMAT(date_payment, '%Y-%m-%d') = ",$this->session->userdata('date_payment'));
						// }


						// $query = $this->db->query('SELECT customer.id AS id_customer, customer.name AS nama_customer, customer_type.name AS tipe_customer, customer.keep_limit
						// FROM customer
						// INNER JOIN customer_type ON customer.jenis_customer=customer_type.id WHERE customer.id = '.$customer_id.'')->result();
						// $data['customer'] = $query;
						// $this->db->select('customer.name AS `nama_customer`, customer_type.name AS `tipe_customer`, customer.keep_limit', FALSE);
						// $this->db->from('customer');
						// $this->db->join('customer_type', 'customer_type.id = customer.jenis_customer');
						// $this->db->where('customer.id', $customer_id);

		$data['customer'] = $this->db->get()->result();

		$data['output'] = null;

		$data['arr'] = array(
			'customer_name' => $this->session->userdata('cust_name'),
			'customer_id'   => $this->session->userdata('name'),
			'tamu_name'     => $this->session->userdata('tamu_name'),
			'no_nota'       => $this->session->userdata('no_nota'),
			'status'       => $this->session->userdata('status'),
			'ekspedisi'       => $this->session->userdata('ekspedisi'),
			'date_payment'  => $this->session->userdata('date_payment'),
			'radio'         => $this->session->userdata('radio'),
			'perpage'       => $perpage,
			'offset'        => $offset,
		);

		$this->load->view('administrator/page_limit_customer',$data);
	}

	function limit_customer_update()
	{
		$items_id = $this->input->post('item_id');

		$keep_limit = $this->input->post('keep_limit');

		$status = $this->input->post('status');

		for($i = 0; $i < count($items_id); $i++)
		{
			$where = array('id' => $items_id[$i]);
			$order = $this->main_model->get_detail('customer', $where);
			$data_update = array(
				'keep_limit' => $keep_limit[$i],
			);

			$this->db->update('customer',$data_update,$where);
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Data Keep Limit Telah diupdate</div>');

		if($this->session->userdata('name') != null or $this->session->userdata('tamu_name') != null or $this->session->userdata('no_nota') != null or $this->session->userdata('ekspedisi') != null or $this->session->userdata('status') != null)
		{
			redirect('administrator/main/search_limit_customer_value');
		} else {

			redirect('administrator/main/limit_customer');
		}

	}


	public function get_category($alert = 0)
	{

		$data_total = $this->main_model->get_list_where('product_category',array('status_category' => 'publish'));

		$config['base_url'] = site_url('administrator/main/get_category'); 
		$config['total_rows'] = $data_total->num_rows();
		$config['per_page'] = 10;  
		$config["uri_segment"] = 4;  
		$choice = $config["total_rows"] / $config["per_page"];
		$config["num_links"] = floor($choice);


		$config['first_link']       = 'First';
		$config['last_link']        = 'Last';
		$config['next_link']        = 'Next';
		$config['prev_link']        = 'Prev';
		$config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination justify-content-center">';
		$config['full_tag_close']   = '</ul></nav></div>';
		$config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
		$config['num_tag_close']    = '</span></li>';
		$config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
		$config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
		$config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
		$config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['prev_tagl_close']  = '</span>Next</li>';
		$config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
		$config['first_tagl_close'] = '</span></li>';
		$config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['last_tagl_close']  = '</span></li>';

		$this->load->library('pagination');

		$this->pagination->initialize($config);
		$data['page'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;


		$data['category'] = $this->main_model->get_category_list($config["per_page"], $data['page']);           

		$data['pagination'] = $this->pagination->create_links();

		$this->load->view('administrator/get_category',$data);
						// echo "Sedang proses testing";
	}

	public function get_category_min($alert = 0)
	{

		$data_total = $this->main_model->get_list_where('product_category',array('status_category' => 'publish'));

		$config['base_url'] = site_url('administrator/main/get_category'); 
		$config['total_rows'] = $data_total->num_rows();
		$config['per_page'] = 10; 
		$config["uri_segment"] = 4;  
		$choice = $config["total_rows"] / $config["per_page"];
		$config["num_links"] = floor($choice);


		$config['first_link']       = 'First';
		$config['last_link']        = 'Last';
		$config['next_link']        = 'Next';
		$config['prev_link']        = 'Prev';
		$config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination justify-content-center">';
		$config['full_tag_close']   = '</ul></nav></div>';
		$config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
		$config['num_tag_close']    = '</span></li>';
		$config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
		$config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
		$config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
		$config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['prev_tagl_close']  = '</span>Next</li>';
		$config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
		$config['first_tagl_close'] = '</span></li>';
		$config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
		$config['last_tagl_close']  = '</span></li>';

		$this->load->library('pagination');

		$this->pagination->initialize($config);
		$data['page'] = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;


		$data['category'] = $this->main_model->get_category_list($config["per_page"], $data['page']);           

		$data['pagination'] = $this->pagination->create_links();


		$this->load->view('administrator/get_category_min',$data);
	}

	public function edit_price_process()
	{
		$this->load->model('M_category','category_model');
		$post 		= $this->input->post();
		$result 	= $this->category_model->edit_price($post);
		$this->session->set_flashdata('message','<div class="alert alert-success">Proses berhasil</div>');
		redirect('administrator/main/get_category', 'refresh');
	}

	public function edit_price_process_min()
	{
		$this->load->model('M_category_min','category_model_min');
		$post 		= $this->input->post();
		$result 	= $this->category_model_min->edit_price($post);
		$this->session->set_flashdata('message','<div class="alert alert-success">Proses berhasil</div>');
		redirect('administrator/main/get_category_min', 'refresh');
	}

	function update_harga_umum($offset = 0){
		$data['output'] = null;



		$this->db->select('product_price.id, product.name_item, product_price.price, customer_type.name', FALSE);
		$this->db->join('customer_type', 'customer_type.id = product_price.cust_type_id');
		$this->db->join('product', 'product.id = product_price.prod_id');
		$this->db->where('product.status', 'Publish');
		$this->db->order_by('product.name_item', 'ASC');

		$data_total = $this->db->get('product_price');

		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}

		$this->load->library('pagination');

		$url = 'update_harga_umum';
		$uri = 4;

		$this->pagging_setting($url,$perpage,$data_total->num_rows(),$uri);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;

		$this->db->select('product_price.id, product.name_item, product_price.price, customer_type.name', FALSE);
		$this->db->join('customer_type', 'customer_type.id = product_price.cust_type_id');
		$this->db->join('product', 'product.id = product_price.prod_id');
		$this->db->where('product.status', 'Publish');
		$this->db->order_by('product.name_item', 'ASC');
		$data['list_item'] = $this->main_model->get_list('product_price', array('perpage' => $perpage, 'offset' => $offset),array('by' => 'product.name_item', 'sorting' => 'ASC'));



		$this->load->view('administrator/page_update_harga_umum',$data);
	}

	function process_update_harga_umum()
	{

		$items_id = $this->input->post('item_id');

		$price = $this->input->post('price');

		for($i = 0; $i < count($items_id); $i++)
		{

			if( ($price[$i] != null) and (is_numeric($price[$i]) == 1) )
			{

				$price_new = $price[$i];

				$data_update = array('price' => $price_new);

				$where = array('id' => $items_id[$i]);

				$this->db->update('product_price',$data_update,$where);

			}	
		}

		$this->session->set_flashdata('message','<div class="alert alert-success">Data harga Telah di Ubah</div>');

		if($this->session->userdata('prod_id') != null)
		{
			redirect('administrator/main/search_product_session_harga');
		} else {

			redirect('administrator/main/update_harga_umum');
		}

	}

	function search_product_session_harga()
	{

		if($this->input->post('prod_id') != '')
		{
			$prod_id = $this->input->post('prod_id');
		}
		else
		{
			$prod_id = $this->session->userdata('prod_id');
		}

		if($this->input->post('view_pages') != '')
		{
			if ($this->input->post('view_pages') != 'all') {
				$perpage = $this->input->post('view_pages');
			}else{

				$perpage = 1000;
			}
		} else {

			$perpage = 10;
		}

		$data_session = array('prod_id' => $prod_id, 'perpage' => $perpage);

		$this->session->set_userdata($data_session);

		redirect('administrator/main/search_product_harga');

	}

	function search_product_harga($offset = 0) {
		$data['output'] = null;
		$int_value = $this->session->userdata('prod_id');



		$this->db->select('product_price.id, product.name_item, product_price.price, customer_type.name', FALSE);
		$this->db->join('customer_type', 'customer_type.id = product_price.cust_type_id');
		$this->db->join('product', 'product.id = product_price.prod_id');
		$this->db->where('product.status', 'Publish');
		$this->db->where('product.id', $int_value);
		$this->db->order_by('product.name_item', 'ASC');

		$data_total = $this->db->get('product_price');

		if ($this->session->userdata('perpage') != null) {
			$perpage = $this->session->userdata('perpage');
		}else{
			$perpage = 10;
		}

		$this->load->library('pagination');

		$url = 'search_product_harga';
		$uri = 4;

		$config = array(
			'base_url'        => base_url().'administrator/main/search_product_harga',
			'per_page'        => $perpage,
			'total_rows'      => $data_total->num_rows(),
			'full_tag_open'   => '<div class="pagination-wrapper text-right"> <div class="pagination">',
			'full_tag_close'  => '</div></div>',
			'num_tag_open'    => '<li>',
			'num_tag_close'   => '</li>',
			'prev_tag_open'   => '<li>',
			'prev_tag_close'  => '</li>',
			'next_tag_open'   => '<li>',
			'next_tag_close'  => '</li>',
			'last_tag_open'   => '<li>',
			'last_tag_close'  => '</li>',
			'first_tag_open'  => '<li>',
			'first_tag_close' => '</li>',
			'cur_tag_open'    => '<li class="active"><a href="#">',
			'cur_tag_close'   => '</a></li>',
			'uri_segment'     => 4
		);

		$this->pagination->initialize($config);
		$data['offset'] = $offset;
		$data['perpage'] = $perpage;
		$search_name = $this->main_model->get_detail('product_category',array('id'=> $int_value));
		$data['placeholder_cat'] = $search_name;

		$this->db->select('product_price.id, product.name_item, product_price.price, customer_type.name', FALSE);
		$this->db->join('customer_type', 'customer_type.id = product_price.cust_type_id');
		$this->db->join('product', 'product.id = product_price.prod_id');
		$this->db->where('product.status', 'Publish');
		$this->db->where('product.id', $int_value);
		$this->db->order_by('product.name_item', 'ASC');
		$data['list_item'] = $this->db->get('product_price');

		$this->load->view('administrator/page_search_harga_umum', $data);
	}

	function report_tutup_buku_v2(){
		$data['output'] = null;
		$this->load->view('administrator/page_report_tutupbuku',$data);
	}

	function report_tutupbuku_process()

	{

		$data['output'] = null;
		$month = $this->input->post('month');
		$data['this_month'] = $month;
		$query_clause = "SELECT * FROM orders WHERE DATE_FORMAT(date_payment,'%Y-%m') = '$month' AND order_payment = 'Paid'";
		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_tutupbuku_result',$data);
	}

	function update_lock_tutupbuku()
	{
		$month = $this->input->post('month');

		$query_clause = "UPDATE orders SET locked_status = 'Tutup Buku' WHERE order_payment = 'Paid' AND DATE_FORMAT(date_payment,'%Y-%m') = '$month'";

		$query = $this->db->query($query_clause);
		$this->session->set_flashdata('message','<div class="alert alert-success">Status Tutup Buku Periode <strong>#'.$month.' </strong> telah diupdate !</div>');
		redirect('administrator/main/report_tutup_buku_lock');
	}

	function report_tutup_buku() {
		$data_tanggal = date("F Y");
		$this->check_hak_akses('order_dropship');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Pesanan Bulan '.$data_tanggal);
		$crud->where('order_payment','Paid');
		$crud->where('locked_status !=','Tutup Buku');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesanan Lunas');
		$crud->display_as('shipping_from','From');
		$crud->display_as('shipping_to','To');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('shipping_status','Status Pengiriman');
		$crud->display_as('order_status','Jenis Pesanan');
		$crud->display_as('notes','Catatan');
		$crud->display_as('print_nota','Print Nota');
		$crud->display_as('print_ekspedisi','Print Ekspedisi');
		$crud->display_as('locked_status','Status Tutup Buku');
		$crud->order_by('id','DESC');
		$crud->columns('id','customer_id','order_datetime','total','order_payment','locked_status');
						// $crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-success btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_read();
		$crud->unset_delete();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->set_relation('customer_id', 'customer', 'name');
		$crud->callback_column($this->unique_field_name('customer_id'), function ($value, $row) {
			if ($row->customer_id != 0) {
				return $value . ' (' . $row->customer_id . ')';
			} else {
				return '<span style="color:#e23427;">' . $row->name_customer . ' <strong> (Guest)</strong></span>';
			}
		});
		$crud->callback_column('print_nota',array($this,'callback_print_nota'));
		$crud->callback_column('print_ekspedisi',array($this,'callback_print_ekspedisi'));

		$data['output'] = $crud->render();
						// $data['order_payment'] = 'Periode '.$data_tanggal;
		$data['order_payment'] = 'UNTUK TUTUP BUKU';
		$this->load->view('administrator/page_order',$data);
	}

	function report_tutup_buku_lock() {
		$data_tanggal = date("F Y");
		$this->check_hak_akses('order_dropship');
		$crud = new grocery_CRUD();
		$crud->set_table('orders')
		->set_subject('Pesanan Bulan Tutup Buku');
		$crud->where('order_payment','Paid');
		$crud->where('locked_status','Tutup Buku');
		$crud->display_as('id','ID Pesanan');
		$crud->display_as('order_datetime','Tanggal Pesanan Lunas');
		$crud->display_as('shipping_from','From');
		$crud->display_as('shipping_to','To');
		$crud->display_as('order_payment','Status Pembayaran');
		$crud->display_as('shipping_status','Status Pengiriman');
		$crud->display_as('order_status','Jenis Pesanan');
		$crud->display_as('notes','Catatan');
		$crud->display_as('print_nota','Print Nota');
		$crud->display_as('print_ekspedisi','Print Ekspedisi');
		$crud->display_as('locked_status','Status Tutup Buku');
		$crud->order_by('id','DESC');
		$crud->columns('id','customer_id','order_datetime','total','order_payment','locked_status');
						// $crud->add_action('LIHAT', '#', 'administrator/main/order_detail','btn btn-success btn-crud');
		$crud->order_by('id','DESC');
		$crud->unset_texteditor('shipping_from');
		$crud->unset_texteditor('shipping_to');
		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_read();
		$crud->unset_delete();
		$crud->callback_column('id',array($this,'customer_id'));
		$crud->display_as('customer_id','Pembeli');
		$crud->set_relation('customer_id', 'customer', 'name');
		$crud->callback_column($this->unique_field_name('customer_id'), function ($value, $row) {
			if ($row->customer_id != 0) {
				return $value . ' (' . $row->customer_id . ')';
			} else {
				return '<span style="color:#e23427;">' . $row->name_customer . ' <strong> (Guest)</strong></span>';
			}
		});
		$crud->callback_column('print_nota',array($this,'callback_print_nota'));
		$crud->callback_column('print_ekspedisi',array($this,'callback_print_ekspedisi'));

		$data['output'] = $crud->render();
		$data['order_payment'] = 'Periode Tutup Buku';
		$this->load->view('administrator/page_order',$data);
	}

	public function report_per_month_diskon_point(){
						// $data['output'] = null;
		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{

			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND order_payment = 'Paid' AND jenis_customer = '$jenis_customer' ORDER BY order_datetime DESC LIMIT 20";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND order_payment = 'Paid' ORDER BY order_datetime DESC LIMIT 20";
		}
		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_per_month_point',$data);
	}

	public function report_per_month_point_process()

	{
		$month = $this->input->post('month');
		$jenis_customer = $this->input->post('jenis_customer');
		$data['this_month'] = $month;
		$data['jenis_customer'] = $jenis_customer;

		if(($jenis_customer == 'Lokal') or ($jenis_customer == 'Luar'))
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' AND jenis_customer = '$jenis_customer'";
		}
		else
		{
			$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' ";
		}
		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_per_month_point_result',$data);
	}

	function report_poin_diskon_eksport()
	{

		$month = $this->input->post('month');

		$name_excel = "Laporan Poin dan Diskon ".date('M Y', strtotime($month));

		header("Cache-Control: must-revalidate");
		header("Pragma: must-revalidate");

		header("Content-type: application/vnd.ms-excel");
		header("Content-disposition: attachment; filename=".$name_excel.".xls");

		$data['output'] = null;

		$data['header'] = $this->main_model->get_detail('content',array('name' => 'nota'));

		$data['this_month'] = $month;

		$query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' ";

		$query = $this->db->query($query_clause);

		$data['transaksi'] = $query;

		$this->load->view('administrator/page_report_point_diskon_eksport',$data);
	}

	public function get_jumlah_laku_v2(){

		$data['output'] = null;
		if($month != '')
		{
			$query_clause = "SELECT MONTHNAME(A.order_datetime) AS bulan,C.name_item,A.prod_id,sum(qty) AS jumlah_qty,(select sum(stock_awal) from product_variant WHERE prod_id = C.id) as jumlah_stock
			FROM orders_item A 
			JOIN product_variant B on B.id = A.variant_id 
			JOIN product C on C.id = B.prod_id
			WHERE DATE_FORMAT(A.order_datetime,'%Y-%m') = '$month'
			GROUP BY C.id
			DESC LIMIT 20";
		}
		else
		{
			$query_clause = "SELECT MONTHNAME(A.order_datetime) AS bulan,C.name_item,A.prod_id,sum(qty) AS jumlah_qty,(select sum(stock_awal) from product_variant WHERE prod_id = C.id) as jumlah_stock FROM orders_item A 
			JOIN product_variant B on B.id = A.variant_id 
			JOIN product C on C.id = B.prod_id 
			GROUP BY C.id,MONTHNAME(A.order_datetime)
			DESC LIMIT 20";
		}
		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_jumlah_laku_v2', $data);
	}

	public function get_jumlah_laku_process()

	{
		$month = $this->input->post('month');
		$data['this_month'] = $month;
		$prod_id = $this->input->post('prod_id');



		if($month != '')
		{
			$query_clause = "SELECT MONTHNAME(A.order_datetime) AS bulan,C.name_item,A.prod_id,sum(qty) AS jumlah_qty,(select sum(stock_awal) from product_variant WHERE prod_id = C.id) as jumlah_stock
			FROM orders_item A 
			JOIN product_variant B on B.id = A.variant_id 
			JOIN product C on C.id = B.prod_id
			WHERE DATE_FORMAT(A.order_datetime,'%Y-%m') = '$month'
			GROUP BY C.id";
		}
		else
		{
			$query_clause = "SELECT MONTHNAME(A.order_datetime) AS bulan,C.name_item,A.prod_id,sum(qty) AS jumlah_qty,(select sum(stock_awal) from product_variant WHERE prod_id = C.id) as jumlah_stock FROM orders_item A 
			JOIN product_variant B on B.id = A.variant_id 
			JOIN product C on C.id = B.prod_id 
			WHERE A.prod_id = '$prod_id' 
			GROUP BY C.id,MONTHNAME(A.order_datetime)";
		}

		$query = $this->db->query($query_clause);
		$data['transaksi'] = $query;
		$this->load->view('administrator/page_report_laku_terjual_result',$data);

	}
	
function report_jumlah_laku_eksport()
					{

						$month = $this->input->post('month');

						$name_excel = "Laporan Jumlah Laku ".date('M Y', strtotime($month));

						header("Cache-Control: must-revalidate");
						header("Pragma: must-revalidate");

						header("Content-type: application/vnd.ms-excel");
						header("Content-disposition: attachment; filename=".$name_excel.".xls");

						$data['output'] = null;

						$data['header'] = $this->main_model->get_detail('content',array('name' => 'nota'));

						$data['this_month'] = $month;

						// $query_clause = "SELECT * FROM orders WHERE order_status != 'Cancel' AND DATE_FORMAT(order_datetime,'%Y-%m') = '$month' AND order_payment = 'Paid' ";

						$query_clause = "SELECT MONTHNAME(A.order_datetime) AS bulan,C.name_item,A.prod_id,sum(qty) AS jumlah_qty,(select sum(stock_awal) from product_variant WHERE prod_id = C.id) as jumlah_stock
						FROM orders_item A 
						JOIN product_variant B on B.id = A.variant_id 
						JOIN product C on C.id = B.prod_id
						WHERE DATE_FORMAT(A.order_datetime,'%Y-%m') = '$month'
						GROUP BY C.id";

						$query = $this->db->query($query_clause);

						$data['transaksi'] = $query;

						// $this->load->view('administrator/page_report_point_diskon_eksport',$data);
						$this->load->view('administrator/page_jumlah_laku_eksport',$data);
					}

}