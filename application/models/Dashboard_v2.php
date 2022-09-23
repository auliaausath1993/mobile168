<?php


class Dashboard_v2 extends CI_Model {
	public function __construct()
	{
		$this->load->database();
	}

	public function graph_dropshipper()
	{
		$data = $this->db->query("SELECT orders_item.customer_id, orders_item.qty, customer.name AS name_customer, customer_type.name
			FROM orders_item
			INNER JOIN customer ON orders_item.customer_id=customer.id
			LEFT JOIN customer_type ON orders_item.customer_id = customer_type.id ORDER BY orders_item.qty DESC LIMIT 10");
		return $data->result();
	}

	public function graph_reseller()
	{
		$data = $this->db->query("SELECT orders_item.customer_id, orders_item.qty, customer.name AS name_customer, customer_type.name
			FROM orders_item
			INNER JOIN customer ON orders_item.customer_id=customer.id
			LEFT JOIN customer_type ON orders_item.customer_id = customer_type.id ORDER BY orders_item.qty DESC LIMIT 10");
		return $data->result();
	}

	public function total_omset()
	{
		// $query_7 = "SELECT NOW() as sekarang, DATE_SUB(NOW(), INTERVAL 7 DAY) as '- 7 hari'";
		$data = $this->db->query("SELECT sum(total) AS total_omset FROM (select orders.total FROM orders ORDER BY orders.date_payment desc limit 7) AS total_omset");
		return $data->result();
	}

	public function total_pelanggan()
	{
		$data = $this->db->query("SELECT CONCAT(YEAR(created_at),'/',MONTH(created_at)) AS tahun_bulan, COUNT(*) AS jumlah_bulanan
			FROM customer
			WHERE CONCAT(YEAR(created_at),'/',MONTH(created_at))=CONCAT(YEAR(NOW()),'/',MONTH(NOW()))
			GROUP BY YEAR(created_at),MONTH(created_at)");
		return $data->result();
	}

	public function total_keep_paid()
	{
		$data = $this->db->query("SELECT CONCAT(YEAR(order_datetime),'/',MONTH(order_datetime)) AS tahun_bulan, COUNT(*) AS jumlah_bulanan
			FROM orders_item
			WHERE order_status = 'Keep' AND order_payment = 'Paid' AND CONCAT(YEAR(order_datetime),'/',MONTH(order_datetime))=CONCAT(YEAR(NOW()),'/',MONTH(NOW()))
			GROUP BY YEAR(order_datetime),MONTH(order_datetime)");
		return $data->result();
	}

	public function total_keep_unpaid()
	{
		$data = $this->db->query("SELECT CONCAT(YEAR(order_datetime),'/',MONTH(order_datetime)) AS tahun_bulan, COUNT(*) AS jumlah_bulanan
			FROM orders_item
			WHERE order_status = 'Keep' AND order_payment = 'Unpaid' AND CONCAT(YEAR(order_datetime),'/',MONTH(order_datetime))=CONCAT(YEAR(NOW()),'/',MONTH(NOW()))
			GROUP BY YEAR(order_datetime),MONTH(order_datetime)");
		return $data->result();
	}

	public function data_expired()
	{
		$data = $this->db->query("SELECT datediff(data_license.expired_date, now()) AS selisih FROM data_license");
		return $data->result();
	}

	public function jumlah_laku()
	{
		$data = $this->db->query("SELECT SUM(qty) AS jumlah_terjual, CONCAT(MONTH(order_datetime)) AS bulan
			FROM orders_item
			WHERE prod_id = 1 AND CONCAT(MONTH(order_datetime))
			GROUP BY MONTH(order_datetime)");
		return $data->result();
	}

	public function jumlah_laku_v2()
	{
		$data = $this->db->query("SELECT MONTHNAME(orders_item.order_datetime) AS bulan, product.name_item, sum(orders_item.qty) as jumlah_qty FROM product  
			JOIN orders_item on orders_item.prod_id = product.id GROUP by product.id,  CONCAT(MONTH(orders_item.order_datetime)) order by product.id");
		return $data->result();
	}

}