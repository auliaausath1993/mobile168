<?php

/* Model Main */

class Main_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
	}
	
	function get_category_list($limit, $start){
        $query = $this->db->get_where('product_category', array('status_category' => 'publish'), $limit, $start);
        return $query;
    }

	function get_slide_home()
	{
		$this->db->order_by('slide_sort','ASC');
		$query = $this->db->get('slide');

		return $query->result();
	}

	function get_all($table)
	{
		$query = $this->db->get($table);

		return $query;
	}

	function get_list_all($table)
	{
		$query = $this->db->get($table);

		return $query->result();
	}

	function get_list($table, $limit = null, $sort = null)
	{
		if($limit != null) {
			$this->db->limit($limit['perpage'],$limit['offset']);
		}
		if($sort != null) {
			$this->db->order_by($sort['by'],$sort['sorting']);
		}
		$query = $this->db->get($table);

		return $query;
	}

	function get_list_where($table, $where = array(), $limit = null, $sort = null)
	{
		$this->db->where($where);
		if($limit != null) {
			$this->db->limit($limit['perpage'],$limit['offset']);
		}
		if($sort != null) {
			$this->db->order_by($sort['by'],$sort['sorting']);
		}
		$query = $this->db->get($table);

		return $query;
	}

	function get_list_where_in($table,$field, $where = array(), $limit = null, $sort = null)
	{
		$this->db->where_in($field,$where);

		if($limit != null) {
			$this->db->limit($limit['perpage'],$limit['offset']);
		}
		if($sort != null) {
			$this->db->order_by($sort['by'],$sort['sorting']);
		}
		$query = $this->db->get($table);

		return $query;
	}

	function get_list_where_total($table, $field, $value)
	{
		$this->db->where($field,$value);
		$query = $this->db->get($table);

		return $query->num_rows();
	}

	function get_join_where($table,$table_join,$where_join, $where = array())
	{
		$this->db->select('*');
		$this->db->from($table);
		$this->db->join($table_join,$where_join);
		$this->db->where($where);

		return $this->db->get();
	}

	function get_list_by_archive($table,$field,$value,$limit = array())
	{
		$query_clause = "SELECT * FROM $table WHERE DATE_FORMAT($field, '%M-%Y') = '$value' ORDER BY id DESC LIMIT $limit[offset], $limit[perpage]  ";
		$query = $this->db->query($query_clause);

		return $query->result();
	}

	function get_list_by_archive_total($table,$field,$value)
	{
		$query_clause = "SELECT * FROM $table WHERE DATE_FORMAT($field, '%M-%Y') = '$value' ";
		$query = $this->db->query($query_clause);

		return $query->num_rows();
	}

	function get_detail($table,$where = array())
	{
		$this->db->where($where);
		$query = $this->db->get($table);

		return $query->row_array();
	}

	function get_detail_total($table,$where = array())
	{
		$this->db->where($where);
		$query = $this->db->get($table);

		return $query->num_rows();
	}

	function get_archive_year($table,$field)
	{
		$query_clause = "SELECT DISTINCT DATE_FORMAT($field,'%Y') AS year FROM $table ORDER BY id DESC";
		$query = $this->db->query($query_clause);

		return $query->result();
	}

	function get_archive_month($table,$field,$year)
	{
		$query_clause = "SELECT DISTINCT DATE_FORMAT($field, '%M') AS month FROM $table WHERE DATE_FORMAT($field, '%Y') = '$year' ORDER BY id DESC";
		$query = $this->db->query($query_clause);

		return $query;
	}

	function get_archive_day($table,$field,$day)
	{
		$query_clause = "SELECT * FROM $table WHERE order_status != 'Cancel' AND DATE_FORMAT($field, '%Y-%m-%d') = '$day' ORDER BY id DESC";
		$query = $this->db->query($query_clause);

		return $query;
	}

	function search_product($lang,$q)
	{
		$product_description = "product_description_".$lang;
		$this->db->like('product_name',$q);
		$this->db->or_like($product_description,$q);

		$query = $this->db->get('product');

		return $query;
	}

	function search_news($lang,$q)
	{
		$news_title = "news_title_".$lang;
		$news_content = "news_content_".$lang;

		$this->db->like($news_title,$q);
		$this->db->or_like($news_content,$q);

		$query = $this->db->get('news');

		return $query;
	}

	function check_user($username = "",$password = "")
	{
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

		$query = $this->db->get_where($this->table,array('user_name' => $username));

		$result = $query->row_array();

		$this->user_password = $result['user_pass'];

		$this->user_password = $this->encrypt->decode($this->user_password);

		if(($query->num_rows() > 0 ) AND ($password === $this->user_password))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	function update_status_data_pelanggan($tableName,$data,$where)
	{
		$query=$this->db->update($tableName,$data,$where);
		return $query;
	}
}