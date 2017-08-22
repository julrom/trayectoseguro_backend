<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Question_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Question';
		$this->id = 'id';
	}
	
	public function create($name) {
		$last_order = $this->_get_last_display_order();
		$last_order = ($last_order === FALSE) ? 1 : ++$last_order;
	
		$this->db->insert($this->table, array(
				'title' => $name,
				'display_order' => $last_order,
				'active' => 1
		));
		
		$id = $this->db->insert_id();
		
		return ($id > 0) ? $id : FALSE;
	}
	
	public function get_actives() {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('active', 1);
		$this->db->order_by("display_order", "asc"); 
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function get_all() {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->order_by("display_order", "asc");
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? $query->result() : array();
	}
	
	public function activate($id, $activate) {
		$data = array();
		
		if (isset($activate)) {
			$data['active'] = $activate;
		}
		
		if (!empty($data)) {
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, $data);
		}
		
		return FALSE;
	}
	
	public function update($id = NULL, $title = NULL){
		if(isset($id) && isset($title)){
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, array('title' => $title));
		}
		return FALSE;
	}
	
	private function _get_last_display_order(){
		$this->db->select_max('display_order');
		$this->db->from($this->table);
		$query = $this->db->get();
		
		return ($query->num_rows() > 0) ? $query->row()->display_order : FALSE;
	}

}
