<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . 'libraries/Zyght_Model.php');

class Company_model extends Zyght_Model {
	public function __construct(){
		parent::__construct();

		$this->table = 'Company';
		$this->id = 'id';
	}

	public function create($name, $code) {
		$this->db->insert($this->table, array(
			'name' => $name,
			'code' => $code,
			'active' => 1
		));

		$id = $this->db->insert_id();

		return ($id > 0) ? $id : FALSE;
	}

	public function update($id, $name = NULL, $code = NULL) {
		$data = array();

		if (isset($name)) {
			$data['name'] = $name;
		}
		
		if (isset($code)) {
			$data['code'] = $code;
		}
		
		if (!empty($data)) {
			$this->db->where($this->id, $id);
			return $this->db->update($this->table, $data);
		}
		
		return FALSE;
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
	
	public function get_actives() {
		$this->db->select('*');
		$this->db->from($this->table);
		$this->db->where('active', 1);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : array();
	}

}
