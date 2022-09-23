<?php

class Cronjob extends CI_Controller {

	Public function __construct() {
		parent::__construct();

		date_default_timezone_set('Asia/Jakarta');

		$this->load->model('main_model');
	}

	function get_expired_order($token = null) {
		if($token == $this->config->item('tokomobile_token')) {
			$date = date('Y-m-d H:i:s');
			$data_order_expired = $this->main_model->get_list_where('orders_item',
				array(
					'due_datetime   !='	=> '0000-00-00 00:00:00',
					'order_payment' 	=> 'Unpaid',
					'order_status'  	=> 'Keep',
					'due_datetime   <=' => $date
				)
			);
			$i = 0;
			foreach($data_order_expired->result() as $expired) {
				$this->cancel_order_item($expired->id);
				$i++;
			}
			$data_json = array('status' => 'Success','total_canceled' => $i);
		} else {
			$data_json = array('status' => 'Error','reason' => 'Invalid token');
		}
		echo json_encode($data_json);
	}

	function cancel_order_item($order_item_id = null) {
		$data_order_item  = $this->main_model->get_detail('orders_item', array('id' => $order_item_id));
		$data_value_stock = $this->main_model->get_detail('content', array('name' => 'stock_setting'));

		// Re-stock

		$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $data_order_item['variant_id']));

		// GET Data Order

		$data_order = $this->main_model->get_list_where('orders',array('id' => $data_order_item['order_id']));
		if($data_order->num_rows() == 0) {

			if ($data_value_stock ['value'] != 3 ) {
				$restock = $data_order_item_product['stock'] + $data_order_item['qty'];
				$data_update = array('stock' => $restock);
				$where = array('id' => $data_order_item['variant_id']);
				$this->db->update('product_variant',$data_update,$where);
			}

			$order_cancel = $this->main_model->get_detail('orders_item_cancel', array('id' => $order_item_id));
            if (empty($order_cancel)) {
    			$data_cancel = array(
    				'id'             => $data_order_item['id'],
    				'customer_id'    => $data_order_item['customer_id'],
    				'order_datetime' => $data_order_item['order_datetime'],
    				'prod_id'        => $data_order_item['prod_id'],
    				'variant_id'     => $data_order_item['variant_id'],
    				'qty'            => $data_order_item['qty'],
    				'price'          => $data_order_item['price'],
    			);

    			$this->db->insert('orders_item_cancel', $data_cancel);
            }

			// Remove order
			$where = array('id' => $order_item_id);
			$this->db->update('orders_item', array('order_status' => 'Cancel'), $where);
		}
	}

	function get_expired_order_dropship_rekap($token = null) {
		if($token == $this->config->item('tokomobile_token')) {
			$date = date('Y-m-d H:i:s');

			//Get Expired Order Dropship
			$data_order_expired_dropship = $this->main_model->get_list_where('orders',
				array(
					'due_datetime   !='	=> '0000-00-00 00:00:00',
					'order_payment'     => 'Unpaid',
					'order_status'      => 'Dropship',
					'due_datetime   <=' => $date
				)
			);
			$i = 0;
			foreach($data_order_expired_dropship->result() as $expired) {
				$payment = $this->main_model->get_list_where('confirmation', array('order_id' => $expired->id));
				if ($payment->num_rows() < 1) {
					$this->cancel_order_item_dropship($expired->id);
					$i++;
				}
			}

			//Get Expired Order Rekap
			$data_order_expired_rekap = $this->main_model->get_list_where('orders',
				array(
					'due_datetime   !='	=> '0000-00-00 00:00:00',
					'order_payment'     => 'Unpaid',
					'order_status'      => 'Keep',
					'due_datetime   <=' => $date
				)
			);
			$j = 0;
			foreach($data_order_expired_rekap->result() as $expired) {
				$this->cancel_order_item_dropship_rekap($expired->id);
				$j++;
			}

			$data_json = array(
				'status' => 'Success',
				'total_canceled_dropship' => $i,
				'total_canceled_dropship' => $j
			);
		} else {
			$data_json = array(
				'status' => 'Error',
				'reason' => 'Invalid token'
			);
		}
		echo json_encode($data_json);
	}

	function cancel_order_item_dropship_rekap($order_id = null) {
		$data_value_stock = $this->main_model->get_detail('content',array('name' => 'stock_setting'));
		$data_order = $this->main_model->get_detail('orders',array('id' => $order_id));
		$data_order_item = $this->main_model->get_list_where('orders_item',array('order_id' => $order_id));
		foreach($data_order_item->result() as $orders_item) {

			// Re-stock
			$data_order_item_product = $this->main_model->get_detail('product_variant',array('id' => $orders_item->variant_id));

			if ($data_value_stock ['value'] != 3 ) {
				$restock = $data_order_item_product['stock'] + $orders_item->qty;
				$data_update = array('stock' => $restock);
				$where = array('id' => $orders_item->variant_id);
				$this->db->update('product_variant',$data_update,$where);
			}

			// Remove order
			$where = array('id' => $orders_item->id);
			$this->db->delete('orders_item',$where);
		}
		$where = array('id' => $order_id);
		$this->db->delete('orders',$where);
	}

}