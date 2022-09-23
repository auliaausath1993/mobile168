<?php

class Shop extends CI_Controller 
{

	Public 
		$attribute,
		$weight_format,
		$theme_path;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('cart');
		$this->load->config('shop');
		$this->load->model('cart_model');
		
		$this->attribute = $this->config->item('shop_product_attribute');
		$this->weight_format = $this->config->item('shop_weight_format');
		$this->theme_path = 'frontend/themes/'.$this->config->item('shop_theme');
	}	
	
	/* PROCESS INSERT ITEMS TO CART */
	function add_to_cart()
	{
		$item_id = $this->input->post('product_id');
		$item_name = $this->input->post('product_name');
		$item_qty = $this->input->post('product_qty');
		$item_price = $this->input->post('product_price');
		
		for ($i=0;$i<$count($this->attribute);$i++)
		{
			$attribute = $this->attribute[$i];
			$item_options[$attribute] =  $this->input->post($attribute);
		}	
		
		$data = array(
               'id'      => $item_id,
               'qty'     => $item_qty,
               'price'   => $item_price,
               'name'    => $item_name,
               'options' => array($item_options)
            );
			
		$this->cart->insert($data);
		
		// Redirect and Success Notice
		$this->session->set_flashdata('shop',$this->config->item('shop_add_cart_success'));
		redirect('shop/cart');
	}
	
	/* PROCESS UPDATE CART */
	function update_cart()
	{
		$items_rowid = $this->input->post('rowid');
		$items_qty = $this->input->post('qty');
		
		$total_items = count($items_rowid);
		
		for($i=0;$i<$total_items;$i++)
		{
			$data = array(
					'rowid' => $items_rowid[$i],
					'qty' => $items_qty
				);
		}
		
		// Redirect and Success Notice
		$this->session->set_flashdata('shop',$this->config->item('shop_update_cart_success'));
		redirect('shop/cart');
	}

	/* DELETE ITEM OF CART */
	function delete_cart($rowid)
	{
		$data = array(
					'rowid' => $row_id,
					'qty' => 0
					);
		
		$this->cart->update($data);
		
		// Redirect and Success Notice
		$this->session->set_flashdata('shop',$this->config->item('shop_delete_cart_success'));
		redirect('shop/cart');
	}	
	
	/* VIEW CART */
	function cart()
	{
		$this->load->view($this->theme_path.'/shop_view_cart');
	}
	
	/* VIEW PAGE CHECKOUT */
	function checkout()
	{
		$this->load->view($this->theme_path.'/shop_checkout_form');
	}

	/* PROCCESS CHECK SHIPPING */
	function check_shipping()
	{
		$this->load->model('main_model');
		
		$ship_rates_id = $this->input->post('ship_id');
		$ship_total_weight = $this->total->weight('ship_weight'); // GET Database Shipping
		
		$data_ship = $this->main_model->get_detail('cart_ship',array('id' => $ship_rates_id));
		
		//calculate total weight in the cart
		$total_weight_items = 0;
		foreach ($this->cart->contents() as $items)
		{
			$item_weight = $items['options']['weight'];
			$total_weight_items = $total_weight_items + $item_weight;
		}	
		
		$total_ship = ceil($total_weight_items + 1) * $data_ship['ship_cost'];
		
		$this->session->set_userdata('ship_rates',$total_ship);
		
		redirect('shop/checkout');
	}	
	
	/* PROCCESS CHECKOUT */
	
}				

	

		
		