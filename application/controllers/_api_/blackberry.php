<?php

/* Class Main */

class Blackberry extends CI_Controller {

	Public $status_new_member = "Moderate"; // moderate, active
	Public $jne_service = "reg";

	Public function __construct()
	{
		parent::__construct();
		
		date_default_timezone_set('Asia/Jakarta');
		
		$this->load->model('main_model');

		if(!$this->session->userdata('lang_active'))
		{
			$this->session->set_userdata('lang_active','en');
		}	
		
		
		header('Content-type: application/json');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: *');
		
	}
	
	function check_token($token = null, $customer_id = null)
	{
		$status_aplication = $this->main_model->get_detail('content',array('name' => 'status_aplication'));

		if($token != $this->config->item('tokomobile_token'))
		{
			$data_json = array("status" => "Invalid Token");
			exit (json_encode($data_json));
		}

		if($status_aplication['value'] == 'OFF')
		{
			$message_off = $this->main_model->get_detail('content',array('name' => 'message_off'));
			$data_json = array("status" => "OFF","message" => $message_off['value']);
			exit (json_encode($data_json));
		}

		if($customer_id != null)
		{
			$data_customer = $this->main_model->get_list_where('customer',array('id' => $customer_id));
			if($data_customer->num_rows() != 1)
			{
				$data_json = array("status" => "Member Not Found");
				exit (json_encode($data_json));
			}
			else
			{
				$data_customer_detail = $data_customer->row_array();
				if($data_customer_detail['status'] != 'Active')
				{
					$data_json = array("status" => "Member Not Active");
					exit (json_encode($data_json));
				}
			}	
		}
	}
	
	function login_sample()
	{
		$this->load->view('frontend/login_sample');
	}	
	
	function login()
	{
		$token = $this->input->post('token');
		$this->check_token($token);
		
		$customer_id = $this->input->post('customer_id');
		$password = $this->input->post('password');

		if(is_numeric($customer_id) == 1)
		{
			$data_check = $this->main_model->get_list_where('customer',array('id' => $customer_id));
		}
		else
		{
			$data_check = $this->main_model->get_list_where('customer',array('email' => $customer_id));
		}
		
		
		if($data_check->num_rows() > 0)
		{

			if(is_numeric($customer_id) == 1)
			{
				$data_member = $this->main_model->get_detail('customer',array('id' => $customer_id));
			}
			else
			{
				$data_member = $this->main_model->get_detail('customer',array('email' => $customer_id));
			}
			
			$member_password = $this->encrypt->decode($data_member['password']);
			
			if ($password == $member_password)
			{
				if($data_member['status'] == 'Active')
				{
					$data_json = array(
								'status' => 'Success',
								'customer_id' => $data_member['id'],
								'customer_email' => $data_member['email'],
								'customer_name' => $data_member['name'],
								'customer_phone' => $data_member['phone']
								);
				}
				else
				{
					$data_json = array(
								'status' => 'Failed',
								'error' => 'User belum aktif, hubungi Admin untuk aktivasi'
								);
				}			
			}
			else
			{
				$data_json = array(
									'status' => 'Failed',
									'error' => 'Password anda salah'
									);
			}
		}
		else
		{
			$data_json = array(
								'status' => 'Failed',
								'error' => 'User not found'
								);
		}

		echo json_encode($data_json);
	}
	
	
	function register()
	{
		$token = $this->input->post('token');
		$this->check_token($token);
	
		$nama = $this->input->post('nama');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$alamat = $this->input->post('alamat');
		$provinsi = $this->input->post('provinsi');
		$kota = $this->input->post('kota');
		$kodepos = $this->input->post('kodepos');
		$phone = $this->input->post('phone');
		$pinbb = $this->input->post('pinbb');
		
		$data_check = $this->main_model->get_list_where('customer',array('email' => $email));
		
		if($data_check->num_rows() < 1)
		{
			$data_insert = array(
							'name' => $nama,
							'email' => $email,
							'password' => $this->encrypt->encode($password),
							'address' => $alamat,
							'prov_id' => $provinsi,
							'kota_id' => $kota,
							'postcode' => $kodepos,
							'phone' => $phone,
							'pin_bb' => $pinbb,
							'status' => $this->status_new_member
							);
			
			$this->db->insert('customer',$data_insert);	
			
			$customer_id = mysql_insert_id();
			
			$data_json = array(
							'status' => 'Success',
							'customer_id' => $customer_id,
							'password' => $password
							);				
		}
		else
		{
			$data_json = array(
							'status' => 'Failed',
							'error' => 'Email is exists'
							);
		}

		echo json_encode($data_json);
	}
	
	function get_customer_info()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		
		$data_customer_provinsi = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $data_customer['prov_id']));
		$data_customer_kota = $this->main_model->get_detail('jne_kota',array('kota_id' => $data_customer['kota_id']));
		
		$data_json = $data_customer;
		$data_json['prov'] = $data_customer_provinsi['provinsi_nama'];
		$data_json['kota'] = $data_customer_kota['kota_nama'];
		
		echo '{ data:['; 
		echo json_encode($data_json);
		echo ']}';
		
	}
	
	function get_customer_info_2()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		
		$data_customer_provinsi = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $data_customer['prov_id']));
		$data_customer_kota = $this->main_model->get_detail('jne_kota',array('kota_id' => $data_customer['kota_id']));
		
		$data_json = $data_customer;
		$data_json['prov'] = $data_customer_provinsi['provinsi_nama'];
		$data_json['kota'] = $data_customer_kota['kota_nama'];
		$data_json['password_decrypted'] = $this->encrypt->decode($data_customer['password']);
		
		$data_json['status'] = 'Success';
		
		echo '{ data:['; 
		echo json_encode($data_json);
		echo ']}';
	}
	
	// CATEGORY //
	function get_list_product_category()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$tipe = $this->input->post('tipe');
		$page = $this->input->post('page');
		
		$perpage = 10;
		$offset = $perpage * ($page - 1); 
		
		$data_category_total = $this->main_model->get_list_where('product_category',array('tipe' => $tipe,'status_category' =>'publish'));
		$data_category = $this->main_model->get_list_where('product_category',array('tipe' => $tipe,'status_category' =>'publish'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'name','sorting' => 'ASC'));
		
		$a = $data_category_total->num_rows();
		$i = 1;
		
		if($data_category_total->num_rows() > 0)
		{
			echo '{ data:[';
			
			foreach($data_category->result() as $categories):
				
				$data_total_prod = $this->main_model->get_list_where('product',array('category_id' => $categories->id,'status' => 'Publish'));
					$data_json = array('id' => $categories->id,'name' => $categories->name);
					if($i == 1) {
					echo json_encode($data_json);
					} else {
					echo ','.json_encode($data_json);
					}
					$i++;
				
			endforeach;
			
			echo ']}';	
			
		}
		else
		{
			$return_json['status'] = 'Error'; 
			echo '{ data:[';
			echo ']}';
		}
	}
	
	
	// LIST PRODUCT //
	function get_list_product()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$page = $this->input->post('page');
		$category = $this->input->post('category');
		
		$data_this_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		
		$perpage = 20;
		$offset = $perpage * ($page - 1); 
		
		if(($category != null) or ($category != 'all'))
		{
			$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish','category_id' => $category));
		}
		else{
			$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish'));
		}
		
		$total_page = ceil($data_product_total->num_rows() / $perpage); 
		
		if(($category != null) or ($category != 'all'))
		{
		
			$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish','category_id' => $category),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC'));
		}
		else
		{
			$data_product = $this->main_model->get_list_where('product',array('status' => 'Publish'),array('perpage' => $perpage,'offset' => $offset),array('by' => 'datetime','sorting' => 'DESC')); 
		}
		
		
		$a = $data_product->num_rows();
		$i = 1;

		// status stock "tampilkan(1), sembunyikan(2), hilangkan(3)"
		$status_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		
		if($a > 0)
		{
			
		echo '{ data:[';
		
		foreach ($data_product->result() as $products):
		
			$products->name_item = str_replace('-','_',$products->name_item);

			$total_stock = 0;
			$data_stock = $this->main_model->get_list_where('product_variant',array('prod_id' => $products->id, 'available !=' => 'Delete'));
			foreach ($data_stock->result() as $stock):
				$total_stock = $total_stock + $stock->stock;
			endforeach;
		
			if($data_this_customer['jenis_customer'] == 'Lokal')
			{
				$price = $products->price;
				$price_old = $products->price_old;
			}
			else{
				$price = $products->price_luar;
				$price_old = $products->price_old_luar;
			}
		
			$data_json = array(
							'product_id' => $products->id,
							'name_item' => $products->name_item,
							'harga' => $price,
							'harga_lama' => $price_old,
							'berat' => $products->weight,
							'keterangan' => $products->description,
							'foto' => base_url().'media/images/'.$products->image,
							'img_thumbnail' => base_url().'media/images/thumb/'.$products->image,
							'img_medium' => base_url().'media/images/medium/'.$products->image,
							'img_large' => base_url().'media/images/large/'.$products->image,
							'min_order' => $products->min_order,
							'view_stock' => $status_stock ['value'],
							'total_stock' => $total_stock
							);
		
			$data_variant = $this->main_model->get_list_where('product_variant',array('prod_id' => $products->id, 'available !=' => 'Delete'));
			
			if($data_variant->num_rows() > 0) {
				foreach($data_variant->result() as $variants): 
				
					if ($variants->stock == 0) {
						$data_variant_product[] = array('id_variant' => $variants->id, 'variant_name' => $variants->variant,'stock' => 'HABIS','available' => $variants->available);
					}else{
						$data_variant_product[] = array('id_variant' => $variants->id, 'variant_name' => $variants->variant,'stock' => $variants->stock,'available' => $variants->available);
					}
					
				endforeach;
				$data_json['variant'] = $data_variant_product;
			}
			else
			{
				$data_json['variant'][] = array('id_variant' => 0, 'variant' => null,'stock' => 0);
			}	
			
			$data_variant_product = null;	
			
			if($i == $a) {
			echo json_encode($data_json);
			} else {
			echo json_encode($data_json).',';
			}
			
			$i++;
			
		endforeach;
		
		echo ']}';
		
		}
		else
		{
			$return_json['status'] = 'Error'; 
			echo '{ data:[';
			echo ']}';
		}
	}
	
	function get_search_product()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$data_this_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
	
		$category = $this->input->post('category');
		$page = $this->input->post('page');
		$q = $this->input->post('q');
		
		if ((!$page) or ($page == null))
		{
			$page = 1;
		}
		
		$perpage = 20;
		$offset = $perpage * ($page - 1); 
		
		//TOTAL QUERY
		
		$this->db->where('category_id',$category);
		$this->db->where('status','Publish');
		$this->db->like('name_item',$q);
		$data_product_total = $this->db->get('product');
		
		$total_page = ceil($data_product_total->num_rows() / $perpage); 
		
		//QUERY
		
		$this->db->where('category_id',$category);
		$this->db->where('status','Publish');
		$this->db->like('name_item',$q);
		$this->db->limit($perpage,$offset);
		$this->db->order_by('datetime','DESC');
		$data_product = $this->db->get('product');
		
		echo '{ data:[';
		
		$a = $data_product->num_rows();
		$i = 1;

		// status stock "tampilkan(1), sembunyikan(2), hilangkan(3)"
		$status_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		
		foreach ($data_product->result() as $products):
		
			$products->name_item = str_replace('-','_',$products->name_item);

			$total_stock = 0;
			$data_stock = $this->main_model->get_list_where('product_variant',array('prod_id' => $products->id, 'available !=' => 'Delete'));
			foreach ($data_stock->result() as $stock):
				$total_stock = $total_stock + $stock->stock;
			endforeach;
		
			if($data_this_customer['jenis_customer'] == 'Lokal')
			{
				$price = $products->price;
				$price_old = $products->price_old;
			}
			else{
				$price = $products->price_luar;
				$price_old = $products->price_old_luar;
			}
		
			$data_json = array(
							'product_id' => $products->id,
							'name_item' => $products->name_item,
							'harga' => $price,
							'harga_lama' => $price_old,
							'berat' => $products->weight,
							'keterangan' => $products->description,
							'foto' => base_url().'media/images/'.$products->image,
							'img_thumbnail' => base_url().'media/images/thumb/'.$products->image,
							'img_medium' => base_url().'media/images/medium/'.$products->image,
							'img_large' => base_url().'media/images/large/'.$products->image,
							'min_order' => $products->min_order,
							'view_stock' => $status_stock ['value'],
							'total_stock' => $total_stock
							);
		
			$data_variant = $this->main_model->get_list_where('product_variant',array('prod_id' => $products->id,'stock >' => 0, 'available !=' => 'Delete'));
			
			if($data_variant->num_rows() > 0) {
				foreach($data_variant->result() as $variants): 
					$data_variant_product[] = array('id_variant' => $variants->id, 'variant_name' => $variants->variant,'stock' => $variants->stock,'available' => $variants->available);
				endforeach;
				
				$data_json['variant'] = $data_variant_product;
			}
			else
			{
				$data_json['variant'][] = array('id_variant' => 0, 'variant' => null,'stock' => 0);
			}	
			
			$data_variant_product = null;	
			
			if($i == $a) {
			echo json_encode($data_json);
			} else {
			echo json_encode($data_json).',';
			}
			
			$i++;
			
		endforeach;
		
		echo ']}';

	}
	
	function get_total_pages()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$page = $this->input->post('page');
		$category = $this->input->post('category');
		
		$perpage = 20;
		$offset = $perpage * ($page - 1); 
		
		if(($category != null) or ($category != 'all'))
		{
			$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish','category_id' => $category));
		}
		else{
			$data_product_total = $this->main_model->get_list_where('product',array('status' => 'Publish'));
		}
		
		$data_json['total_page'] = ceil($data_product_total->num_rows() / $perpage);
		$data_json['current_page'] = $page;
		
		echo '{ data:[';
		echo json_encode($data_json);
		echo "]}";
	}
	
	function get_check_stock_status()
	{
		// status stock "tampilkan(1), sembunyikan(2), hilangkan(3)"
		$status_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));

		$data_json = array('status' => $status_stock ['value']);

		echo '{ data:[';
		echo json_encode($data_json);
		echo "]}";
	}
	
	function get_detail_product()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$product_id = $this->input->post('prod_id');
		
		$products = $this->main_model->get_detail('product',array('id' => $product_id));
		
		$data_json =  array(
				'product_id' => $products['id'],
				'name_item' => $products['name_item'],
				'harga' => $products['price'],
				'harga_lama' => $products['price_old'],
				'berat' => $products['berat'],
				'keterangan' => $products['keterangan'],
				'foto' => base_url().'media/images/'.$products['foto'],
				'img_thumbnail' => base_url().'media/images/thumb/'.$products['foto'],
				'img_medium' => base_url().'media/images/medium/'.$products['foto'],
				'img_large' => base_url().'media/images/large/'.$products['foto'],
				'min_order' => $products['min_order'],
				'view_stock' => $view_stock
			);
						
			$data_variant = $this->main_model->get_list_where('rel_variant_prod',array('prod_id' => $products['id'], 'available !=' => 'Delete'));

			if($data_variant->num_rows() > 0) {
				foreach($data_variant->result() as $variants): 
				
					if ($status_stock ['value'] != 3) {
						if ($variants->stock == 0) {
							$data_variant_product[] = array('id_variant' => $variants->id, 'variant_name' => $variants->variant,'stock' => 'HABIS','available' => $variants->available);
						}else{
							$data_variant_product[] = array('id_variant' => $variants->id, 'variant_name' => $variants->variant,'stock' => $variants->stock,'available' => $variants->available);
						}
					}else{
						$data_variant_product[] = array('id_variant' => $variants->id, 'variant_name' => $variants->available);
					}
				
				endforeach;
				
				$data_json['variant'] = $data_variant_product;
			}
			else
			{
				$data_json['variant'][] = array('id_variant' => 0, 'variant' => null,'stock' => 0);
			}	
			
			$data_variant_product = null;	

		echo json_encode($data_json);		
	}
	
	function process_order_item()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$prod_id = $this->input->post('prod_id');
		$variant_id = $this->input->post('variant_id');
		$qty = $this->input->post('qty');
		$note = $this->input->post('notes');
		$price = $this->input->post('price');
		
		$data_this_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));

		if(($variant_id != null) and ($variant_id != 0))	
		
		{
			$data_product = $this->main_model->get_detail('product',array('id' => $prod_id));
			$data_product_category = $this->main_model->get_detail('product_category',array('id' => $data_product['category_id']));
			$data_product_variant = $this->main_model->get_detail('product_variant',array('id' => $variant_id));
			$data_due_date = $this->main_model->get_detail('content',array('name' => 'due_date_setting'));
			$status_due_date = $this->main_model->get_detail('content',array('name' => 'status_due_date'));

			if ($status_due_date['value'] == "ON") {
				$days = $data_due_date['value'];
				$today = date('Y-m-d H:i:s');

		 		$date1 = str_replace('-', '/', $today);

		 		//$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . "+ ".$days." days"));
		 		$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . '+'.$data_due_date['value']));
			}
			else
			{
				
				$due_datetime = "0000-00-00 00:00:00";
			}
			// status stock "tampilkan(1), sembunyikan(2), hilangkan(3)"
			$status_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
			
			if ($status_stock['value'] != 3) {
				if($data_product_variant['stock'] > 0)
				{
					if($data_product['status'] == 'Publish')
					{
						
						if ($qty >= $data_product['min_order']) {
							if ($qty <= $data_product_variant['stock']) {
								$subtotal = $price * $qty;
					
								$data_insert = array (
													'customer_id' => $customer_id,
													'prod_id' => $prod_id,
													'order_datetime' => date('Y-m-d H:i:s'),
													'due_datetime' => $due_datetime,
													'variant_id' => $variant_id,
													'qty' => $qty,
													'price' => $price,
													'subtotal' => $subtotal,
													'order_status' => 'Keep',
													'order_payment' => 'Unpaid',
													'tipe' => $data_product_category['tipe'],
													'ref' => 'Blackberry',
													'notes' => $note,
													'jenis_customer' => $data_this_customer['jenis_customer']
													);
								
								$this->db->insert('orders_item',$data_insert);
								
								// Kurangi stok
								$update_stock = $data_product_variant['stock'] - $qty;
								
								$data_update_stock = array('stock' => $update_stock);
								$where = array('id' => $variant_id);
								
								$this->db->update('product_variant',$data_update_stock,$where);
								
								$data_json = array('status' => 'Success');
							}else{

								$data_json = array('status' => 'Failed','message' => 'Maaf Produk yang anda pesan melebihi stock yang tersedia');
							}
						}else{

							$data_json = array('status' => 'Failed','message' => 'Maaf Minimum order produk '.$data_product['min_order']);
						}
					}
					else
					{
						$data_json = array('status' => 'Failed','message' => 'Maaf Produk yang anda pesan saat ini sedang tidak tersedia');
					}	
				}
				else
				{
					$data_json = array('status' => 'Failed', 'message' => 'Maaf Produk Habis');
				}
			}else{

				if ($data_product['status'] == 'Publish') {

					if(($qty >= $data_product['min_order']) )
					{
						$subtotal = $price * $qty;
					
						$data_insert = array (
											'customer_id' => $customer_id,
											'prod_id' => $prod_id,
											'order_datetime' => date('Y-m-d H:i:s'),
											'due_datetime' => $due_datetime,
											'variant_id' => $variant_id,
											'qty' => $qty,
											'price' => $price,
											'subtotal' => $subtotal,
											'order_status' => 'Keep',
											'order_payment' => 'Unpaid',
											'tipe' => $data_product_category['tipe'],
											'ref' => 'Blackberry',
											'notes' => $note,
											'jenis_customer' => $data_this_customer['jenis_customer']
											);
						
						$this->db->insert('orders_item',$data_insert);
						
						$data_json = array('status' => 'Success');
					}
					else
					{
						$data_json = array('status' => 'Failed','message' => 'Maaf Minimum order produk '.$data_product['min_order']);
					}

				}else{
					$data_json = array('status' => 'Failed','message' => 'Maaf Produk yang anda pesan saat ini sedang tidak tersedia');
				}	
			}
			
		}
		else
		{
			$data_json = array('Order Gagal');
		}	
		
		echo json_encode($data_json);
		
	}
	
	function list_order()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);


		// $tipe = $this->input->post('tipe');
		
		$data_order_item = $this->main_model->get_list_where('orders_item',array('order_status' => 'Keep','order_payment'=>'Unpaid','customer_id' => $customer_id));
		
		echo '{ data:[';
		
		$a = $data_order_item->num_rows();
		$i = 1;
		
			foreach($data_order_item->result() as $orders):
			
				$data_product = $this->main_model->get_detail('product',array('id' => $orders->prod_id));
				
				if($orders->variant_id == 0)
				{
					$data_variant['variant'] = 'All Var';
				}
				else
				{
					$data_variant = $this->main_model->get_detail('product_variant',array('id' => $orders->variant_id));
				}	
				
				$weight =  $data_product['weight'] * $orders->qty;
				
				$data_product['name_item'] = str_replace('-','_',$data_product['name_item']);
				
				$data_json = array(
								'order_item_id' => $orders->id,
								'prod_id' => $orders->prod_id,
								'prod_name' => $data_product['name_item'],
								'order_datetime' => $orders->order_datetime,
								'due_datetime' => $orders->due_datetime,
								'variant' => $data_variant['variant'],
								'qty' => $orders->qty,
								'price' => $orders->price,
								'weight' => $weight,
								'subtotal' => $orders->subtotal
								);
				if($i == $a) {
				echo json_encode($data_json);
				} else {
				echo json_encode($data_json).',';
				}
				
				$i++;				
				
			endforeach;	
		
		echo ']}';
	}
	
	function get_list_dropship()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$dropship_type = $this->input->post('dropship_type');
		$order_item_id = $this->input->post('order_item_id');
		
		if($dropship_type == 'alamat_sendiri')
		{
			$data_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		}
		$result = json_decode($order_item_id);
		foreach($result as $key => $value) {
		   
				$data_update_order_item = array('order_id' => $order_id, 'order_status' => 'Dropship');
				$where = array('id' => $value->order_item_id);
				
				$this->db->update('orders_item',$data_update_order_item,$where);
		    
		}
	}
	
	function process_dropship()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$from = $this->input->post('from');
		$to = $this->input->post('to');
		$prov_id = $this->input->post('prov_id');
		$kota_id = $this->input->post('kota_id');
		$ongkir = $this->input->post('ongkir');
		$address = $this->input->post('address_recipient');
		$phone = $this->input->post('phone_recipient');
		$postcode = $this->input->post('postal_code');
		//$total = $this->input->post('total');
		
		$tarif_id = $this->input->post('tarif_id');
		$tarif_tipe = $this->input->post('tarif_tipe');
		
		$weight = $this->input->post('weight');
		
		//$total_shop = $total - $ongkir;
		
		$order_item_id = $this->input->post('order_item_id');
		
		$data_this_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		
		$data_due_date = $this->main_model->get_detail('content',array('name' => 'due_date_setting'));
		$status_due_date = $this->main_model->get_detail('content',array('name' => 'status_due_date'));
		if ($status_due_date['value'] == "ON") {
				$days = $data_due_date['value'];
				$today = date('Y-m-d H:i:s');

		 		$date1 = str_replace('-', '/', $today);

		 		//$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . "+ ".$days." days"));
		 		$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . '+'.$data_due_date['value']));
			}
			else
			{
				
				$due_datetime = "0000-00-00 00:00:00";
			}
		// Insert One Record table Orders
		$data_insert = array(
						'customer_id' => $customer_id,
						'order_datetime' => date('Y-m-d H:i:s'),
						'due_datetime' => $due_datetime,
						'shipping_from' => $from,
						'shipping_to' => $to,
						'address_recipient' => $address,
						'phone_recipient' => $phone,
						'postal_code' => $postcode,
						'kota_id' => $kota_id,
						'prov_id' => $prov_id,
						'tarif_id' => $tarif_id,
						'tarif_tipe' => $tarif_tipe,
						'shipping_fee' => $ongkir,
						'shipping_weight' => $weight,
						'order_status' => 'Dropship',
						'order_payment' => 'Unpaid',
						'ref' => 'Blackberry',
						'jenis_customer' => $data_this_customer['jenis_customer']
						);
						
		$this->db->insert('orders',$data_insert);				
		
		$order_id = mysql_insert_id();
		//parsing json order item
		$result = json_decode($order_item_id);
		//var_dump($order_item_id);
		//Define Real total
		$order_total_shop = 0;
		
		foreach($result as $key => $value) {
		   
				$data_update_order_item = array('order_id' => $order_id, 'order_status' => 'Dropship');
				$where = array('id' => $value->order_item_id);
				
				$this->db->update('orders_item',$data_update_order_item,$where);
				
				// GET this order items
				$this_order_item = $this->main_model->get_detail('orders_item',array('id' => $value->order_item_id));
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
	
	function process_rekap()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		/*
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		$prov_id = $this->input->post('prov_id');
		$kota_id = $this->input->post('kota_id');
		$ongkir = $this->input->post('ongkir');
		$address = $this->input->post('address_recipient');
		$phone = $this->input->post('phone_recipient');
		$postcode = $this->input->post('postal_code');
		*/
		
		//$total = $this->input->post('total');
		$weight = $this->input->post('weight');
		
		//$total_shop = $total - $ongkir;
		
		$order_item_id = $this->input->post('order_item_id');
		
		$data_this_customer = $this->main_model->get_detail('customer',array('id' => $customer_id));
		
		$data_due_date = $this->main_model->get_detail('content',array('name' => 'due_date_setting'));
		$status_due_date = $this->main_model->get_detail('content',array('name' => 'status_due_date'));
		if ($status_due_date['value'] == "ON") {
				$days = $data_due_date['value'];
				$today = date('Y-m-d H:i:s');

		 		$date1 = str_replace('-', '/', $today);

		 		//$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . "+ ".$days." days"));
		 		$due_datetime = date('Y-m-d H:i:s', strtotime($date1 . '+'.$data_due_date['value']));
			}
			else
			{
				
				$due_datetime = "0000-00-00 00:00:00";
			}
		// Insert One Record table Orders
		$data_insert = array(
						'customer_id' => $customer_id,
						'order_datetime' => date('Y-m-d H:i:s'),
						'due_datetime' => $due_datetime,						
						'shipping_weight' => $weight,
						'order_status' => 'Keep',
						'order_payment' => 'Unpaid',
						'ref' => 'Blackberry',
						'jenis_customer' => $data_this_customer['jenis_customer']
						);
						
		$this->db->insert('orders',$data_insert);				
		
		$order_id = mysql_insert_id();
		//parsing json order item
		$result = json_decode($order_item_id);
		//var_dump($order_item_id);
		//Define Real total
		$order_total_shop = 0;
		
		foreach($result as $key => $value) {
		   
				$data_update_order_item = array('order_id' => $order_id, 'order_status' => 'Keep','order_payment' => 'Paid');
				$where = array('id' => $value->order_item_id);
				
				$this->db->update('orders_item',$data_update_order_item,$where);
				
				// GET this order items
				$this_order_item = $this->main_model->get_detail('orders_item',array('id' => $value->order_item_id));
				$order_total_shop = $order_total_shop + $this_order_item['subtotal'];
		}
		
		// Check if Data Not Valid
		
		$data_new_total = $order_total_shop;
			
		$data_new_update = array('subtotal' => $order_total_shop,'total' => $data_new_total);
		$where = array('id' => $order_id);
		$this->db->update('orders',$data_new_update,$where);
		
		$data_json = array('status' => 'Success');
		echo json_encode($data_json);
	}
	
	function get_ship_rates_prov()
	{
		$token = $this->input->post('token');
		$this->check_token($token);
	
		$data_prov = $this->main_model->get_list('jne_provinsi');
		
		echo '{ data:[';
		$data_prov_blank = array('id' => 0,'nama' => '- Pilih Provinsi -');
		echo json_encode($data_prov_blank);

		$i = 1;
		foreach($data_prov->result() as $provinsi):
		
			$data_provinsi = array(
							'id' => $provinsi->provinsi_id,
							'nama' => $provinsi->provinsi_nama
							);
							
			
			echo ','.json_encode($data_provinsi);
			
			
		$i++;
		endforeach;
		
		echo "]}";
		
	}
	
	function get_ship_rates_city()
	{
		$token = $this->input->post('token');
		$this->check_token($token);
	
		$prov_id = $this->input->post('prov_id');
		$data_city = $this->main_model->get_list_where('jne_kota',array('kota_prov_id' => $prov_id));
		
		echo '{ data:[';
		
		$i = 1;
		
		$data_cities_blank = array('id' => 0,'nama' => '- Pilih Kota -');
		
		echo json_encode($data_cities_blank);
		
		foreach($data_city->result() as $cities):
		
			$data_cities = array(
							'id' => $cities->kota_id,
							'nama' => $cities->kota_nama
							);
			
			echo ','.json_encode($data_cities);
				
		$i++;
		endforeach;
		
		echo "]}";
	}
	
	function get_ship_rates_cost()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$kota_id = $this->input->post('kota_id');
		
		$data_cost = $this->main_model->get_detail('jne_tarif',array('kota_tuju_id' => $kota_id));
		
		$data_json = array('shipping_fee' => $data_cost[$this->jne_service]);
		
		echo '{ data:[';
		echo json_encode($data_json);
		echo "]}";
	}
	
	function get_data_order()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$order_payment = $this->input->post('order_payment');
		$page = $this->input->post('page');
		
		$perpage = 20;
		$offset = $perpage * ($page - 1); 
		
		$data_order = $this->main_model->get_list_where('orders',array('customer_id' => $customer_id,'order_payment' => $order_payment),array('perpage' => $perpage, 'offset' => $offset), array('by' => 'id','sorting' => 'DESC'));

		echo '{ data:[';
		
		$i = 1;
		foreach($data_order->result() as $orders): 
		
			$data_json = array(
								'order_id' => $orders->id,
								'order_datetime' => $orders->order_datetime,
								'due_datetime' => $orders->due_datetime,
								'shipping_fee' => $orders->shipping_fee,
								'shipping_weight' => $orders->shipping_weight,
								'shipping_from' => $orders->shipping_from,
								'shipping_to' => $orders->shipping_to,
								'shipping_status' => $orders->shipping_status,
								'resi' => $orders->resi,
								'prov_id' => $orders->prov_id,
								'kota_id' => $orders->kota_id,
								'address_recipient' => $orders->address_recipient,
								'phone_recipient' => $orders->phone_recipient,
								'postal_code' => $orders->postal_code,
								'total' => $orders->total,
								'order_status' => $orders->order_status,
								'order_payment' => $orders->order_payment
								);
			
			if($i == 1)
			{
				echo json_encode($data_json);	
			}
			else
			{
				echo ','.json_encode($data_json);	
			}
			
			$i++;	
		
		endforeach;
		
		echo "]}";
		
	}
	
	function get_data_order_detail()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$order_id = $this->input->post('order_id');
		
		$data_check_order = $this->main_model->get_list_where('orders',array('id' => $order_id,'customer_id' => $customer_id));
		
		if($data_check_order->num_rows() > 0)
		{
			$data_order = $data_check_order->row_array();
			$data_kota = $this->main_model->get_detail('jne_kota',array('kota_id' => $data_order['kota_id']));
			$data_provinsi = $this->main_model->get_detail('jne_provinsi',array('provinsi_id' => $data_order['prov_id']));
			
			$data_json = array(
					'status' => 'Success',	
					'order_id' => $data_order['id'],
					'order_datetime' => $data_order['order_datetime'],
					'due_datetime' => $data_order['due_datetime'],
					'shipping_fee' => $data_order['shipping_fee'],
					'shipping_weight' => $data_order['shipping_weight'],
					'shipping_from' => $data_order['shipping_from'],
					'shipping_to' => $data_order['shipping_to'],
					'shipping_status' => $data_order['shipping_status'],
					'resi' => $data_order['resi'],
					'prov_id' => $data_order['prov_id'],
					'provinsi' => $data_provinsi['provinsi_nama'],
					'kota_id' => $data_order['kota_id'],
					'address_recipient' => $data_order['address_recipient'],
					'phone_recipient' => $data_order['phone_recipient'],
					'postal_code' => $data_order['postal_code'],
					'kota' => $data_kota['kota_nama'],
					'total' => $data_order['total'],
					'order_status' => $data_order['order_status'],
					'order_payment' => $data_order['order_payment']
					);
			
			$data_order_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id));

			foreach($data_order_item->result() as $orders_item):
				$data_this_product = $this->main_model->get_detail('product',array('id' => $orders_item->prod_id));
				$data_this_variant = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));
				
				$data_json_orders[] = array(
										'orders_item_id' => $orders_item->id,
										'name_item' => $data_this_product['name_item'],
										'variant_name' => $data_this_variant['variant'],
										'subtotal' => $orders_item->subtotal,
										'qty' => $orders_item->qty
										);
										
			endforeach;

			$data_json['orders_item'] = $data_json_orders;
			
		}
		else
		{
			$data_json = array('status' => 'Failed');
		}
		
		echo '{ data:[';
		echo json_encode($data_json);
		echo "]}";
	}
	
	function get_data_order_unpaid()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$data_order = $this->main_model->get_list_where('orders',array('customer_id' => $customer_id,'order_payment' => 'Unpaid'));

		echo '{ data:[';
		
		$i = 1;
		foreach($data_order->result() as $orders): 
		
			$data_json = array(
					'order_id' => $orders->id,
					'total' => $orders->total
					);
			
			if($i == 1)
			{
				echo json_encode($data_json);	
			}
			else
			{
				echo ','.json_encode($data_json);	
			}
			
			$i++;	
		
		endforeach;
		
		echo "]}";
	}
	
	function confirm_payment()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$nama = $this->input->post('nama');
		$bank = $this->input->post('bank');
		$jumlah = $this->input->post('jumlah');
		$rekening = $this->input->post('rekening');
		$order_id = $this->input->post('order_id');
		$payment_methode = $this->input->post('payment_methode_id');
		
		$data_insert = array(
						'customer_id' => $customer_id,
						'order_id' => $order_id,
						'date' => date('Y-m-d H:i:s'),
						'name' => $nama,
						'bank' => $bank,
						'amount' => $jumlah,
						'bank_account_number' => $rekening,
						'payment_method_id' => $payment_methode,
						'status' => 'Pending'
						);
								
		$this->db->insert('confirmation',$data_insert);		

		$data_json = array('status' => 'Success');	

		echo json_encode($data_json);	
	}
	
	function get_total_unread_message()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);

		$data_message = $this->main_model->get_list_where('message',array('customer_id' => $customer_id,'status' => 'unread'));
		
		$total_message = $data_message->num_rows();
		
		$data_json['total'] = $total_message;
		echo '{ data:['; 
		echo json_encode($data_json);
		echo ']}';
	}
	
	function get_list_message()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);


		$page = $this->input->post('page');
		
		$perpage = 10;
		$offset = $perpage * ($page - 1);
		
		$data_message = $this->main_model->get_list_where('message',array('customer_id' => $customer_id),array('perpage' => $perpage,'offset' => $offset),array('by' => 'id','sorting' => 'DESC'));
		
		$i = 1;
		echo '{ data:[';	
		
		foreach($data_message->result() as $messages):

				$data_json = array(
								'id' => $messages->id,
								'subject' => $messages->subject,
								'content' => $messages->content
								);
				
				if($i == 1)
				{
					echo json_encode($data_json);	
				}
				else
				{
					echo ','.json_encode($data_json);	
				}
		$i++;			
		endforeach;
		echo ']}'; 
 
	}
	
	function get_detail_message()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$message_id = $this->input->post('message_id');
		
		$data_message = $this->main_model->get_detail('message',array('id' => $message_id));

		// UPDATE 
		$data_update = array('status' => 'read');
		$where = array('id' => $message_id);
		$this->db->update('message',$data_update,$where);

		$data_json = array(
				'subject' => $data_message['subject'],
				'content' => $data_message['content']
				);

		echo '{ data:['; 
		echo json_encode($data_json);
		echo ']}';
		
	}
	
	function get_info()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$data_kontak = $this->main_model->get_detail('content',array('name' => 'kontak'));
		$data_rekening = $this->main_model->get_detail('content',array('name' => 'rekening'));
		$data_info = $this->main_model->get_detail('content',array('name' => 'info'));
		
		$data_json = array('kontak' => $data_kontak['value'] ,'rekening' => $data_rekening['value'],'info' => $data_info['value']);
		
		echo json_encode($data_json);
	}
	
	function update_settings()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
	
		$nama = $this->input->post('nama');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$password = $this->input->post('password');
		$alamat = $this->input->post('alamat');
		$prov_id = $this->input->post('prov_id');
		$kota_id = $this->input->post('kota_id');
		$kodepos = $this->input->post('kodepos');
		$pinbb = $this->input->post('pinbb');
		
		if(($password != null) and ($password != ''))
		{
			$encrypt_password = $this->encrypt->encode($password);
		}	
		
		
		$data_update = array(
						'name' => $nama,
						'email' => $email,
						'phone' => $phone,
						'address' => $alamat,
						'pin_bb' => $pinbb,
						'postcode' => $kodepos
						);
						
		if(($password != null) and ($password != ''))
		{
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
		
		$data_link_update = $this->main_model->get_detail('content',array('name' => 'link_blackberry'));
		$data_json = array('link' => $data_link_update['value']);
		
		echo json_encode($data_json);
		
	}

	function get_payment_methode()
	{
		$token = $this->input->post('token');
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
		
		$data_payment_methode = $this->main_model->get_list('payment_method');

		echo '{ data:[';
		$i =1;
		foreach($data_payment_methode->result() as $payment_methode):

			$data_json = array('id' => $payment_methode->id,'name' => $payment_methode->name);

		if($i == 1)
				{
					echo json_encode($data_json);	
				}
				else
				{
					echo ','.json_encode($data_json);	
				}
		$i++;			

		endforeach;
		echo ']}';
		
	}
	
	//CUSTOM
	function cancel_order_item()
	{
		$token = $this->input->post('token');
		$order_item_id = $this->input->post('order_item_id');
		
		$customer_id = $this->input->post('customer_id');
		$this->check_token($token,$customer_id);
		/*
		$data_order_item = $this->main_model->get_detail('orders_item',array('id' => $order_item_id));

		if($data_order_item['customer_id'] == $customer_id)
		{
			if ($data_value_stock ['value'] != 3 ) {
			// Re-stock 
				$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $data_order_item['variant_id']));

				$restock = $data_order_item_product['stock'] + $data_order_item['qty'];

				$data_update = array('stock' => $restock);

				$where = array('id' => $data_order_item['variant_id']);

				$this->db->update('product_variant',$data_update,$where);
			}

			// Remove order
			$where = array('id' => $order_item_id);

			$this->db->delete('orders_item',$where);

			$data_json = array('status' => 'Success');
		}
		else
		{
			$data_json = array('status' => 'Error');
		}	
		*/
$data_json = array('status' => 'Error');
		echo json_encode($data_json);
	}
	
	function get_tarif_data()
	{
		$customer_id = $this->input->post('customer_id');
		$token = $this->input->post('token');
		$this->check_token($token,$customer_id);

		$keyword = $this->input->post('keyword');

		$this->db->like('kecamatan',$keyword);
		$this->db->or_like('kota_kabupaten',$keyword);

		$query = $this->db->get('tarif');

		echo '{ data:[';

		$i = 1;

		foreach($query->result() as $list_tarif):

		$data_tarif = array(
		'tarif_id' => $list_tarif->coding, 	
		'kota' => $list_tarif->kota_kabupaten,
		'kecamatan' => $list_tarif->kecamatan,
		'tarif_reg' => $list_tarif->reg,
		'tarif_oke' => $list_tarif->oke,
		'tarif_yes' => $list_tarif->yes,
		);


		if($i == 1) {
		echo json_encode($data_tarif);
		} else {
		echo ','.json_encode($data_tarif);
		}	

		$i++;
		endforeach;

		echo "]}";

	}
	
}
			