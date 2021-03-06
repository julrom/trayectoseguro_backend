<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/API_Controller.php');

class Rtravel extends API_Controller {
	private $resource;

	function __construct() {
		parent::__construct();

		$this->resource = 'Rtravel';

		$this->load->model('user_model');
		$this->load->model('travel_model');
		$this->load->model('travellog_model');
	}

	public function add_post() {
		$access_token = $this->get_access_token();

		$user = $this->user_model->get_loggedin_user($access_token);
		if ($user === FALSE) {
			$this->response_error(404, array(
				"Error en token"
			));
		}

		$answers = $this->json_decode($this->post('answers'));
		$travel_logs = $this->json_decode($this->post('travel_logs'));
		$max_speed = $this->post('max_speed');
		$average_speed= $this->post('average_speed');
		$distance = $this->post('distance');
		$duration = $this->post('duration');
		
		$travel_id = $this->travel_model->create($user, $answers, $travel_logs,
				$max_speed, $average_speed, $distance, $duration);

		if ($travel_id === FALSE) {
			$this->response_error(404);
		}

		$result = $this->travel_model->get_travel_by_id($travel_id);

		$this->response_ok($result);
	}

	public function list_get() {
		$result = $this->travel_model->get_all_with_user(
			$this->get('company_id'), 
			$this->get('user_id') 
		);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}


	public function list_logs_get() {
		$travel_id = $this->get('travel_id');

		$result = $this->travellog_model->get_by_travel_id($travel_id);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}

	public function download_logs_get() {
		$this->load->helper('download');

		$travel_id = $this->get('travel_id');

		$logs = $this->travellog_model->get_by_travel_id($travel_id);

		if ($logs === FALSE) {
			$this->response_error(404);
		}

		$csv = 'Latitud,Longitud,Fecha' . "\r\n";
		foreach ($logs as $log) {
			$csv .= $log->latitude . ',' . $log->longitude . ',' . $log->date . "\r\n";	
		}

		force_download('travel_logs.csv', $csv);

		// $this->response_ok($result);
	}

	public function summary_get() {
		$travel_id = $this->get('travel_id');

		$result = $this->travellog_model->get_travel_info($travel_id);

		if ($result === FALSE) {
			$this->response_error(404);
		}

		$this->response_ok($result);
	}
	
	public function list_by_id_get() {
		if (!$this->get('travel_id')) {
			$this->response_error(400);
		}
		
		$result = $this->travel_model->get_travel_by_id($this->get('travel_id'));
		
		if ($result === FALSE) {
			$this->response_error(404);
		}
		
		$this->response_ok($result);
	}
}
