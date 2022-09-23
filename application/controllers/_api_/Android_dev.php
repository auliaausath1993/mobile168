<?php
defined('BASEPATH') OR exit('No direct script access allowed');//http://stackoverflow.com/questions/18382740/cors-not-working-php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

class Android_dev extends CI_Controller {

	Public $status_new_member = "Moderate"; // moderate, active
	Public $jne_service = "reg";
	Public $base_url_api = "http://api.tokomobile.co.id/ongkir/development/api/";
	Public $token, $domain;

	function __construct() {
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");
		$postdata = file_get_contents("php://input");
		$this->request = json_decode($postdata);

		$this->domain = base_url(); //$this->config->item('tokomobile_domain');
		$this->token = $this->config->item('tokomobile_token');
	}

	function check_app_version() {
		$data_link_update = $this->main_model->get_detail('data_version_update');
		$data_link_update_2 = $this->main_model->get_detail('content',array('name' => 'link_android'));
		$langsung_dashboard = $this->main_model->get_detail('content',array('name' => 'langsung_dashboard'));
		$no_wa = $this->main_model->get_detail('content',array('name' => 'no_wa'));
		$data_json = array(
			'number_version'     => $data_link_update['number_version'],
			'link'               => $data_link_update_2['value'],
			'langsung_dashboard' => $langsung_dashboard['value'],
			'no_wa'              => $no_wa['value'],
		);

		echo json_encode($data_json);

	}

	function check_token($token = null, $customer_id = null) {
		$status_aplication = $this->main_model->get_detail('content',array('name' => 'status_aplication'));

		if($token != $this->config->item('tokomobile_token')) {
			$data_json = array("status" => "Invalid Token");
			exit (json_encode($data_json));
		}


		if($status_aplication['value'] == 'OFF') {
			$message_off = $this->main_model->get_detail('content',array('name' => 'message_off'));
			$data_json = array("status" => "OFF","message" => $message_off['value']);
			exit (json_encode($data_json));
		};

		if($customer_id != null) {
			$data_customer = $this->main_model->get_list_where('customer',array('id' => $customer_id));
			if($data_customer->num_rows() != 1) {
				$data_json = array("status" => "Member Not Found");
				exit (json_encode($data_json));
			} else {
				$data_customer_detail = $data_customer->row_array();
				$langsung_dashboard = $this->main_model->get_detail('content',array('name' => 'langsung_dashboard'));
				if($data_customer_detail['status'] != 'Active' && $langsung_dashboard['value'] == 'OFF') {
					$data_json = array("status" => "Member Not Active");
					exit (json_encode($data_json));
				}
			}
		}

	}

	private function checkMemberStatus($customer_id) {
		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		return $customer['status'];
	}

	function login() { //OK
		$token = $this->request->token;
		$this->check_token($token,null);

		$customer_id = $this->request->customer_id;
		$password    = $this->request->password;
		$registration_id = property_exists($this->request, 'registration_id') ? $this->request->registration_id : '';

		if(is_numeric($customer_id) == 1) {
			$data_check = $this->main_model->get_list_where('customer',array('id' => $customer_id));
		} else {
			$data_check = $this->main_model->get_list_where('customer',array('email' => $customer_id));
		}

		if ($data_check->num_rows() > 0) {
			if(is_numeric($customer_id) == 1) {
				$data_member = $this->main_model->get_detail('customer',array('id' => $customer_id));
			} else {
				$data_member = $this->main_model->get_detail('customer',array('email' => $customer_id));
			}

			// $member_password = $this->encrypt->decode($data_member['password']);
			// $member_password = password_verify($password, $data_member['password']);

			// if ($password == $member_password) {
			if (password_verify($password, $data_member['password'])) {
			
				if ($data_member['status'] == 'Active') {
					$data_json = array(
						'status'         => 'Success',
						'customer_id'    => $data_member['id'],
						'customer_email' => $data_member['email'],
						'customer_name'  => $data_member['name'],
						'jenis_customer' => $data_member['jenis_customer'],
						'prov_id'        => $data_member['prov_id'],
						'kota_id'        => $data_member['kota_id'],
						'kecamatan_id'   => $data_member['kecamatan_id'],
						'status_member'  => $data_member['status'],
						'notif'          => $data_member['notif'],
					);
					if ($registration_id != $data_member['token']) {
						if ($data_member['token']) {
							$title = 'Aktifitas Login Aplikasi';
							$message = 'Akun ini telah login di device lain. Akun ini akan dilogout.';
							// $this->fcm_push_single(array($data_member['token']), 'login', $title, $message);
						}
						$where = array('id' => $data_member['id']);
						$this->db->update('customer', array('token' => $registration_id), $where);
					}
				} else if ($data_member['kirim_otp'] == 1) {
					$data_json = array(
						'status'      => 'Success',
						'kirim_otp'   => true,
						'email'       => $data_member['email'],
						'customer_id' => $data_member['id'],
					);
				} else {
					$data_json = array(
						'status' => 'Failed',
						'error'  => 'User belum aktif, hubungi Admin untuk aktivasi'
					);
				}
			} else {
				$data_json = array(
					'status' => 'Failed',
					'error'  => 'Password anda salah'
				);
			}
		} else {
			$data_json = array(
				'status' => 'Failed',
				'error'  => 'User not found'
			);
		}
		echo json_encode($data_json);
	}

	function fcm_push_single($reg_id, $type, $title, $msg) {
		$notif = array(
			'text'			=> $msg,
		    'title'         => $title,
		    'vibrate'   	=> 'default',
		    'sound'     	=> 'default',
		    'type'			=> $type,
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

	function register() {
		$token = $this->request->token;
		$this->check_token($token,null);

		$nama      = $this->request->nama;
		$email     = $this->request->email;
		$password  = $this->request->password;
		$alamat    = $this->request->alamat;
		$provinsi  = $this->request->provinsi;
		$kota      = $this->request->kota;
		$kecamatan = $this->request->kecamatan;
		$kodepos   = $this->request->kodepos;
		$phone     = $this->request->phone;
		$pinbb     = $this->request->pinbb;

		$data_check = $this->main_model->get_list_where('customer',array('email' => $email));

		if($data_check->num_rows() < 1) {
			$data_insert = array(
				'name'           => $nama,
				'email'          => $email,
				// 'password'       => $this->encrypt->encode($password),
				'password'       => password_hash($password,PASSWORD_DEFAULT),
				'address'        => $alamat,
				'prov_id'        => $provinsi,
				'kota_id'        => $kota,
				'kecamatan_id'   => $kecamatan,
				'postcode'       => $kodepos,
				'phone'          => $phone,
				'pin_bb'         => $pinbb,
				'jenis_customer' => 0,
				'notif'          => 'confirm|order_status|resi',
				'status'         => $this->status_new_member
			);

			$this->db->insert('customer',$data_insert);

			$customer_id = $this->db->insert_id();

			$data_json = array(
				'status'         => 'Success',
				'customer_id'    => $customer_id,
				'customer_email' => $email,
				'customer_name'  => $nama,
				'customer_phone' => $phone,
				'jenis_customer' => 0,
				'prov_id'        => $provinsi,
				'kota_id'        => $kota,
				'kecamatan_id'   => $kecamatan,
				'status_member'  => $this->status_new_member,
				'message'        => 'Terimakasih telah register, silahkan menunggu verifikasi'
			);
		} else {
			$data_json = array(
				'status' => 'Failed',
				'error'  => 'Email is exists'
			);
		}
		echo json_encode($data_json);
	}

	function register_v2() {
		$token = $this->request->token;
		$this->check_token($token, null);
		$nama      = $this->request->nama;
		$email     = $this->request->email;
		$password  = $this->request->password;
		$phone     = $this->request->phone;

		$data_check = $this->main_model->get_list_where('customer',array('email' => $email));

		if ($data_check->num_rows() < 1) {
			$data_insert = array(
				'name'           => $nama,
				'email'          => $email,
				// 'password'       => $this->encrypt->encode($password),
				'password'       => password_hash($password,PASSWORD_DEFAULT),
				'phone'          => $phone,
				'jenis_customer' => 0,
				'notif'          => 'confirm|order_status|resi',
				'status'         => $this->status_new_member,
				'kirim_otp'      => 1,
			);
			$this->db->trans_start();
			$this->db->insert('customer', $data_insert);

			$customer_id = $this->db->insert_id();

			$this->load->library('email');

			$this->emailConfig();

			$domain = $this->config->item('tokomobile_domain');
			$shop_name = $this->config->item('tokomobile_online_shop');

			$this->email->from('admin@' . $domain, 'Admin ' . $shop_name);
			$this->email->to($email);

			$this->email->subject('Selamat Datang di ' . $shop_name);

			$otp = rand(111111, 999999);

			while (1) {
				$check_otp = $this->db->get_where('otp', array('code' => $otp))->row_array();
				if (empty($check_otp)) {
					break;
				} else {
					$otp = rand(111111, 999999);
				}
			}

			$data_otp = array(
				'customer_id' => $customer_id,
				'code'        => $otp
			);
			$this->db->insert('otp', $data_otp);

			$data = array(
				'domain'    => $domain,
				'shop_name' => $shop_name,
				'name'      => $nama,
				'email'     => $email,
				'phone'     => $phone,
				'password'  => $password,
				'otp'       => $otp
			);
			$message = $this->load->view('emails/welcome', $data, TRUE);
			$this->email->message($message);

			$this->email->send();

			// echo $this->email->print_debugger();
			$this->db->trans_complete();
			$data_json = array(
				'status'         => 'Success',
				'customer_id'    => $customer_id,
				'customer_email' => $email,
				'customer_name'  => $nama,
				'customer_phone' => $phone,
				'jenis_customer' => 0,
				'status_member'  => $this->status_new_member,
				'message'        => 'Terimakasih telah register, silahkan masukan kode OTP yang dikirim ke email Anda'
			);
		} else {
			$data_json = array(
				'status' => 'Failed',
				'error'  => 'Email is exists'
			);
		}
		echo json_encode($data_json);
	}

	public function checkOtp() {
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$otp_code = $this->request->otp_code;
		$otp = $this->db->get_where('otp', array('code' => $otp_code))->row_array();
		if (!empty($otp)) {
			$this->db->update('customer', array('sudah_otp' => 1, 'kirim_otp' => 0), array('id' => $customer_id));
			$this->db->delete('otp', array('code' => $otp_code));
			$data_json = array(
				'status' => 'Success',
				'message' => 'Silakan hubungi Admin untuk aktivasi melalui menu Chat pada icon di pojok kanan atau melalui WhatsApp di menu informasi'
			);
		} else {
			$data_json = array(
				'status'  => 'Failed',
				'message' => 'Kode OTP yang Anda masukkan salah'
			);
		}
		echo json_encode($data_json);
	}

	public function resendOtp() {
		$token       = $this->request->token;
		$email       = $this->request->email;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';

		$this->load->library('email');

		$this->emailConfig();

		$domain = $this->config->item('tokomobile_domain');
		$shop_name = $this->config->item('tokomobile_online_shop');

		$this->email->from('admin@' . $domain, 'Admin ' . $shop_name);
		$this->email->to($email);

		$this->email->subject('Selamat Datang di ' . $shop_name);

		$otp = rand(111111, 999999);

		while (1) {
			$check_otp = $this->db->get_where('otp', array('code' => $otp))->row_array();
			if (empty($check_otp)) {
				break;
			} else {
				$otp = rand(111111, 999999);
			}
		}

		$data_otp = array('code' => $otp);
		$this->db->update('otp', $data_otp, array('customer_id' => $customer_id));
		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));

		$data = array(
			'domain'    => $domain,
			'shop_name' => $shop_name,
			'name'      => $customer['name'],
			'email'     => $customer['email'],
			'phone'     => $customer['phone'],
			// 'password'  => $this->encrypt->decode($customer['password']),
			'password'  => password_hash($customer,PASSWORD_DEFAULT),
			'otp'       => $otp,
			'kirim_otp' => 1
		);
		$message = $this->load->view('emails/welcome', $data, TRUE);
		$this->email->message($message);

		$this->email->send();

		$data_json = array(
			'status'  => 'Success',
			'message' => 'Email berisi kode OTP telah dikirimkan ke email Anda'
		);
		echo json_encode($data_json);
	}

	function get_cart_total() {
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		if ($customer_id) {
			$cart = $this->db->select('COUNT(*) AS total')
				->get_where('orders_item', array(
					'customer_id'  => $customer_id,
					'order_status' => 'Keep',
					'order_id'     => 0,
				))->row_array();
			$message = $this->db->select('COUNT(*) AS total')
				->get_where('message', array(
					'customer_id' => $customer_id,
					'status'      => 'unread'
				))->row_array();
			$countchat = $this->db->select('COUNT(*) AS total')
				->get_where('chatting', array(
					'customer_id' => $customer_id,
					'status'      => 'Unread',
					'sender'      => 'Admin'
				))->row_array();
			$countchat_product = $this->main_model->get_list_where('chat_product', array('customer_id' => $customer_id, 'read_chat' => 0, 'sender' => 'Admin'));
			$countchat_product = $this->db->select('COUNT(*) AS total')
				->get_where('chat_product', array(
					'customer_id' => $customer_id,
					'read_chat'   => 0,
					'sender'      => 'Admin'
				))->row_array();
			$data_json = array(
				'status'             => 'Success',
				'total_cart'         => $cart['total'],
				'total_message'      => $message['total'],
				'total_chat'         => $countchat['total'],
				'total_chat_product' => $countchat_product['total'],
			);
		} else {
			$data_json = array(
				'status'             => 'Success',
				'total_cart'         => 0,
				'total_message'      => 0,
				'total_chat'         => 0,
				'total_chat_product' => 0,
			);
		}
		echo json_encode($data_json);
	}

	function get_customer_info() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		$prov_id       = $data_customer['prov_id'];
		$city_id       = $data_customer['kota_id'];
		$kecamatan_id  = $data_customer['kecamatan_id'];

		// GET PROVINSI
		$prov = $this->main_model->get_detail('provinces', array('province_id' => $prov_id));
		$kota = $this->main_model->get_detail('cities', array('city_id' => $city_id));
		$kec = $this->main_model->get_detail('subdistricts', array('subdistrict_id' => $kecamatan_id));

		$data_json = array(
			'status' 	=> 'Success',
			'customer'	=> $data_customer,
			'prov' 		=> $prov['province'],
			'kota' 		=> $kota['city_name'],
			'kec' 		=> $kec['subdistrict_name'],
		);
		echo json_encode($data_json);
	}

	//PRODUCT BEST SELLER & NEW
	function get_dashboard_data() {
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		if ($customer_id == '' || $customer['jenis_customer'] == 0) {
			$order = 'asc';
		} else {
			$order = 'desc';
		}
		$data_best_seller = $this->main_model->get_list_where('product',array('status' => 'Publish','best_seller' => 'Ya'), null, array('by' => 'datetime','sorting' => 'DESC'));
		foreach ($data_best_seller->result() as $products){
			$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $products->id))->result();
			if (count($list_price) > 0) {
				$product_price = $this->db->get_where('product_price', array('prod_id' => $products->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
				if (count($product_price) > 0) {
					$price = $product_price['price'];
				  	$price_old = $product_price['old_price'];
				} else {
					$price = $list_price[0]->price;
				  	$price_old = $list_price[0]->old_price;
				}
			} else {

				if ($customer['jenis_customer'] == '1') {
					$price     = $products->price;
					$price_old = $products->price_old;
				} else {
					if ($products->price_old_luar == 0) {
						$price     = $products->price_luar;
						$price_old = $products->price_old_luar;
					} else {
						$price     = $products->price;
						$price_old = $products->price_old_luar;
					}
				}
			}
			$json['data_best_seller'][] = array(
				'product_id'    => $products->id,
				'name_item'     => $products->name_item,
				'harga'         => $price,
				'harga_lama'    => $price_old,
				'berat'         => $products->weight,
				'foto'          => base_url().'media/images/original/'.$products->image,
				'img_thumbnail' => base_url().'media/images/thumb/'.$products->image,
				'img_medium'    => base_url().'media/images/medium/'.$products->image,
				'img_large'     => base_url().'media/images/large/'.$products->image,
				'min_order'     => $products->min_order,
				'product_type'  => $products->product_type,
			);
		}

		$data_promo = $this->main_model->get_list_where('product',array('status' => 'Publish','promo' => 'Ya'), null, array('by' => 'datetime','sorting' => 'DESC'));
		foreach($data_promo->result() as $products) {
			$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $products->id))->result();
				if (count($list_price) > 0) {
					$product_price = $this->db->get_where('product_price', array('prod_id' => $products->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
					if (count($product_price) > 0) {
						$price = $product_price['price'];
					  	$price_old = $product_price['old_price'];
					} else {
						$price = $list_price[0]->price;
					  	$price_old = $list_price[0]->old_price;
					}
				} else {

					if ($customer['jenis_customer'] == '1') {
						$price     = $products->price;
						$price_old = $products->price_old;
					} else {
						if ($products->price_old_luar == 0) {
							$price     = $products->price_luar;
							$price_old = $products->price_old_luar;
						} else {
							$price     = $products->price;
							$price_old = $products->price_old_luar;
						}
					}
				}
			$json['data_promo'][] = array(
				'product_id'    => $products->id,
				'name_item'     => $products->name_item,
				'harga'         => $price,
				'harga_lama'    => $price_old,
				'berat'         => $products->weight,
				'foto'          => base_url().'media/images/original/'.$products->image,
				'img_thumbnail' => base_url().'media/images/thumb/'.$products->image,
				'img_medium'    => base_url().'media/images/medium/'.$products->image,
				'img_large'     => base_url().'media/images/large/'.$products->image,
				'min_order'     => $products->min_order,
				'product_type'  => $products->product_type,
			);
		}

		$data_slider = $this->main_model->get_list_where('slideshow');
		foreach($data_slider->result() as $slider){
			$json['slider'][] = array(
				'id'    => $slider->id,
				'name'  => $slider->title,
				'image' => base_url().'media/images/'.$slider->image,
			);
		}
		echo json_encode($json);
	}

	function get_dashboard_data_v2() {
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$program_diskon = $this->db->get_where('discounts', array('active' => 1))->row_array();
		$category_discount = explode('|', $program_diskon['product_categories']);
		$customer_type_discount = explode('|', $program_diskon['customer_types']);
		$tag_discount = explode('|', $program_diskon['product_tags']);

		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		if ($customer_id == '' || $customer['jenis_customer'] == 0) {
			$order = 'asc';
		} else {
			$order = 'desc';
		}

		$tags = $this->db->get('name_tag')->result();
		foreach ($tags as $tag) {
			$products = $this->db->select('P.id, price, price_old, price_old_luar, name_item, image, category_id')
				->from('product_tags PT')
				->join('product P', 'P.id = PT.product_id', 'INNER')
				->where('PT.tag_id', $tag->id)
				->where('P.status', 'Publish')
				->order_by('P.datetime', 'DESC')
				->get()->result();
			$data_product = array();
			foreach ($products as $product) {
				$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $product->id))->result();
				if (count($list_price) > 0) {
					$product_price = $this->db->get_where('product_price', array('prod_id' => $product->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
					if (count($product_price) > 0) {
						$price = $product_price['price'];
					  	$price_old = $product_price['old_price'];
					} else {
						$price = $list_price[0]->price;
					  	$price_old = $list_price[0]->old_price;
					}
				} else {

					if ($customer['jenis_customer'] == '1') {
						$price     = $product->price;
						$price_old = $product->price_old;
					} else {
						if ($product->price_old_luar == 0) {
							$price     = $product->price_luar;
							$price_old = $product->price_old_luar;
						} else {
							$price     = $product->price;
							$price_old = $product->price_old_luar;
						}
					}
				}
				if (empty($program_diskon)) {
					$discount = false;
				} else {
					$today = date('Y-m-d');
					$discount = in_array($product->category_id, $category_discount) && in_array($tag->id, $tag_discount) && in_array($customer['jenis_customer'], $customer_type_discount) && $today >= $program_diskon['from_date'] && $today <= $program_diskon['to_date'];
				}
				$data_product[] = array(
					'product_id' => $product->id,
					'name_item'  => $product->name_item,
					'harga'      => $price,
					'harga_lama' => $price_old,
					'img_medium' => base_url('media/images/medium/' . $product->image),
					'discount'   => $discount,
				);
			}
			if ($tag->id != 1) {
				$tag->products = $data_product;
			}
		}
		$json['tags'] = $tags;

		$data_slider = $this->main_model->get_list_where('slideshow');
		foreach($data_slider->result() as $slider){
			$json['slider'][] = array(
				'id'    => $slider->id,
				'name'  => $slider->title,
				'image' => base_url().'media/images/'.$slider->image,
			);
		}
		$no_wa = $this->main_model->get_detail('content',array('name' => 'no_wa'));
		$json['no_wa'] = $no_wa['value'];
		echo json_encode($json);
	}

	// CATEGORY //
	function get_list_product_category() {//OK
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$data_category_total = $this->db->query('SELECT * FROM product_category WHERE status_category != "delete" ORDER BY name ASC');
		$data_category = $this->db->query('SELECT * FROM product_category WHERE status_category != "delete" ORDER BY name ASC');
		$a = $data_category_total->num_rows();
		$i = 1;

		if($data_category_total->num_rows() > 0) {
			foreach($data_category->result() as $categories) {
				$data_total_prod = $this->main_model->get_list_where('product',array('category_id' => $categories->id,'status' => 'Publish'));
				$data_json[] = array('id' => $categories->id,'name' => $categories->name);
			}
			$return_json = array(
				'status'   => 'Success',
				'category' => $data_json
			);
		} else {
			$return_json['status'] = 'Error';
		}
		echo json_encode($return_json);
	}

	// LIST PRODUCT //
	function get_list_product() {//OK
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$page        = $this->request->page;
		$category    = $this->request->category;

		$perpage     = 20;
		$offset      = $perpage * ($page - 1);

		if(($category != null) && ($category != 'all')) {
			if ($category == 'preorder') {
				$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish','product_type' => 'PO'));
				$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish','product_type' => 'PO'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC'));
			} else {
				$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish','category_id' => $category));
				$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish','category_id' => $category),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC'));
			}
		} else {
			$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish'));
			$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC'));
		}
		$total_page = ceil($data_product_total->num_rows() / $perpage);
		if ($data_product_total->num_rows() > 0) {
			if ($data_product->num_rows() > 0) {
				foreach ($data_product->result() as $products) {
				$stok_total = $this->db->query("SELECT SUM(stock) AS stok_total FROM product_variant WHERE prod_id ='$products->id' ")->row_array();
					$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));

					if ($customer_id == '' || $customer['jenis_customer'] == 0) {
						$order = 'asc';
					} else {
						$order = 'desc';
					}

					$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $products->id))->result();
					if (count($list_price) > 0) {
						$product_price = $this->db->get_where('product_price', array('prod_id' => $products->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
						if (count($product_price) > 0) {
							$price = $product_price['price'];
						  	$price_old = $product_price['old_price'];
						} else {
							$price = $list_price[0]->price;
						  	$price_old = $list_price[0]->old_price;
						}
					} else {

						if ($customer['jenis_customer'] == '1') {
							$price     = $products->price;
							$price_old = $products->price_old;
						} else {
							if ($products->price_old_luar == 0) {
								$price     = $products->price_luar;
								$price_old = $products->price_old_luar;
							} else {
								$price     = $products->price;
								$price_old = $products->price_old_luar;
							}
						}
					}
					$data_json[] = array(
						'product_id'    => $products->id,
						'name_item'     => $products->name_item,
						'harga'         => $price,
						'harga_lama'    => $price_old,
						'berat'         => $products->weight,
						'foto'          => base_url().'media/images/original/'.$products->image,
						'img_thumbnail' => base_url().'media/images/thumb/'.$products->image,
						'img_medium'    => base_url().'media/images/medium/'.$products->image,
						'img_large'     => base_url().'media/images/large/'.$products->image,
						'min_order'     => $products->min_order,
						'product_type'  => $products->product_type,
						'promo'			=> $products->promo,
						'best_seller'	=> $products->best_seller,
						'stok_total'    => $stok_total['stok_total']
					);
				}
				$return_json = array(
					'status'        => 'Success',
					'total_page'    => $total_page,
					'total_product' => $data_product_total->num_rows(),
					'product'       => $data_json
				);
			} else {
				$return_json = array(
					'status'        => 'Failed',
					'total_page'    => 1
				);
			}
		} else {
			$return_json = array(
				'status'        => 'Not Found',
				'total_page'    => 1
			);
		}
		echo json_encode($return_json);
	}

	function get_list_product_v2() {//OK
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$page        = $this->request->page;
		$category    = $this->request->category;

		$perpage     = 20;
		$offset      = $perpage * ($page - 1);

		$program_diskon = $this->db->get_where('discounts', array('active' => 1))->row_array();
		$category_discount = explode('|', $program_diskon['product_categories']);
		$customer_type_discount = explode('|', $program_diskon['customer_types']);
		$tag_discount = explode('|', $program_diskon['product_tags']);

		$where_product['status'] = 'Publish';

		if ($category != 'all') {
			$where_product['category_id'] = $category;
		}

		$product_total = $this->db->select('COUNT(*) AS total')
			->where($where_product)
			->get('product')->row_array();

		$total_page = ceil($product_total['total'] / $perpage);

		$this->db->select('id, price, price_old, price_old_luar, name_item, image, category_id');
		$data_product = $this->main_model->get_list_where('product', $where_product, array('perpage' => $perpage,'offset' => $offset), array('by' => 'datetime', 'sorting' => 'DESC'));

		if ($product_total['total'] > 0) {
			foreach ($data_product->result() as $product) {
				$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));

				if ($customer_id == '' || $customer['jenis_customer'] == 0) {
					$order = 'asc';
				} else {
					$order = 'desc';
				}

				$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $product->id))->result();
				if (count($list_price) > 0) {
					$product_price = $this->db->get_where('product_price', array('prod_id' => $product->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
					if (count($product_price) > 0) {
						$price = $product_price['price'];
					  	$price_old = $product_price['old_price'];
					} else {
						$price = $list_price[0]->price;
					  	$price_old = $list_price[0]->old_price;
					}
				} else {

					if ($customer['jenis_customer'] == '1') {
						$price     = $product->price;
						$price_old = $product->price_old;
					} else {
						if ($product->price_old_luar == 0) {
							$price     = $product->price_luar;
							$price_old = $product->price_old_luar;
						} else {
							$price     = $product->price;
							$price_old = $product->price_old_luar;
						}
					}
				}
				$tags = $this->db->select('PT.tag_id, NT.name')
					->join('product_tags PT', 'PT.tag_id = NT.id')
					->get_where('name_tag NT', array('PT.product_id' => $product->id))
					->result();

				$tag_product = array();
				foreach ($tags as $tag) {
					$tag_product[] = $tag->tag_id;
				}
				$compare_tag = array_diff($tag_product, $tag_discount);
				if (empty($program_diskon)) {
					$discount = false;
				} else {
					$today = date('Y-m-d');
					$discount = in_array($product->category_id, $category_discount) && count($compare_tag) == 0 && in_array($customer['jenis_customer'], $customer_type_discount) && $today >= $program_diskon['from_date'] && $today <= $program_diskon['to_date'];
				}
				$data_json[] = array(
					'product_id' => $product->id,
					'name_item'  => $product->name_item,
					'harga'      => $price,
					'harga_lama' => $price_old,
					'img_medium' => base_url('media/images/medium/' . $product->image),
					'tag'        => count($tags) > 0 ? $tags[0] : null,
					'discount'   => $discount,
				);
			}
			$return_json = array(
				'status'        => 'Success',
				'total_page'    => $total_page,
				'total_product' => $product_total['total'],
				'product'       => $data_json
			);
		} else {
			$return_json = array(
				'status'        => 'Not Found',
				'total_page'    => 1
			);
		}
		echo json_encode($return_json);
	}

	function get_search_product() {//OK
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$page        = $this->request->page;
		$q           = $this->request->q;
		$this->check_token($token,$customer_id);

		$perpage     = 20;
		$offset      = $perpage * ($page - 1);

		$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish','name_item LIKE' => '%'.$q.'%'));
		$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish','name_item LIKE' => '%'.$q.'%'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC'));

		$total_page = ceil($data_product_total->num_rows() / $perpage);
		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		if ($customer_id == '' || $customer['jenis_customer'] == 0) {
			$order = 'asc';
		} else {
			$order = 'desc';
		}
		if($data_product_total->num_rows() > 0) {
			if($data_product->num_rows() > 0) {
				foreach ($data_product->result() as $products) {
					$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $products->id))->result();
					if (count($list_price) > 0) {
						$product_price = $this->db->get_where('product_price', array('prod_id' => $products->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
						if (count($product_price) > 0) {
							$price = $product_price['price'];
						  	$price_old = $product_price['old_price'];
						} else {
							$price = $list_price[0]->price;
						  	$price_old = $list_price[0]->old_price;
						}
					} else {

						if ($customer['jenis_customer'] == '1') {
							$price     = $products->price;
							$price_old = $products->price_old;
						} else {
							if ($products->price_old_luar == 0) {
								$price     = $products->price_luar;
								$price_old = $products->price_old_luar;
							} else {
								$price     = $products->price;
								$price_old = $products->price_old_luar;
							}
						}
					}
					$data_json[] = array(
						'product_id'    => $products->id,
						'name_item'     => $products->name_item,
						'harga'         => $price,
						'harga_lama'    => $price_old,
						'berat'         => $products->weight,
						'foto'          => base_url().'media/images/original/'.$products->image,
						'img_thumbnail' => base_url().'media/images/thumb/'.$products->image,
						'img_medium'    => base_url().'media/images/medium/'.$products->image,
						'img_large'     => base_url().'media/images/large/'.$products->image,
						'min_order'     => $products->min_order,
						'product_type'  => $products->product_type,
						'best_seller'	=> $products->best_seller,
						'promo'			=> $products->promo,
					);
				}
				$return_json = array(
					'status'        => 'Success',
					'total_page'    => $total_page,
					'total_product' => $data_product_total->num_rows(),
					'product'       => $data_json
				);
			} else {
				$return_json = array(
					'status'        => 'Failed',
					'total_page'    => 1
				);
			}
		} else {
			$return_json = array(
				'status'        => 'Not Found',
				'total_page'    => 1
			);
		}
		echo json_encode($return_json);
	}

	function get_search_product_v2() {//OK
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$page        = $this->request->page;
		$q           = $this->request->q;
		$this->check_token($token,$customer_id);

		$perpage     = 20;
		$offset      = $perpage * ($page - 1);

		$program_diskon = $this->db->get_where('discounts', array('active' => 1))->row_array();
		$category_discount = explode('|', $program_diskon['product_categories']);
		$customer_type_discount = explode('|', $program_diskon['customer_types']);
		$tag_discount = explode('|', $program_diskon['product_tags']);

		$this->db->select('COUNT(*) AS total');
		$data_product_total = $this->main_model->get_detail('product',array('status' => 'Publish','name_item LIKE' => '%' . $q . '%'));

		$this->db->select('id, price, price_old, price_old_luar, name_item, image, category_id');
		$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish','name_item LIKE' => '%'.$q.'%'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC'));

		$total_page = ceil($data_product_total['total'] / $perpage);
		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		if ($customer_id == '' || $customer['jenis_customer'] == 0) {
			$order = 'asc';
		} else {
			$order = 'desc';
		}
		if ($data_product_total['total'] > 0) {
			foreach ($data_product->result() as $products) {
				$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $products->id))->result();
				if (count($list_price) > 0) {
					$product_price = $this->db->get_where('product_price', array('prod_id' => $products->id, 'cust_type_id' => $customer['jenis_customer']))->row_array();
					if (count($product_price) > 0) {
						$price = $product_price['price'];
					  	$price_old = $product_price['old_price'];
					} else {
						$price = $list_price[0]->price;
					  	$price_old = $list_price[0]->old_price;
					}
				} else {

					if ($customer['jenis_customer'] == '1') {
						$price     = $products->price;
						$price_old = $products->price_old;
					} else {
						if ($products->price_old_luar == 0) {
							$price     = $products->price_luar;
							$price_old = $products->price_old_luar;
						} else {
							$price     = $products->price;
							$price_old = $products->price_old_luar;
						}
					}
				}
				$tags = $this->db->select('PT.tag_id, NT.name')
					->join('product_tags PT', 'PT.tag_id = NT.id')
					->get_where('name_tag NT', array('PT.product_id' => $products->id))
					->result();

				$tag_product = array();
				foreach ($tags as $tag) {
					$tag_product[] = $tag->tag_id;
				}
				$compare_tag = array_diff($tag_product, $tag_discount);
				if (empty($program_diskon)) {
					$discount = false;
				} else {
					$today = date('Y-m-d');
					$discount = in_array($products->category_id, $category_discount) && count($compare_tag) == 0 && in_array($customer['jenis_customer'], $customer_type_discount) && $today >= $program_diskon['from_date'] && $today <= $program_diskon['to_date'];
				}
				$data_json[] = array(
					'product_id' => $products->id,
					'name_item'  => $products->name_item,
					'harga'      => $price,
					'harga_lama' => $price_old,
					'img_medium' => base_url('media/images/medium/' . $products->image),
					'tag'        => count($tags) > 0 ? $tags[0] : null,
					'discount'   => $discount
				);
			}
			$return_json = array(
				'status'        => 'Success',
				'total_page'    => $total_page,
				'total_product' => $data_product_total['total'],
				'product'       => $data_json
			);
		} else {
			$return_json = array(
				'status'        => 'Not Found',
				'total_page'    => 1
			);
		}
		echo json_encode($return_json);
	}

	function get_detail_product() { //OK
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$product_id  = $this->request->product_id;
		$this->check_token($token,$customer_id);

		$program_diskon = $this->db->get_where('discounts', array('active' => 1))->row_array();
		$category_discount = explode('|', $program_diskon['product_categories']);
		$customer_type_discount = explode('|', $program_diskon['customer_types']);
		$tag_discount = explode('|', $program_diskon['product_tags']);

		$products = $this->main_model->get_detail('product',array('id' => $product_id));

		// status stock "tampilkan(1), sembunyikan(2), hilangkan(3)"
		$status_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		$stok_limited = $this->main_model->get_detail('content',array('name' => 'stok_limited'));
		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
		if ($customer_id == '' || $customer['jenis_customer'] == 0) {
			$order = 'asc';
		} else {
			$order = 'desc';
		}
		$list_price = $this->db->order_by('cust_type_id', $order)->get_where('product_price', array('prod_id' => $products['id']))->result();
		if (count($list_price) > 0) {
			$product_price = $this->db->get_where('product_price', array('prod_id' => $products['id'], 'cust_type_id' => $customer['jenis_customer']))->row_array();
			if (count($product_price) > 0) {
				$price = $product_price['price'];
			  	$price_old = $product_price['old_price'];
			} else {
				$price = $list_price[0]->price;
			  	$price_old = $list_price[0]->old_price;
			}
		} else {

			if ($customer['jenis_customer'] == '1') {
				$price     = $products['price'];
				$price_old = $products['price_old'];
			} else {
				if ($products['price_old_luar'] == 0) {
					$price     = $products['price_luar'];
					$price_old = $products['price_old_luar'];
				} else {
					$price     = $products['price'];
					$price_old = $products['price_old_luar'];
				}
			}
		}

		if ($customer_id == '' || $customer['jenis_customer'] == 0) {
			$order = 'asc';
		} else {
			$order = 'desc';
		}
		$harga_grosir = null;
		$this->db->order_by('cust_type_id ', $order);
		$this->db->order_by('qty_awal', 'asc');
		$list_grosir = $this->main_model->get_list_where('harga_grosir', array('prod_id' => $product_id))->result();
		if (count($list_grosir) > 0) {
			$grosir = $this->main_model->get_list_where('harga_grosir', array('prod_id' => $product_id, 'cust_type_id' => $customer['jenis_customer']));
			if ($grosir->num_rows() > 0) {
				foreach ($grosir->result() as $grosir) {
					$harga_grosir[] = array(
						'qty'   => $grosir->qty_awal.' - '.$grosir->qty_akhir,
						'harga' => $grosir->price
					);
				}
			} else {
				$data_grosir = $this->db->get_where('harga_grosir', array(
					'cust_type_id' => $list_grosir[0]->cust_type_id,
					'prod_id' => $product_id
				));
				foreach ($data_grosir->result() as $data_grosir) {
					$harga_grosir[] = array(
						'qty'   => $data_grosir->qty_awal.' - '.$data_grosir->qty_akhir,
						'harga' => $data_grosir->price
					);
				}
			}
		}

		$tags = $this->db->select('PT.tag_id, NT.name')
			->join('product_tags PT', 'PT.tag_id = NT.id')
			->get_where('name_tag NT', array('PT.product_id' => $product_id))
			->result();

		$tag_product = array();
		foreach ($tags as $tag) {
			$tag_product[] = $tag->tag_id;
		}
		$compare_tag = array_diff($tag_product, $tag_discount);
		/*if (empty($program_diskon)) {
			$discount = false;
		} else {
			$today = date('Y-m-d');
			$discount = in_array($products['category_id'], $category_discount) && ((count($compare_tag) == 0 && count($tag_product) > 0) || !$program_diskon['product_tags']) && in_array($customer['jenis_customer'], $customer_type_discount) && $today >= $program_diskon['from_date'] && $today <= $program_diskon['to_date'];
		}*/
		$today = date('Y-m-d');
		$discount_v2 = in_array($products['category_id'], $category_discount) && in_array($customer['jenis_customer'], $customer_type_discount) && $today >= $program_diskon['from_date'] && $today <= $program_diskon['to_date'];
		$data_json =  array(
			'product_id'    => $products['id'],
			'category_id'   => $products['category_id'],
			'name_item'     => $products['name_item'],
			'harga'         => $price,
			'harga_lama'    => $price_old,
			'product_type'  => $products['product_type'],
			'berat'         => $products['weight'],
			'keterangan'    => $products['description'],
			'foto'          => $products['image'],
			'img_thumbnail' => base_url().'media/images/thumb/'.$products['image'],
			'img_medium'    => base_url().'media/images/medium/'.$products['image'],
			'img_large'     => base_url().'media/images/large/'.$products['image'],
			'min_order'     => $products['min_order'],
			'view_stock'    => $status_stock ['value'],
			'stok_limited'  => $stok_limited ['value'],
			'harga_grosir'  => $harga_grosir,
			//'discount'      => $discount
			'discount'      => $discount_v2
		);
		if ($products['video']) {
			$data_json['video'] = base_url('media/videos/' . $products['video']);
		}

		$data_variant = $this->db->order_by('variant', 'asc')->get_where('product_variant',array('prod_id' => $products['id'], 'available !=' => 'Delete'));

		if($data_variant->num_rows() > 0) {
			foreach($data_variant->result() as $variants) {
				if ($status_stock ['value'] != 3) {
					if ($variants->stock == 0) {
						$data_variant_product[] = array(
							'id_variant'   => $variants->id,
							'variant_name' => $variants->variant,
							'stock'        => 'HABIS',
							'available'    => $variants->available
						);
					} else {
						$stock = $variants->stock <= $stok_limited['value'] ? 'Stok Limited' : $variants->stock;
						$data_variant_product[] = array(
							'id_variant'   => $variants->id,
							'variant_name' => $variants->variant,
							'stock'        => $stock,
							'available'    => $variants->available
						);
					}
				} else {
					$data_variant_product[] = array(
						'id_variant'   => $variants->id,
						'variant_name' => $variants->variant,
						'available'    => $variants->available
					);
				}
			}
			$data_json['variant'] = $data_variant_product;
		} else {
			$data_json['variant'][] = array(
				'id_variant' => 0,
				'variant'    => null,
				'stock'      => 0
			);
		}

		$images_path[] = array(
			'url_thumb' => base_url().'media/images/thumb/'.$products['image'],
			'url'       => base_url().'media/images/large/'.$products['image'],
			'foto'      => $products['image']
		);

		$list_image = $this->main_model->get_list_where('rel_produk_image',array('prod_id' => $product_id));
		foreach($list_image->result() as $images) {
			if($images->image != null) {
				$images_path[] = array(
					'url_thumb' => base_url().'media/images/thumb/'.$images->image,
					'url'       => base_url().'media/images/large/'.$images->image,
					'foto'      => $images->image
				);
			}
		}
		$data_json['image_data'] = $images_path;
		$data_json['status_member'] = $this->checkMemberStatus($customer_id);
		echo json_encode($data_json);
	}

	function process_order_item() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);

		$prod_id    = $this->request->prod_id;
		$variant_id = $this->request->variant_id;
		$qty        = $this->request->qty;
		$notes      = $this->request->notes;
		$ref        = property_exists($this->request, 'ref') ? $this->request->ref : '';

		for ($q = 0; $q < count($qty); $q++) {
			$stock_variant = $this->main_model->get_detail('product_variant', array('id' => $variant_id[$q]));
			$check_stock_status = $stock_variant['stock'] - $qty[$q];
			if ($check_stock_status < 0) {
				$check_proses = 'batalkan';
				$data_json = array('status' => 'Failed', 'message' => 'Maaf Stock Produk Tidak Cukup');
				echo json_encode($data_json);
				exit;
			} else {

				// FUNGSI CHECK PROD_ID == VARIANT_ID
				if (intval($stock_variant['prod_id']) != intval($prod_id)) {
					$data_json = array(
						'status'          => 'Failed',
						'message'         => 'ERROR! PROD_ID AND VARIANT.PROD_ID NOT EQUIVALENT',
						'prod_id'         => $prod_id,
						'variant_prod_id' => $stock_variant['prod_id'],
						'variant_id'      => $variant_id[$q]
					);

					log_message('debug', 'ORDERLOG! PROD_ID AND VARIANT.PROD_ID NOT EQUIVALENT - PROD_ID : '.$prod_id.' , VARIANT.PROD_ID : '.$stock_variant['prod_id'],' , VARIANT.ID : '.$variant_id[$q]);

					echo json_encode($data_json);

					exit();
				}

				// END FUNGSI

				$check_proses = 'lanjutkan';
			}
		}

		if ($check_proses == 'lanjutkan') {
			if (($variant_id != null) and ($variant_id != 0)) {
				for ($v = 0; $v < count($qty); $v++) {
					if ($qty[$v] > 0) {
						$data_product = $this->main_model->get_detail('product',array('id' => $prod_id));
						$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
						$data_product_variant = $this->main_model->get_detail('product_variant',array('id' => $variant_id[$v]));
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

						$this->db->order_by('cust_type_id', 'desc');
						$this->db->order_by('qty_awal', 'asc');
						$list_grosir = $this->main_model->get_list_where('harga_grosir', array('prod_id' => $prod_id))->result();

						// status stock "tampilkan(1), sembunyikan(2), hilangkan(3)"
						$status_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
						if ($status_stock['value'] != 3) {
							if ($data_product_variant['stock'] > 0) {
								if ($data_product['status'] == 'Publish') {
									if ($qty[$v] >= $data_product['min_order']) {
										if ($qty[$v] <= $data_product_variant['stock']) {
											$list_price = $this->db->order_by('cust_type_id','desc')->get_where('product_price', array('prod_id' => $prod_id))->result();
											if (count($list_price) > 0) {
												$product_price = $this->db->get_where('product_price', array('prod_id' => $prod_id, 'cust_type_id' => $data_customer['jenis_customer']))->row_array();
												if (count($product_price) > 0) {
													$harga = $product_price['price'];
												} else {
													$harga = $list_price[0]->price;
												}
											} else {
												if ($data_customer['jenis_customer'] == '1') {
													if (count($list_grosir) > 0) {
														$grosir = $this->main_model->get_list_where('harga_grosir', array('prod_id' => $prod_id, 'cust_type_id' => $data_customer['jenis_customer']));
														if ($grosir->num_rows() > 0) {
															$harga = $data_product['price'];
															foreach ($grosir->result() as $grosir) {
																if (array_sum($qty) >= $grosir->qty_awal && array_sum($qty) <= $grosir->qty_akhir ) {
																	$harga = $grosir->price;
																}
															}
														} else {
															$harga = $data_product['price'];
															$data_grosir = $this->db->get_where('harga_grosir', array(
																'cust_type_id' => $list_grosir[0]->cust_type_id,
																'prod_id' => $prod_id
															));
															foreach ($data_grosir->result() as $data_grosir) {
																if (array_sum($qty) >= $harga_grosir->qty_awal && array_sum($qty) <= $harga_grosir->qty_akhir ) {
																	$harga = $harga_grosir->price;
																}
															}
														}
													} else {
														$harga = $data_product['price'];
													}
												} else {
													if (count($list_grosir) > 0) {
														$grosir = $this->main_model->get_list_where('harga_grosir', array('prod_id' => $prod_id, 'cust_type_id' => $data_customer['jenis_customer']));
														if ($grosir->num_rows() > 0) {
															if ($data_product['price_old_luar'] == 0) {
																$harga = $data_product['price_luar'];
															} else {
																$harga = $data_product['price'];
															}
															foreach ($grosir->result() as $grosir) {
																if (array_sum($qty) >= $grosir->qty_awal && array_sum($qty) <= $grosir->qty_akhir ) {
																	$harga = $grosir->price;
																}
															}
														} else {
															if ($data_product['price_old_luar'] == 0) {
																$harga = $data_product['price_luar'];
															} else {
																$harga = $data_product['price'];
															}
															$data_grosir = $this->db->get_where('harga_grosir', array(
																'cust_type_id' => $list_grosir[0]->cust_type_id,
																'prod_id' => $prod_id
															));
															foreach ($data_grosir->result() as $data_grosir) {
																if (array_sum($qty) >= $harga_grosir->qty_awal && array_sum($qty) <= $harga_grosir->qty_akhir ) {
																	$harga = $harga_grosir->price;
																}
															}
														}
													} else {
														if ($data_product['price_old_luar'] == 0) {
															$harga = $data_product['price_luar'];
														} else {
															$harga = $data_product['price'];
														}
													}
												}
											}

											$subtotal = $harga * $qty[$v];
											$now = date('Y-m-d H:i:s');
											$data_insert = array(
												'customer_id'    => $data_customer['id'],
												'prod_id'        => $prod_id,
												'order_datetime' => $now,
												'due_datetime'   => $due_datetime,
												'variant_id'     => $variant_id[$v],
												'qty'            => $qty[$v],
												'price'          => $harga,
												'subtotal'       => $subtotal,
												'modal'          => $data_product['price_production'],
												'subtotal_modal' => $data_product['price_production'] * $qty[$v],
												'order_status'   => 'Keep',
												'order_payment'  => 'Unpaid',
												'tipe'           => $data_product['product_type'],
												'notes'          => $notes,
												'ref'            => 'Android',
												'jenis_customer' => $data_customer['jenis_customer']
											);
											$this->db->trans_start();
											$this->db->insert('orders_item',$data_insert);
											$order_item_id = $this->db->insert_id();

											// Kurangi stok
											$update_stock = $data_product_variant['stock'] - $qty[$v];

											$data_update_stock = array('stock' => $update_stock);
											$where = array('id' => $variant_id[$v]);

											$this->db->update('product_variant', $data_update_stock, $where);

											$stock_histories = array(
												'prod_id'       => $prod_id,
												'variant_id'    => $variant_id[$v],
												'prev_stock'    => $data_product_variant['stock'],
												'stock'         => $update_stock,
												'qty'           => '-' . $qty[$v],
												'price'         => $harga,
												'order_item_id' => $order_item_id,
												'customer_id'   => $customer_id,
												'created_at'    => $now,
												'ref'           => $ref ? 'Android' : ucfirst($ref)
											);
											$this->db->insert('stock_histories', $stock_histories);
											$this->db->trans_complete();
											$data_json = array('status' => 'Success','message' => 'Pesanan Berhasil Dibuat');
										} else {
											$data_json = array('status' => 'Failed','message' => 'Maaf Produk yang anda pesan melebihi stock yang tersedia');
										}
									} else {
										$data_json = array('status' => 'Failed','message' => 'Maaf Minimum order produk '.$data_product['min_order']);
									}
								} else {
									$data_json = array('status' => 'Failed','message' => 'Maaf Produk yang anda pesan saat ini sedang tidak tersedia');
								}
							} else {
								$data_json = array('status' => 'Failed', 'message' => 'Maaf Produk Habis');
							}
						} else {
							if ($data_product['status'] == 'Publish') {
								if(($qty[$v] >= $data_product['min_order']) ) {
									if($data_customer['jenis_customer'] == 'Lokal') {
										if ($data_product['price_old'] != 0) {
											$harga = $data_product['price_old'];
										} else {
											$harga = $data_product['price'];
										}
									} else {
										if ($data_product['price_old_luar'] != 0) {
											$harga = $data_product['price_old_luar'];
										} else {
											$harga = $data_product['price_luar'];
										}
									}
									$subtotal = $harga * $qty[$v];

									$data_insert = array (
										'customer_id'    => $data_customer['id'],
										'prod_id'        => $prod_id,
										'order_datetime' => date('Y-m-d H:i:s'),
										'due_datetime'   => $due_datetime,
										'variant_id'     => $variant_id[$v],
										'qty'            => $qty[$v],
										'price'          => $harga,
										'subtotal'       => $subtotal,
										'modal'          => $data_product['price_production'],
										'subtotal_modal' => $data_product['price_production'] * $qty[$v],
										'order_status'   => 'Keep',
										'order_payment'  => 'Unpaid',
										'tipe'           => $data_product['product_type'],
										'ref'            => 'Android',
										'jenis_customer' => $data_customer['jenis_customer']
									);

									$this->db->insert('orders_item',$data_insert);
									$data_json = array('status' => 'Success');
								} else {
									$data_json = array('status' => 'Failed','message' => 'Maaf Minimum order produk '.$data_product['min_order']);
								}
							} else {
								$data_json = array('status' => 'Failed','message' => 'Maaf Produk yang anda pesan saat ini sedang tidak tersedia');
							}
						}
					}
				}
			} else {
				$data_json = array('status' => 'Failed');
			}
		} else {
			$data_json = array('status' => 'Failed');
		}
		echo json_encode($data_json);
	}

	function list_order() {//OK
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$data_order_item = $this->main_model->get_list_where('orders_item',array('order_status' => 'Keep','order_id' => '0','customer_id' => $customer_id));

		$total_weight = 0;
		$total_amount = 0;
		$total_qty = 0;

		foreach ($data_order_item->result() as $orders) {
			$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));
			if($orders->variant_id == 0) {
				$data_variant['variant'] = 'All Var';
			} else {
				$data_variant = $this->main_model->get_detail('product_variant',array('id' => $orders->variant_id));
			}

			$weight       = $data_product['weight'] * $orders->qty;
			$total_weight = $total_weight + $weight;
			$total_amount = $total_amount + $orders->subtotal;
			$total_qty    = $total_qty +  $orders->qty;

			$data_json['order'][] = array(
				'order_item_id'  => $orders->id,
				'prod_id'        => $orders->prod_id,
				'prod_name'      => $data_product['name_item'],
				'order_datetime' => $orders->order_datetime,
				'order_date'     => date('d-m-Y', strtotime($orders->order_datetime)),
				'order_time'     => date('H:i:s', strtotime($orders->order_datetime)),
				'due_datetime'   => $orders->due_datetime,
				'due_date'       => date('d-m-Y', strtotime($orders->due_datetime)),
				'due_time'       => date('H:i:s', strtotime($orders->due_datetime)),
				'variant'        => $data_variant['variant'],
				'qty'            => $orders->qty,
				'price'          => $orders->price,
				'weight'         => $weight,
				'subtotal'       => $orders->subtotal,
				'notes'          => $orders->notes,
				'image'          => base_url().'media/images/large/'.$data_product['image']
			);
		}

		$fitur_rekap = $this->main_model->get_detail('content', array('name' => 'fitur_rekap'));
		$data_json['fitur_rekap'] = $fitur_rekap['value'];
		$data_json['total_amount'] = $total_amount;
		$data_json['total_weight'] = $total_weight;
		$data_json['total_qty'] = $total_qty;
		$data_json['status_member'] = $this->checkMemberStatus($customer_id);
		echo json_encode($data_json);
	}

	function dropship() {
		$token          = $this->request->token;
		$customer_id    = $this->request->customer_id;
		$ongkir         = $this->request->ongkir;
		$weight         = $this->request->weight;
		$ekspedisi      = $this->request->ekspedisi;
		//$tarif_tipe     = $this->request->tarif_tipe;
		$tarif_tipe     = property_exists($this->request, 'tarif_tipe') ? $this->request->tarif_tipe : '';
		$jenis_customer = $this->request->jenis_customer;
		$order_item_id  = $this->request->order_item_id;
		//$address_id     = $this->request->address_id;
		$address_id     = property_exists($this->request, 'address_id') ? $this->request->address_id : '';
		$ref            = $this->request->ref;
		$point          = $this->request->point;
		$kurs_point     = $this->request->kurs_point;
		$resi           = property_exists($this->request, 'resi') ? $this->request->resi : '';
		$manualAddress  = property_exists($this->request, 'manualAddress') ? $this->request->manualAddress : '';
		$this->check_token($token,$customer_id);

		$item_id = array();
		foreach ($order_item_id as $value) {
			$item_id[] = $value->id;
		}
		$cek_order_item = $this->db->select('COUNT(*) AS total')
			->where_in('id', $item_id)->get('orders_item')->row_array();

		if (count($order_item_id) < 1 && $cek_order_item['total'] > 0) {
			$data_json = array('status' => 'Failed');
			echo json_encode($data_json);
			exit;
		}

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
		//$address = $this->main_model->get_detail('addresses', array('id' => $address_id));
		
		if ($manualAddress) {
			$address = json_decode(json_encode($this->request->address), true);
		} else {
			$address = $this->main_model->get_detail('addresses', array('id' => $address_id));
		}
		
		$nominal_point = $point * $kurs_point;

		$data_insert = array(
			'customer_id'       => $customer_id,
			'order_datetime'    => date('Y-m-d H:i:s'),
			'due_datetime'      => $due_datetime,
			'shipping_from'     => $address['sender'],
			'shipping_to'       => $address['recipient_name'],
			'address_recipient' => $address['address'],
			'phone_recipient'   => $address['recipient_phone'],
			'kecamatan_id'      => $address['subdistrict_id'],
			'kota_id'           => $address['city_id'],
			'prov_id'           => $address['province_id'],
			'postal_code'       => $address['postal_code'],
			'ekspedisi'         => $ekspedisi,
			'tarif_tipe'        => $tarif_tipe,
			'shipping_fee'      => $ongkir,
			'shipping_weight'   => $weight,
			'order_status'      => 'Dropship',
			'order_payment'     => 'Unpaid',
			'ref'               => ucfirst($ref),
			'jenis_customer'    => $jenis_customer,
			'point'             => $point,
			'kurs_point'        => $kurs_point,
			'nominal_point'     => $nominal_point,
			'resi'     	 	    => $resi,
			'get_point'         => 1,
			'added_point'       => 0,
		);
		$this->db->trans_start();
		$this->db->insert('orders',$data_insert);
		$order_id = $this->db->insert_id();

		$total_discount = 0;

		$order_total_shop = 0;
		foreach ($order_item_id as $value) {
			$val_id = is_object($value) ? $value->id : $value;
			$where_order_item = array('id' => $val_id);

			$detail_order_item = $this->main_model->get_detail('orders_item', $where_order_item);
			$data_update_order_item = array(
				'order_id'     => $order_id,
				'order_status' => 'Dropship',
				'qty'          => $detail_order_item['qty']
			);
			if ($value->qty < $detail_order_item['qty'] && is_object($value)) {
				$data_update_order_item['qty'] = $value->qty;
				$data_update_order_item['subtotal'] = $value->qty * $detail_order_item['price'];
				$product = $this->main_model->get_detail('product', array('id' => $detail_order_item['prod_id']));
				$data_update_order_item['subtotal_modal'] = $value->qty * $product['price_production'];

				$data_order_item_new = $detail_order_item;
				$data_order_item_new['qty'] = $detail_order_item['qty'] - $value->qty;
				$data_order_item_new['subtotal'] = $detail_order_item['price'] * $value->qty;
				unset($data_order_item_new['id']);
				$this->db->insert('orders_item', $data_order_item_new);

				$order_item_id = $this->db->insert_id();
				$stock_histories = array(
					'prod_id'       => $detail_order_item['prod_id'],
					'variant_id'    => $detail_order_item['variant_id'],
					'qty'           => '-' . $data_order_item_new['qty'],
					'price'         => $detail_order_item['price'],
					'order_item_id' => $order_item_id,
					'customer_id'   => $customer_id,
					'created_at'    => $detail_order_item['order_datetime'],
					'ref'           => ucfirst($ref)
				);
				$this->db->insert('stock_histories', $stock_histories);

				$update_stock_histories['qty'] = '-' . $value->qty;
				$this->db->update('stock_histories', $update_stock_histories, array('order_item_id' => $order_item_id));
			}

			$discount = $this->getDiscount($detail_order_item, $data_update_order_item['qty']);

			$data_update_order_item['discount'] = $discount;
			$data_update_order_item['program_diskon'] = $discount;
			$total_discount += $discount;

			$where = array('id' => $val_id);
			$this->db->update('orders_item', $data_update_order_item, $where);
			$this_order_item = $this->main_model->get_detail('orders_item', array('id' => $val_id));
			$order_total_shop = $order_total_shop + $this_order_item['subtotal'];
		}

		$data_new_total = $order_total_shop + $ongkir - $total_discount;
		$data_new_update = array(
			'subtotal'       => $order_total_shop,
			'total'          => $data_new_total - $nominal_point,
			'diskon'         => $total_discount,
			'program_diskon' => $total_discount
		);
		$where = array('id' => $order_id);
		$this->db->update('orders', $data_new_update, $where);

		$this->db->trans_complete();

		$data_json = array('status' => 'Success');
		echo json_encode($data_json);
	}

	function process_dropship() { //OK
		$token          = $this->request->token;
		$customer_id    = $this->request->customer_id;
		$from           = $this->request->from;
		$to             = $this->request->to;
		$ongkir         = $this->request->ongkir;
		$address        = $this->request->address_recipient;
		$phone          = $this->request->phone_recipient;
		$postcode       = $this->request->postal_code;
		$weight         = $this->request->weight;
		$prov_id        = $this->request->prov_id;
		$kota_id        = $this->request->kota_id;
		$kecamatan_id   = $this->request->kecamatan_id;
		$ekspedisi      = $this->request->ekspedisi;
		$tarif_tipe     = $this->request->tarif_tipe;
		$jenis_customer = $this->request->jenis_customer;
		$order_item_id  = $this->request->order_item_id;
		$this->check_token($token,$customer_id);

		$item_id = array();
		foreach ($order_item_id as $value) {
			$item_id[] = $value->id;
		}
		$cek_order_item = $this->db->select('COUNT(*) AS total')
			->where_in('id', $item_id)->get('orders_item')->row_array();

		if (count($order_item_id) < 1 && $cek_order_item['total'] > 0) {
			$data_json = array('status' => 'Failed');
			echo json_encode($data_json);
			exit;
		}

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

		$data_insert = array(
			'customer_id'       => $customer_id,
			'order_datetime'    => date('Y-m-d H:i:s'),
			'due_datetime'      => $due_datetime,
			'shipping_from'     => $from,
			'shipping_to'       => $to,
			'address_recipient' => $address,
			'phone_recipient'   => $phone,
			'kecamatan_id'		=> $kecamatan_id,
			'kota_id'           => $kota_id,
			'prov_id'           => $prov_id,
			'ekspedisi'			=> $ekspedisi,
			'tarif_tipe'		=> $tarif_tipe,
			'shipping_fee'      => $ongkir,
			'shipping_weight'   => $weight,
			'order_status'      => 'Dropship',
			'order_payment'     => 'Unpaid',
			'ref'               => 'Android_New',
			'jenis_customer'	=> $jenis_customer
		);
		if ($postcode) {
			$data_insert['postal_code'] = $postcode;
		}
		$this->db->insert('orders',$data_insert);
		$order_id = $this->db->insert_id();

		$order_total_shop = 0;
		foreach ($order_item_id as $value) {
			$val_id = is_object($value) ? $value->id : $value;
			$where_order_item = array('id' => $val_id);

			$detail_order_item = $this->main_model->get_detail('orders_item', $where_order_item);
			$data_update_order_item = array(
				'order_id'      => $order_id,
				'order_status'  => 'Dropship',
			);
			if ($value->qty < $detail_order_item['qty'] && is_object($value)) {
				$data_update_order_item['qty'] = $value->qty;
				$data_update_order_item['subtotal'] = $value->qty * $detail_order_item['price'];
				$product = $this->main_model->get_detail('product', array('id' => $detail_order_item['prod_id']));
				$data_update_order_item['subtotal_modal'] = $value->qty * $product['price_production'];

				$data_order_item_new = $detail_order_item;
				$data_order_item_new['qty'] = $detail_order_item['qty'] - $value->qty;
				$data_order_item_new['subtotal'] = $detail_order_item['price'] * $value->qty;
				unset($data_order_item_new['id']);
				$this->db->insert('orders_item', $data_order_item_new);

				$order_item_id = $this->db->insert_id();
				$stock_histories = array(
					'prod_id'       => $detail_order_item['prod_id'],
					'variant_id'    => $detail_order_item['variant_id'],
					'qty'           => '-' . $data_order_item_new['qty'],
					'price'         => $detail_order_item['price'],
					'order_item_id' => $order_item_id,
					'customer_id'   => $customer_id,
					'created_at'    => $detail_order_item['order_datetime'],
					'ref'           => ucfirst($ref)
				);
				$this->db->insert('stock_histories', $stock_histories);

				$update_stock_histories['qty'] = '-' . $value->qty;
				$this->db->update('stock_histories', $update_stock_histories, array('order_item_id' => $order_item_id));
			}
			$where = array('id' => $val_id);
			$this->db->update('orders_item',$data_update_order_item,$where);
			// GET this order items
			$this_order_item = $this->main_model->get_detail('orders_item',array('id' => $val_id));
			$order_total_shop = $order_total_shop + $this_order_item['subtotal'];
		}
		// Check if Data Not Valid
		$data_new_total = $order_total_shop + $ongkir;
		$data_new_update = array('subtotal' => $order_total_shop,'total' => $data_new_total);
		$where = array('id' => $order_id);
		$this->db->update('orders',$data_new_update,$where);

		$data_json = array('status' => 'Success');
		echo json_encode($data_json);
	}

	function process_rekap() {//OK
		$token         = $this->request->token;
		$customer_id   = $this->request->customer_id;
		$weight        = $this->request->weight;
		$order_item_id = $this->request->order_item_id;
		$notes         = property_exists($this->request, 'notes') ? $this->request->notes : '';
		$point         = property_exists($this->request, 'point') ? $this->request->point : 0;
		$kurs_point    = $this->detailContent('point_to_nominal')['value'];
		$this->check_token($token, $customer_id);

		$item_id = array();
		foreach ($order_item_id as $value) {
			$item_id[] = $value->id;
		}
		$cek_order_item = $this->db->select('COUNT(*) AS total')
			->where_in('id', $item_id)->get('orders_item')->row_array();

		$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));

		if (count($order_item_id) > 0 && $cek_order_item['total'] > 0 && $point <= $customer['point']) {
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

			$nominal_point = $point * $kurs_point;

			$data_insert = array(
				'customer_id'     => $customer_id,
				'order_datetime'  => date('Y-m-d H:i:s'),
				'due_datetime'    => $due_datetime,
				'shipping_weight' => $weight,
				'order_status'    => 'Keep',
				'order_payment'   => 'Unpaid',
				'ref'             => 'Android_New',
				'jenis_customer'  => $customer['jenis_customer'],
				'point'           => $point,
				'kurs_point'      => $kurs_point,
				'nominal_point'   => $nominal_point,
				'get_point'       => 1,
				'added_point'     => 0,
			);
			if ($notes != null || $notes != '') {
				$data_insert['notes'] = $notes;
			}
			$this->db->trans_start();
			$this->db->insert('orders',$data_insert);

			$order_id = $this->db->insert_id();

			if ($point > 0) {
				$point_customer = $customer['point'] - (int)$point;

				$point_history = array(
					'customer_id' => $customer_id,
					'point_prev'  => $customer['point'],
					'point_out'   => (int)$point,
					'point_end'   => (int)$point_customer,
					'order_id'    => $order_id,
					'note'        => 'Penggunaan Point',
				);
				$this->db->insert('point_histories', $point_history);
				$this->db->where('id', $customer_id)
					->update('customer', array('point' => (int)$point_customer));
			}

			$total_discount = 0;

			$order_total_shop = 0;

			foreach ($order_item_id as $item) {
				$where_order_item = array('id' => $item->id);

				$detail_order_item = $this->main_model->get_detail('orders_item', $where_order_item);
				$data_update_order_item = array(
					'order_id'      => $order_id,
					'order_status'  => 'Keep',
				);
				if ($item->qty < $detail_order_item['qty']) {
					$data_update_order_item['order_id'] = 0;
					$data_update_order_item['order_status'] = 'Keep';
					$data_update_order_item['qty'] = $detail_order_item['qty'] - $item->qty;
					$data_update_order_item['subtotal'] = $data_update_order_item['qty'] * $detail_order_item['price'];
					$product = $this->main_model->get_detail('product', array('id' => $detail_order_item['prod_id']));
					$data_update_order_item['subtotal_modal'] = $data_update_order_item['qty'] * $product['price_production'];

					$data_order_item_new = $detail_order_item;
					$data_order_item_new['order_id'] = $order_id;
					$data_order_item_new['order_status'] = 'Keep';
					$data_order_item_new['qty'] = $item->qty;
					$data_order_item_new['subtotal'] = $detail_order_item['price'] * $item->qty;
					$data_order_item_new['subtotal_modal'] = $product['price_production'] * $item->qty;
					$data_order_item_new['order_datetime'] = date('Y-m-d H:i:s');

					$discount = $this->getDiscount($data_order_item_new, $item->qty);

					$data_order_item_new['discount'] = $discount;
					$data_order_item_new['program_diskon'] = $discount;
					unset($data_order_item_new['id']);

					$this->db->insert('orders_item', $data_order_item_new);
					$order_item_id = $this->db->insert_id();

					$stock_histories = $this->db->get_where('stock_histories', array('order_item_id >=' => $item->id, 'variant_id' => $detail_order_item['variant_id']))->result();

					$index = 0;
					foreach ($stock_histories as $history) {
						$this->db->update('stock_histories', array(
							'stock'      => $history->stock + $item->qty,
							'prev_stock' => $index == 0 ? $history->prev_stock : $history->prev_stock + $item->qty,
						), array(
							'id' => $history->id,
						));
						$index++;
					}

					$variant = $this->main_model->get_detail('product_variant', array('id' => $detail_order_item['variant_id']));

					$stock_histories = array(
						'prod_id'       => $detail_order_item['prod_id'],
						'variant_id'    => $detail_order_item['variant_id'],
						'prev_stock'    => $variant['stock'] + $item->qty,
						'stock'         => $variant['stock'],
						'qty'           => '-' . $data_order_item_new['qty'],
						'price'         => $detail_order_item['price'],
						'order_item_id' => $order_item_id,
						'customer_id'   => $customer_id,
						'created_at'    => $data_order_item_new['order_datetime'],
						'ref'           => ucfirst($ref)
					);
					$this->db->insert('stock_histories', $stock_histories);

					$update_stock_histories['qty'] = '-' . ($detail_order_item['qty'] - $item->qty);
					$this->db->update('stock_histories', $update_stock_histories, array('order_item_id' => $item->id));

					$order_total_shop += $data_order_item_new['subtotal'];
				} else {
					$discount = $this->getDiscount($detail_order_item, $detail_order_item['qty']);

					$data_update_order_item['discount'] = $discount;
					$data_update_order_item['program_diskon'] = $discount;

					$order_total_shop += $detail_order_item['subtotal'];
				}
				$total_discount += $discount;
				$this->db->update('orders_item', $data_update_order_item, $where_order_item);
			}

			$data_new_total = $order_total_shop - $total_discount;
			$data_new_update = array(
				'subtotal'       => $order_total_shop,
				'total'          => $data_new_total - $nominal_point,
				'diskon'         => $total_discount,
				'program_diskon' => $total_discount
			);
			$where = array('id' => $order_id);
			$this->db->update('orders',$data_new_update, $where);
			$this->db->trans_complete();

			$data_json = array('status' => 'Success');
		} else {
			$data_json = array('status' => 'Failed');
		}
		echo json_encode($data_json);
	}

	private function getDiscount($order_item, $qty) {
	$discount = 0;
	$program_diskon = $this->db->get_where('discounts', array('active' => 1))->row_array();
	if (!empty($program_diskon)) {
		$now = date('Y-m-d');
		$range_date = $now >= $program_diskon['from_date'] && $now <= $program_diskon['to_date'];
		if ($range_date) {
			$range_qty = $qty >= $program_diskon['min_qty'] && $qty <= $program_diskon['max_qty'];
			if ($range_qty) {
				$customer_id = $order_item['customer_id'];
				$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
				$customer_type = in_array($customer['jenis_customer'], explode('|', $program_diskon['customer_types']));
				
				if ($customer_type) {
					$product = $this->main_model->get_detail('product', array('id' => $order_item['prod_id']));
					$category = in_array($product['category_id'], explode('|', $program_diskon['product_categories']));
					if ($category) {
						$price = $order_item['price'];
						if ($program_diskon['discount_type'] == 'percent') {
							$discount = ($program_diskon['amount'] * $price) / 100;
						} else {
							$discount = $program_diskon['amount'];
						}
						$discount = $discount * $order_item['qty'];
					}
				}
				
			}
		}
	}
	return $discount;
}

/*private function getDiscount($order_item, $qty) {
	$discount = 0;
	$program_diskon = $this->db->get_where('discounts', array('active' => 1))->row_array();
	if (!empty($program_diskon)) {
		$now = date('Y-m-d');
		$range_date = $now >= $program_diskon['from_date'] && $now <= $program_diskon['to_date'];
		if ($range_date) {
			$range_qty = $qty >= $program_diskon['min_qty'] && $qty <= $program_diskon['max_qty'];
			if ($range_qty) {
				$customer_id = $order_item['customer_id'];
				$customer = $this->main_model->get_detail('customer', array('id' => $customer_id));
				$customer_type = in_array($customer['jenis_customer'], explode('|', $program_diskon['customer_types']));
				if ($customer_type) {
					$product_tags = $this->db->select('COUNT(*) AS total')
					->where('product_id', $order_item['prod_id'])
					->where_in('tag_id', explode('|', $program_diskon['product_tags']))
					->get('product_tags')->row_array();
					if ($product_tags['total'] > 0) {
						$product = $this->main_model->get_detail('product', array('id' => $order_item['prod_id']));
						$category = in_array($product['category_id'], explode('|', $program_diskon['product_categories']));
						if ($category) {
							$price = $order_item['price'];
							if ($program_diskon['discount_type'] == 'percent') {
								$discount = ($program_diskon['amount'] * $price) / 100;
							} else {
								$discount = $program_diskon['amount'];
							}
							$discount = $discount * $order_item['qty'];
						}
					}
				}
			}
		}
	}
	return $discount;
}*/
	function get_data_order() {
		$token         = $this->request->token;
		$customer_id   = $this->request->customer_id;
		$order_payment = $this->request->order_payment;
		$page          = $this->request->page;
		$keyword       = property_exists($this->request, 'keyword') ? $this->request->keyword : '';
		$this->check_token($token, $customer_id);

		$perpage = 50;
		$offset = $perpage * ($page - 1);

		if ($order_payment != 'Canceled') {

			$where_order = 'customer_id = '.$customer_id.' AND order_payment = "'.$order_payment.'" AND order_status != "Cancel"';
			if ($keyword != '') {
				$where_order .= ' AND (id LIKE "%'.$keyword.'%" OR order_datetime LIKE "%'.$keyword.'%" OR total LIKE "%'.$keyword.'%" OR notes LIKE "%'.$keyword.'%")';
			}
			$data_order_total = $this->db->get_where('orders', $where_order)->num_rows();
			$data_order = $this->main_model->get_list_where('orders', $where_order, array('perpage' => $perpage, 'offset' => $offset), array('by' => 'id','sorting' => 'DESC'));

			if ($data_order->num_rows() > 0) {
				foreach ($data_order->result() as $orders) {
					// $orders_item = $this->db->get_where('orders_item', array('order_id' => $orders->id));
					// $notes = [];
					// foreach ($orders_item->result() as $item) {
					// 	$notes[] = '<p>'.$item->notes.'</p>';
					// }

					if ($order_payment == 'Paid') {
						$payment_method_id = $this->main_model->get_detail('payment_method', array('id' => $orders->payment_method_id));
						if (!empty($payment_method_id)) {
							$payment_method = $payment_method_id['name'];
						} else {
							$payment = $this->db->where(array('order_id' => $orders->id, 'status' => 'Approve'))
								->get('confirmation')->row_array();

							if (!empty($payment)) {
								$payment_method = $payment['bank'];
							} else {
								$payment_method = 'Cash';
							}
						}
					} else {
						$payment_method = null;
					}

					$data_json[] = array(
						'order_id'     => $orders->id,
						'order_date'   => date('d-m-Y', strtotime($orders->order_datetime)),
						'order_time'   => date('h.i a', strtotime($orders->order_datetime)),
						// 'notes'        => implode(' ', $notes),
						'notes'		   => $orders->notes,
						'total'        => 'Rp. '.number_format($orders->total, 0, ',', '.'),
						'order_status' => $orders->order_status,
						'payment'      => $payment_method
					);

				}
				$return_json = array(
					'status'      => 'Success',
					'list'        => $data_json,
					'order_total' => $data_order_total
				);
			} else {
				$return_json = array('status' => 'Failed ');
			}
		} else {
			$where_order = 'customer_id = '.$customer_id;
			if ($keyword != '') {
				$where_order .= ' AND (A.order_datetime LIKE "%'.$keyword.'%" OR A.price LIKE "%'.$keyword.'%" OR B.name_item LIKE "%'.$keyword.'%" OR C.variant LIKE "%'.$keyword.'%")';
			}
			$this->db->select('A.*, B.name_item, C.variant');
			$this->db->join('product B', 'B.id = A.prod_id', 'left');
			$this->db->join('product_variant C', 'C.id = A.variant_id', 'left');
			$data_order_total = $this->db->get_where('orders_item_cancel A', $where_order)->num_rows();

			$this->db->select('A.*, B.name_item, C.variant');
			$this->db->join('product B', 'B.id = A.prod_id', 'left');
			$this->db->join('product_variant C', 'C.id = A.variant_id', 'left');
			$data_order = $this->main_model->get_list_where('orders_item_cancel A', $where_order, array('perpage' => $perpage, 'offset' => $offset), array('by' => 'id','sorting' => 'DESC'));

			if ($data_order->num_rows() > 0) {
				foreach ($data_order->result() as $orders) {
					$data_json[] = array(
						'order_id'   => $orders->id,
						'order_date' => date('d-m-Y', strtotime($orders->order_datetime)),
						'order_time' => date('h.i a', strtotime($orders->order_datetime)),
						'product'    => $orders->name_item,
						'variant'    => $orders->variant,
						'qty'        => $orders->qty,
						'price'      => 'Rp. '.number_format($orders->price, 0, ',', '.'),
					);
				}
				$return_json = array(
					'status'      => 'Success',
					'list'        => $data_json,
					'order_total' => $data_order_total
				);
			} else {
				$return_json = array('status' => 'Failed ');
			}
		}
		echo json_encode($return_json);
	}

	function get_data_order_detail() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$order_id    = $this->request->order_id;
		$this->check_token($token,$customer_id);

		$data_check_order = $this->main_model->get_list_where('orders', array('id' => $order_id,'customer_id' => $customer_id, 'order_status !=' => 'Cancel'));
		if ($data_check_order->num_rows() > 0) {
			$data_order = $data_check_order->row_array();
			$data_prov = '';
			$data_kota = '';
			$data_kec = '';
			if ($data_order['prov_id'] != 0) {
				$province = $this->main_model->get_detail('provinces', array('province_id' => $data_order['prov_id']));
				$data_prov = $province['province'];
				$city = $this->main_model->get_detail('cities', array('city_id' => $data_order['kota_id']));
				$data_kota = $city['type'] . ' ' . $city['city_name'];
				$subdistrict = $this->main_model->get_detail('subdistricts', array('subdistrict_id' => $data_order['kecamatan_id']));
				$data_kec = $subdistrict['subdistrict_name'];
			}
			$data_json = array(
				'status'            => 'Success',
				'order_id'          => $data_order['id'],
				'order_datetime'    => $data_order['order_datetime'],
				'order_date'        => date('d M Y', strtotime($data_order['order_datetime'])),
				'order_time'        => date('h:i:s', strtotime($data_order['order_datetime'])),
				'shipping_fee'      => $data_order['shipping_fee'],
				'shipping_weight'   => $data_order['shipping_weight'],
				'shipping_from'     => $data_order['shipping_from'],
				'shipping_to'       => $data_order['shipping_to'],
				'shipping_status'   => $data_order['shipping_status'],
				'resi'              => $data_order['resi'],
				'address_recipient' => $data_order['address_recipient'],
				'phone_recipient'   => $data_order['phone_recipient'],
				'postal_code'       => $data_order['postal_code'],
				'provinsi'          => $data_prov,
				'kota'              => $data_kota,
				'kecamatan'         => $data_kec,
				'ekspedisi'         => strtoupper($data_order['ekspedisi']).' - '.$data_order['tarif_tipe'],
				'total'             => $data_order['total'],
				'program_diskon'    => $data_order['program_diskon'],
				'diskon'            => $data_order['diskon'],
				'order_status'      => $data_order['order_status'],
				'order_type'        => $data_order['order_status'],
				'order_payment'     => $data_order['order_payment'],
				'nominal_point'     => $data_order['nominal_point'],
				'notes'             => $data_order['notes'],
			);

			if ($data_order['order_payment'] == 'Unpaid') {
				$confirmation = $this->main_model->get_detail('confirmation', array('order_id' => $data_order['id'], 'status !=' => 'Reject'));
				if (!empty($confirmation)) {
					if ($confirmation['status'] == 'Pending') {
						$data_json['order_status'] = 'Menunggu Dikonfirmasi';
					}
				} else {
					$data_json['order_status'] = 'Belum Melakukan Konfirmasi Pembayaran';
				}
				$data_json['shipping_status'] = 'Belum Bayar';
			} else if ($data_order['order_payment'] == 'Paid' && !$data_order['resi']) {
				$data_json['order_status'] = 'Dipacking';
				$data_json['shipping_status'] = 'Dipacking';
			} else if ($data_order['resi']) {
				$data_json['order_status'] = 'Dikirim';
				$data_json['shipping_status'] = 'Dikirim';
			}

			$data_order_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id, 'order_status !=' => 'Cancel'));
			foreach ($data_order_item->result() as $orders_item) {
				$data_this_product = $this->main_model->get_detail('product',array('id' => $orders_item->prod_id));
				$data_this_variant = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));

				$data_json_orders[] = array(
					'orders_item_id' => $orders_item->id,
					'name_item'      => $data_this_product['name_item'],
					'variant_name'   => $data_this_variant['variant'],
					'subtotal'       => $orders_item->subtotal,
					'qty'            => $orders_item->qty
				);
			}
			$data_json['orders_item'] = $data_json_orders;
		} else {
			$data_json = array('status' => 'Failed');
		}
		echo json_encode($data_json);
	}

	function get_data_order_unpaid() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$data_order = $this->main_model->get_list_where('orders',array('customer_id' => $customer_id,'order_payment' => 'Unpaid', 'order_status !=' => 'Cancel'));
		if ($data_order->num_rows() > 0) {
			$data_json = array();
			foreach ($data_order->result() as $orders) {
				$data_unpaid = $this->main_model->get_list_where('confirmation', array('order_id' =>$orders->id));
				if ($data_unpaid->num_rows() < 1) {
					$data_json[] = array(
						'order_id' => $orders->id,
						'total'    => $orders->total
					);
				}
			}
			$return_json = array(
				'status' => 'Success',
				'list'   => $data_json
			);
		} else {
			$return_json = array('status' => 'Failed');
		}
		$bank_accounts = $this->db->get_where('bank_accounts', array('active' => 1))->result();
		$return_json['bank_accounts'] = $bank_accounts;
		$return_json['status_member'] = $this->checkMemberStatus($customer_id);
		echo json_encode($return_json);
	}

	function confirm_payment() {
		if ($_FILES && $_FILES['file']['size'] > 0) {
			$token        = $this->input->post('token');
			$customer_id  = $this->input->post('customer_id');
			$nama         = $this->input->post('nama');
			//$bank         = $this->input->post('bank');
			$jumlah       = $this->input->post('jumlah');
			//$rekening     = $this->input->post('rekening');
			$order_id     = $this->input->post('order_id');
			$bank_account = $this->input->post('bank_account');
		} else {
			$token       = $this->request->token;
			$customer_id = $this->request->customer_id;
			$nama        = $this->request->nama;
			//$bank        = $this->request->bank;
			$jumlah      = $this->request->jumlah;
			//$rekening    = $this->request->rekening;
			$order_id    = $this->request->order_id;
			$bank_account = property_exists($this->request, 'bank_account') ? $this->request->bank_account : '';
		}

		$this->check_token($token, $customer_id);

		$checkPayment = $this->db->get_where('confirmation', array('order_id' => $order_id))->result();
		if (count($checkPayment) > 0) {
			$data_json = array(
				'status' => 'Failed',
				'error'  => 'Pembayaran untuk Order ID ini telah dilakukan'
			);
			echo json_encode($data_json);
			exit;
		}

		$config['upload_path'] = './media/images/attachments/';
		$config['allowed_types'] = '*';
		$config['max_size']  = '0';
		$config['max_width']  = '0';
		$config['max_height']  = '0';

		$this->upload->initialize($config);

		$order = $this->main_model->get_detail('orders', array('id' => $order_id));

		$data_insert = array(
			'customer_id'         => $customer_id,
			'order_id'            => $order_id,
			'date'                => date('Y-m-d H:i:s'),
			'name'                => $nama,
			//'bank'                => $bank,
			'amount'              => $jumlah,
			//'bank_account_number' => $rekening,
			'order_status'        => $order['order_status'],
			'status'              => 'Pending'
		);
		if ($_FILES && $_FILES['file'] && $_FILES['file']['size'] > 0) {
		 	if ( ! $this->upload->do_upload('file')){
				$data_json = array(
					'status' => 'Failed',
					'error'  => $this->upload->display_errors('', ''),
				);
				echo json_encode($data_json);
				exit;
			} else {
				$data_file = $this->upload->data();
				$data_insert['attachment'] = $data_file['file_name'];
			}
		}
		if ($bank_account) {
			$data_insert['bank_account_id'] = $bank_account;
		}
		$this->db->insert('confirmation', $data_insert);
		$data_json = array('status' => 'Success');
		echo json_encode($data_json);
	}

	function get_list_message() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$page        = $this->request->page;
		$this->check_token($token, $customer_id);

		$perpage = 10;
		$offset = $perpage * ($page - 1);

		$message_total = $this->db->select('COUNT(*) AS total')
			->get_where('message', array('customer_id' => $customer_id))->row_array();

		$data_message = $this->main_model->get_list_where('message',array('customer_id' => $customer_id),array('perpage' => $perpage,'offset' => $offset),array('by' => 'id','sorting' => 'DESC'));

		if ($data_message->num_rows() > 0) {
			foreach ($data_message->result() as $msg) {
				$msg->image = $msg->image != '' ? base_url('media/images/messages/'.$msg->image) : null;
			}
			$return_json = array(
				'status'        => 'Success',
				'message'       => $data_message->result(),
				'message_total' => $message_total['total']
			);
		} else {
			$return_json = array('status' => 'Not_found');
		}
		echo json_encode($return_json);
	}

	function get_detail_message() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$message_id  = $this->request->message_id;
		$this->check_token($token,$customer_id);

		$data_message = $this->main_model->get_detail('message',array('id' => $message_id));

		// UPDATE
		$data_update = array('status' => 'read');
		$where = array('id' => $message_id);
		$this->db->update('message', $data_update,$where);

		$data_json = array(
			'subject' => $data_message['subject'],
			'content' => $data_message['content'],
			'image'   => $data_message['image'] != '' ? base_url('media/images/messages/'.$data_message['image']) : null
		);
		echo json_encode($data_json);
	}

	function get_content() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token);
		$data[21] = $this->detailContent('origin_city_id');
		$data[22] = $this->detailContent('jne_status');
		$data[23] = $this->detailContent('tiki_status');
		$data[24] = $this->detailContent('pos_status');
		$data[25] = $this->detailContent('wahana_status');
		$data[26] = $this->detailContent('origin_city_name');
		$data[35] = $this->detailContent('jnt_status');
		$data[36] = $this->detailContent('point_reward_status');
		$data[37] = $this->detailContent('point_to_nominal');
		$data[38] = $this->detailContent('sicepat_status');
		$data[39] = $this->detailContent('lion_status');
		$data[40] = $this->detailContent('non_tarif');
		$data[41] = $this->detailContent('expired_point');
		$data['point'] = $this->main_model->get_detail('customer', array('id' => $customer_id))['point'];
		$data['tanggal_sekarang'] = date('Y-m-d');
		$data['bank_accounts'] = $this->db->get_where('bank_accounts', array('active' => 1))->result();
		echo json_encode($data);
	}

	private function detailContent($name) {
		return $this->main_model->get_detail('content', array('name' => $name));
	}

	function get_info() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$data_kontak   = $this->main_model->get_detail('content',array('name' => 'kontak'));
		$data_rekening = $this->main_model->get_detail('content',array('name' => 'rekening'));
		$data_info     = $this->main_model->get_detail('content',array('name' => 'info'));
		$data_no_wa     = $this->main_model->get_detail('content',array('name' => 'no_wa'));

		$data_json = array(
			'kontak'   => $data_kontak['value'],
			'rekening' => $data_rekening['value'],
			'info'     => $data_info['value'],
			'no_wa'    => $data_no_wa['value']
		);
		echo json_encode($data_json);
	}

	function update_settings() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$nama         = $this->request->nama;
		$email        = $this->request->email;
		$phone        = $this->request->phone;
		$password     = $this->request->password;
		$alamat       = $this->request->alamat;
		$kodepos      = $this->request->kodepos;
		$pinbb        = $this->request->pinbb;
		$prov_id      = $this->request->provinsi;
		$kota_id      = $this->request->kota;
		$kecamatan_id = $this->request->kecamatan;

		$data_update = array(
			'name'         => $nama,
			'email'        => $email,
			'phone'        => $phone,
			'address'      => $alamat,
			'pin_bb'       => $pinbb,
			'postcode'     => $kodepos,
			'prov_id'      => $prov_id,
			'kota_id'      => $kota_id,
			'kecamatan_id' => $kecamatan_id,
		);

		if(($password != null) and ($password != '')) {
			// $encrypt_password = $this->encrypt->encode($password);
			$encrypt_password = password_hash($password,PASSWORD_DEFAULT);
			$data_update['password'] = $encrypt_password;
		}
		$where = array('id' => $customer_id);
		$this->db->update('customer',$data_update,$where);
		$data_json = array('status' => 'Success');
		echo json_encode($data_json);

	}

	function update_profile() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$nama     = $this->request->nama;
		$email    = $this->request->email;
		$phone    = $this->request->phone;
		$password = property_exists($this->request, 'password') ? $this->request->password : '';
		$notif = property_exists($this->request, 'notif') ? $this->request->notif : '';

		$data_update = array(
			'name'  => $nama,
			'email' => $email,
			'phone' => $phone,
			'notif' => $notif,
		);

		if ($password) {
			// $encrypt_password = $this->encrypt->encode($password);
			$encrypt_password = password_hash($password,PASSWORD_DEFAULT);
			$data_update['password'] = $encrypt_password;
		}
		$where = array('id' => $customer_id);
		$this->db->update('customer',$data_update,$where);
		$data_json = array('status' => 'Success');
		echo json_encode($data_json);
	}

	function get_update_apps()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$data_link_update = $this->main_model->get_detail('content',array('name' => 'link_android'));
		$data_json = array('link' => $data_link_update['value']);

		echo json_encode($data_json);

	}

	function registering_fcm($registration_id, $customer_id) {
		$registration = $this->main_model->get_list_where('t_fcm_customer', array('customer_id' => $customer_id, 'registration_id' => $registration_id));
		if ($registration->num_rows() == 0) {
			if($registration_id != "0"){
				if(is_numeric($customer_id) == 1) {
					$data_insert = array(
						'customer_id'     => $customer_id,
						'registration_id' => $registration_id
					);
				} else {
					$detail = $this->main_model->get_detail('customer',array('email' => $customer_id));
					$data_insert = array(
						'customer_id'     => $detail['id'],
						'registration_id' => $registration_id
					);
				}
				$this->db->insert('t_fcm_customer',$data_insert);
			}
		}
		$customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		$data['logout'] = $customer['token'] && $customer['token'] != $registration_id;
		echo json_encode($data);
	}

	function get_chat() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token,$customer_id);

		$data = $this->db->query("SELECT * FROM chatting WHERE customer_id='$customer_id' ORDER BY tanggal ASC");
		$data_update_stock = array('status' => 'Read');
		$where = array('customer_id' => $customer_id,'sender' => 'Admin');
		$this->db->update('chatting',$data_update_stock,$where);

		foreach ($data->result() as $row) {
			$data_json[] = array(
				'id'          => $row->id,
				'tanggal'     => $row->tanggal,
				'customer_id' => $row->customer_id,
				'pesan'       => $row->pesan,
				'image'       => $row->image != '' ? base_url('media/images/chats/'.$row->image) : null,
				'sender'      => $row->sender,
				'status'      => $row->status
			);
		}
		echo json_encode($data_json);
	}

	function kirim_balasan() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$balasan     = $this->request->balasan;
		$this->check_token($token,$customer_id);

		$data_insert = array(
			'tanggal'     => date('Y-m-d H:i:s'),
			'customer_id' => $customer_id,
			'pesan'       => $balasan,
			'sender'      => 'Customer',
			'status'      => 'Unread'
		);

		$x = $this->db->insert('chatting',$data_insert);
		if($x) {
			$data_json = array('status' => 'Success','message' => 'Pesan Berhasil Dikirim');
		} else {
			$data_json = array('status' => 'Failed','message' => 'Terjadi Kesalahan, Coba Lagi');
		}
		echo json_encode($data_json);
	}

	function reply_chat_image() {
		$token       = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$message     = $this->input->post('message');
		$this->check_token($token, $customer_id);

		$chat_path = base_url('media/images/chats/');

		if (!file_exists($chat_path)) {
			mkdir('./media/images/chats');
		}

		$config['upload_path'] = './media/images/chats/';
		$config['allowed_types'] = '*';
		$config['max_size']  = '512';
		$config['max_width']  = '0';
		$config['max_height']  = '0';

		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('file')) {
			$data_json = array(
				'status' => 'Failed',
				'error'  => $this->upload->display_errors('', ''),
			);
		} else{
			$data_file = $this->upload->data();
			$data_insert = array(
				'tanggal'     => date('Y-m-d H:i:s'),
				'customer_id' => $customer_id,
				'pesan'       => $message,
				'image'       => $data_file['file_name'],
				'sender'      => 'Customer',
				'status'      => 'Unread'
			);

			$this->db->insert('chatting', $data_insert);
			$data_json = array('status' => 'Success');
		}
		echo json_encode($data_json);
	}

	function get_list_chat_product() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);

		$query = $this->db->query("SELECT A.*, B.id AS prod_id, B.name_item, B.image FROM chat_product A LEFT JOIN product B ON A.prod_id = B.id WHERE A.id IN(SELECT max(id) FROM chat_product WHERE customer_id = '$customer_id' GROUP by prod_id)");

		$data_chat = $query->result();
		foreach ($data_chat as $chat) {
			$chat->product_name = $chat->name_item;
			$chat->img = base_url('media/images/thumb/'.$chat->image);
			$chat->create_at = date('d-m-Y', strtotime($chat->create_at));
		}

		echo json_encode($data_chat);
	}

	function get_product_chat() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$prod_id     = $this->request->prod_id;
		$this->check_token($token, $customer_id);

		$where = array(
			'prod_id' => $prod_id,
			'customer_id' => $customer_id,
		);
		$chat_product = $this->db->order_by('create_at', 'asc')->get_where('chat_product', $where)->result();
		$data_update_chat = array('read_chat' => 1);
		$where_chat = array(
			'customer_id' => $customer_id,
			'prod_id'     => $prod_id,
			'sender'      => 'Admin'
		);
		$this->db->update('chat_product', $data_update_chat, $where_chat);

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

		$data_json = array(
			'product'      => $data_product,
			'chat_product' => $data_chat
		);
		echo json_encode($data_json);
	}

	function reply_chat_product() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$prod_id     = $this->request->prod_id;
		$content     = $this->request->content;
		$this->check_token($token, $customer_id);

		$data_insert = array(
			'customer_id' => $customer_id,
			'prod_id'     => $prod_id,
			'content'     => $content,
			'sender'      => 'Customer',
		);

		$x = $this->db->insert('chat_product', $data_insert);
		if ($x) {
			$data_json = array('status' => 'Success','message' => 'Pesan Berhasil Dikirim');
		} else {
			$data_json = array('status' => 'Failed','message' => 'Terjadi Kesalahan, Coba Lagi');
		}
		echo json_encode($data_json);
	}

	function get_faq() {
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$faq = $this->main_model->get_detail('content', array('name' => 'faq'));
		$json = array(
			'id'    => $faq['id'],
			'name'  => $faq['name'],
			'value' => $faq['value']
		);
		echo json_encode($json);
	}

	function forget() {
		$token = $this->request->token;
		$email = $this->request->email;
		$this->check_token($token, null);

		$domain = $this->config->item('tokomobile_domain');
		$shop_name = $this->config->item('tokomobile_online_shop');

		$customer = $this->db->get_where('customer', array('email' => $email));
		if ($customer->num_rows() == 0) {
			$data = array(
				'status'  => 'Failed',
				'message' => 'Email tidak ditemukan'
			);
		} else {
			$code = sha1(md5(strtolower($email)));
			$url = base_url().'_api_/android_dev/reset_password/'.$code.'-'.time();
			$this->load->library('email');

			$this->emailConfig();

			$this->email->from('admin@'.$domain, 'Admin '.$shop_name);
			$this->email->to($email);

			$this->email->subject('Konfirmasi Reset Password');

			$message = '<!DOCTYPE html>
				<html>
				<head>
					<meta charset="utf-8">
					<title>Reset Password Confirmation</title>
				<body>
					<h4>Email From Admin '.$shop_name.'</h4>
					<p>Email : '. $customer->row_array()['email'] .'</p>

					<p>Message :</p>
					<br/>
					<p>Anda sudah melakukan permohonan untuk melakukan reset password,berikut data user acount anda</p>
					<p>ID : '. $customer->row_array()['id'] .'</p>
					<p>Nama : '. $customer->row_array()['name'] .'</p>
					<p>Untuk melanjutkan proses reset password admin anda silahkan kunjungi halaman berikut ini :</p>
					<a href="'.$url.'">Klik disini</a>
					<hr/>
				</body>
				</html>';
			$this->email->message($message);

			$this->email->send();

			$this->db->update('customer', array('req_reset' => 1), array('email' => $email));

			$data = array(
				'status'  => 'Success',
				'message' => 'Silahkan cek email anda dan link untuk merubah password telah dikirim ke email anda'
			);
		}

		echo json_encode($data);
	}

	function reset_password() {
		$token = $this->uri->segment(4);
		$token = explode('-', $token);
		$time = $token[1];
		$email = $token[0];
		$now = time();
		$diff = ($now - $time)/60;
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		if ($this->form_validation->run() == FALSE) {
			$this->db->where('sha1(md5(LOWER(email)))', $email);
			$customer = $this->db->get('customer');
			if ($customer->num_rows() > 0) {
				if ($diff > 15) {
					$data['message'] = 'Waktu session telah habis, silahkan coba reset password kembali';
				} else {
					if ($customer->row_array()['req_reset'] == 0 && !$this->session->flashdata('success')) {
						$data['message'] = 'Password telah diubah, silahkan coba reset password kembali';
					} else {
						$data['message'] = 'Success';
					}
				}
			} else {
				$data['message'] = 'URL Tidak Valid';
			}
			$this->session->set_flashdata('hide_failed', 'false');
			$data['req_reset'] = $customer->row_array()['req_reset'];
			$this->load->view('administrator/includes/reset_password', $data);
		} else {
			$password = $this->input->post('password');
			$confirm_password = $this->input->post('confirm_password');

			if ($password != $confirm_password) {
				$this->session->set_flashdata('failed', 'Password dan Konfirmasi Password tidak sesuai');
			} else {
				$data_update = array(
					// 'password'  => $this->encrypt->encode($password),
					'password'  => password_hash($password,PASSWORD_DEFAULT),
					'req_reset' => 0
				);
				$id['sha1(md5(LOWER(email)))'] = $email;
				$this->db->update('customer', $data_update, $id);
				$this->session->set_flashdata('success', 'Password berhasil diubah');
				$this->session->set_flashdata('hide_failed', 'true');
			}
			redirect('_api_/android_dev/reset_password/'.$this->uri->segment(4),'refresh');
		}
	}

	private function emailConfig() {
		$domain = $this->config->item('tokomobile_domain');

		$smtp_host = 'smtp.sendgrid.net';
		$smtp_port = '587';
		$smtp_user = 'apikey';
		$smtp_pass = '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';
		$email_from = 'admin@'.$domain;
		$config = array(
			'protocol' 	=> 'smtp',
			'smtp_host' => $smtp_host,
			'smtp_port' => $smtp_port,
			'smtp_user' => $smtp_user,
			'smtp_pass' => $smtp_pass,
			'charset'	=> 'iso-8859-1',
			'mailtype' 	=> 'html',
			'newline'	=> '\r\n',
			'wordwrap'	=> TRUE,
		);
		$this->email->initialize($config);
	}


	public function addresses() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);

		$addresses = $this->db->select('A.*, P.province, C.type, C.city_name, S.subdistrict_name')
			->join('provinces P', 'P.province_id = A.province_id', 'LEFT')
			->join('cities C', 'C.city_id = A.city_id', 'LEFT')
			->join('subdistricts S', 'S.subdistrict_id = A.subdistrict_id', 'LEFT')
			->get_where('addresses A', array('customer_id' => $customer_id))->result();
		echo json_encode($addresses);
	}

	public function provinces() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);

		$provinces = $this->db->get('provinces')->result();
		echo json_encode($provinces);
	}

	public function cities() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$province_id = property_exists($this->request, 'province_id') ? $this->request->province_id : '';
		$this->check_token($token, $customer_id);

		if ($province_id) {
			$this->db->where('province_id', $province_id);
		}
		$cities = $this->db->get('cities')->result();
		echo json_encode($cities);
	}

	public function subdistricts() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$city_id = $this->request->city_id;
		$this->check_token($token, $customer_id);

		$subdistricts = $this->db->get_where('subdistricts', array('city_id' => $city_id))->result();
		echo json_encode($subdistricts);
	}

	public function add_address() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);

		$data = array(
			'customer_id'     => $customer_id,
			'recipient_name'  => $this->request->recipient_name,
			'recipient_phone' => $this->request->recipient_phone,
			'address'         => $this->request->address,
			'province_id'     => $this->request->province_id,
			'city_id'         => $this->request->city_id,
			'subdistrict_id'  => $this->request->subdistrict_id,
			'postal_code'     => $this->request->postal_code,
			'sender'          => $this->request->sender,
		);
		$this->db->insert('addresses', $data);
		$res['message'] = 'Alamat berhasi disimpan';
		echo json_encode($res);
	}

	public function address_detail() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);
		$id  = $this->request->id;

		$address = $this->main_model->get_detail('addresses', array('id' => $id));
		$provinces = $this->db->get('provinces')->result();
		$cities = $this->db->get_where('cities', array('province_id' => $address['province_id']))->result();
		$subdistricts = $this->db->get_where('subdistricts', array('city_id' => $address['city_id']))->result();

		$data = array(
			'address'      => $address,
			'provinces'    => $provinces,
			'cities'       => $cities,
			'subdistricts' => $subdistricts
		);
		echo json_encode($data);
	}

	public function update_address() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$this->check_token($token, $customer_id);
		$id  = $this->request->id;

		$data = array(
			'recipient_name'  => $this->request->recipient_name,
			'recipient_phone' => $this->request->recipient_phone,
			'address'         => $this->request->address,
			'province_id'     => $this->request->province_id,
			'city_id'         => $this->request->city_id,
			'subdistrict_id'  => $this->request->subdistrict_id,
			'postal_code'     => $this->request->postal_code,
			'sender'          => $this->request->sender,
		);
		$this->db->update('addresses', $data, array('id' => $id));
		$res['message'] = 'Alamat berhasi disimpan';
		echo json_encode($res);
	}

	public function delete_address() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$address_id = $this->request->address_id;
		$this->check_token($token, $customer_id);

		$this->db->delete('addresses', array('id' => $address_id));
		echo json_encode(array('message' => 'Success'));
	}

	public function copy_address() {
		$token = $this->request->token;
		$customer_id = $this->request->customer_id;
		$address_id = $this->request->address_id;
		$this->check_token($token, $customer_id);

		$address = $this->main_model->get_detail('addresses', array('id' => $address_id));
		unset($address['id']);

		$data = array(
			'customer_id'     => $customer_id,
			'recipient_name'  => $this->request->recipient_name,
			'recipient_phone' => $this->request->recipient_phone,
			'address'         => $this->request->address,
			'province_id'     => $this->request->province_id,
			'city_id'         => $this->request->city_id,
			'subdistrict_id'  => $this->request->subdistrict_id,
			'postal_code'     => $this->request->postal_code,
			'sender'          => $this->request->sender,
		);
		$this->db->insert('addresses', $address);
		echo json_encode(array('message' => 'Success'));
	}

	function cara_order() {
		$token       = $this->request->token;
		$customer_id = property_exists($this->request, 'customer_id') ? $this->request->customer_id : '';
		$this->check_token($token,$customer_id);

		$cara_order = $this->db->get('cara_order')->result();
		echo json_encode($cara_order);
	}

	public function get_order_dropship() {
		$token           = $this->request->token;
		$customer_id     = $this->request->customer_id;
		$page            = $this->request->page;
		$shipping_status = $this->request->shipping_status;
		$this->check_token($token,$customer_id);

		$perpage = 20;
		$offset = $perpage * ($page - 1);

		$this->db->select('COUNT(*) AS total');
			if ($shipping_status == 'Unpaid') {
				$this->db->where('order_payment', 'Unpaid');
			} else if ($shipping_status == 'Dipacking') {
				$this->db->where('resi', '')->where('order_payment', 'Paid');
			} else if ($shipping_status == 'Dikirim') {
				$this->db->where('resi !=', '');
			}
		$order_total = $this->db->where('order_status', 'Dropship')
			->where('customer_id', $customer_id)
			->where('order_status !=', 'Cancel')
			->get('orders')->row_array();

		$this->db->select('id, shipping_to, order_payment, order_status, resi, phone_recipient, address_recipient, postal_code, shipping_from, notes, prov_id, kota_id, kecamatan_id');
			if ($shipping_status == 'Unpaid') {
				$this->db->where('order_payment', 'Unpaid');
			} else if ($shipping_status == 'Dipacking') {
				$this->db->where('resi', '')->where('order_payment', 'Paid');
			} else if ($shipping_status == 'Dikirim') {
				$this->db->where('resi !=', '');
			}
		$orders = $this->db->where('order_status', 'Dropship')
			->where('customer_id', $customer_id)
			->where('order_status !=', 'Cancel')
			->order_by('id', 'DESC')
			->get('orders', $perpage, $offset)->result();
		foreach ($orders as $order) {

			if ($order->order_payment == 'Unpaid') {
				$confirmation = $this->main_model->get_detail('confirmation', array('order_id' => $order->id, 'status !=' => 'Reject'));
				if (!empty($confirmation)) {
					if ($confirmation['status'] == 'Pending') {
						$order->status = 'Menunggu Dikonfirmasi';
					}
				} else {
					$order->status = 'Belum Melakukan Konfirmasi Pembayaran';
				}
			} else if ($order->order_payment == 'Paid' && !$order->resi) {
				$order->status = 'Dipacking';
			} else if ($order->resi) {
				$order->status = 'Dikirim';
			}

			if ($order->prov_id != 0) {
				$province = $this->main_model->get_detail('provinces', array('province_id' => $order->prov_id));
				$order->provinsi = $province['province'];
				$city = $this->main_model->get_detail('cities', array('city_id' => $order->kota_id));
				$order->kota = $city['type'] . ' ' . $city['city_name'];
				$subdistrict = $this->main_model->get_detail('subdistricts', array('subdistrict_id' => $order->kecamatan_id));
				$order->kecamatan = $subdistrict['subdistrict_name'];
			}

			$data_order_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order->id, 'order_status !=' => 'Cancel'));
			foreach ($data_order_item->result() as $orders_item) {
				$data_this_product = $this->main_model->get_detail('product',array('id' => $orders_item->prod_id));
				$data_this_variant = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));

				$data_json_orders[] = array(
					'name_item'      => $data_this_product['name_item'],
					'variant_name'   => $data_this_variant['variant'],
					'qty'            => $orders_item->qty
				);
			}
			$order->orders_item = $data_json_orders;
		}
		$data = array(
			'total'  => $order_total['total'],
			'orders' => $orders
		);
		echo json_encode($data);
	}

	function get_resi() {
		$token       = $this->request->token;
		$customer_id = $this->request->customer_id;
		$page        = $this->request->page;
		$this->check_token($token, $customer_id);

		$perpage = 10;
		$offset = $perpage * ($page - 1);

		$where = array(
			'customer_id'  => $customer_id,
			'order_status' => 'Dropship',
			'order_status  !=' => 'Cancel'
		);

		$resi_total = $this->db->select('COUNT(*) AS total')
			->get_where('orders', $where)->row_array();

		$this->db->select('id, total, order_datetime, resi');
		$data_resi = $this->main_model->get_list_where('orders', $where, array('perpage' => $perpage,'offset' => $offset),array('by' => 'id','sorting' => 'DESC'))->result();

		foreach ($data_resi as $resi) {
			$resi->order_date = date('d-m-Y', strtotime($resi->order_datetime));
			$resi->order_time = date('h.i a', strtotime($resi->order_datetime));
			$resi->total = 'Rp. '.number_format($resi->total, 0, ',', '.');
		}

		if ($resi_total['total'] > 0) {
			$return_json = array(
				'status'     => 'Success',
				'resi'       => $data_resi,
				'resi_total' => $resi_total['total']
			);
		} else {
			$return_json = array('status' => 'Not_found');
		}
		echo json_encode($return_json);
	}

	public function check_mobile_token() {
		$token        = $this->request->token;
		$customer_id  = $this->request->customer_id;
		$mobile_token = $this->request->mobile_token;
		$this->check_token($token, $customer_id);
		$customer = $this->main_model->get_detail('customer', array('customer_id' => $customer_id));
		if ($mobile_token != $customer['token']) {
			$data = array(
				'Status'  => 'Failed',
				'message' => 'Akun ini telah login di device lain. Akun ini akan dilogout.'
			);
		} else {
			$data['message'] = 'Success';
		}
		echo json_encode($data);
	}

	public function getPajak()
	{
		$token         = $this->request->token;
		$customer_id   = $this->request->customer_id;
		$order_item_id = $this->request->order_item_id;

		$this->check_token($token, $customer_id);

		$pajak_status = $this->main_model->get_detail('content', array('name' => 'pajak_status'));

		$response = array('pajak_status' => 'Disabled');

		if ($pajak_status['value'] == 'Disabled') {
			echo json_encode($response);
			exit;
		}


		$total_bea_masuk = 0;
		$total_ppn = 0;
		$total_pph = 0;

		$pajak = array();

		foreach ($order_item_id as $id) {
			$order_item = $this->main_model->get_detail('orders_item', array('id' => $id));
			$product = $this->main_model->get_detail('product', array('id' => $order_item['prod_id']));
			$bea_masuk = $product['bea_masuk'];
			$ppn = $product['ppn'];
			$pph = $product['pph'];

			$harga = $product['harga_pajak'] > 0 ? $product['harga_pajak'] : $order_item['price'];

			$pajak_bea_masuk = ($harga * $bea_masuk) / 100;
			$pajak_ppn = ((($harga + $pajak_bea_masuk) * $ppn) / 100) * $order_item['qty'];
			$pajak_pph = ((($harga + $pajak_bea_masuk) * $pph) / 100) * $order_item['qty'];

			$pajak_bea_masuk = $pajak_bea_masuk * $order_item['qty'];

			$pajak[] = array(
				'bea_masuk' => $pajak_bea_masuk,
				'ppn'       => $pajak_ppn,
				'pph'       => $pajak_pph,
			);

			$total_bea_masuk += $pajak_bea_masuk;
			$total_ppn += $pajak_ppn;
			$total_pph += $pajak_pph;
		}

		echo json_encode(array(
			'pajak_status'    => 'Enabled',
			'pajak'           => $pajak,
			'total_bea_masuk' => $total_bea_masuk,
			'total_ppn'       => $total_ppn,
			'total_pph'       => $total_pph,
		));
	}
	
public function get_pesanan_berhasil()
	{
		$token         = $this->request->token;
		$customer_id   = $this->request->customer_id;
		$order_item_id = $this->request->order_item_id;

		$this->check_token($token, $customer_id);

		$pajak_status = $this->main_model->get_detail('content', array('name' => 'pajak_status'));

		$response = array('pajak_status' => 'Disabled');

		if ($pajak_status['value'] == 'Disabled') {
			echo json_encode($response);
			exit;
		}

		$data_orders = $this->db->query("SELECT * FROM `orders` ORDER BY id DESC LIMIT 1")->row_array();


		$bank_accounts = $this->db->get_where('bank_accounts', array('active' => 1))->result();
		$return_json['bank_accounts'] = $bank_accounts;

		echo json_encode(array(
			'pajak_status'    => 'Enabled',
			'order_id'    	  => $data_orders['id'],
			'total_bayar'     => $data_orders['total'],
			'shipping_fee'    => $data_orders['shipping_fee'],
			'diskon'    	  => $data_orders['diskon'],
			'duedate'         => $data_orders['due_datetime'],
			'bank_accounts'   => $bank_accounts,
		));
	}
}