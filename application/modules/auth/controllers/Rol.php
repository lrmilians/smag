<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rol extends REST_Controller {

	protected $allowed_http_methods = array('get', 'delete', 'post', 'put');

	function __construct()
	{
		//header('Access-Control-Allow-Origin:');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == "OPTIONS") {
			die();
		}

		parent::__construct();

		//$this->load->library(array('ion_auth','form_validation'));
		$this->load->helper(array('url','language'));

		//$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

		//$this->lang->load('auth');

		// Configure limits on our controller methods. Ensure
		// you have created the 'limits' table and enabled 'limits'
		// within application/config/rest.php
		$this->methods['userprofile_get']['limit'] = 1; //500 requests per hour per user/key
	//	$this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key

		$this->load->model('session_model');
		$this->load->model('rol_model');
	}

	function roles_post(){
		$token = $this->get('token');
		$session = $this->_checksession($token);
		if($session == -1){
			//$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
			$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
		} else {
			if($session == 0){
				$data = array('select' => array('id', 'name', 'description', 'code'));
				if($this->post('start') != '' || $this->post('size') != ''){
					$roles = $this->rol_model->get_rol($data, 'groups', FALSE, array($this->post('start'), $this->post('size')));
				} else {
					$roles = $this->rol_model->get_rol($data, 'groups', FALSE);
				}
				if($roles == false){
					$this->response($this->data_error_response('00', 'Error recuperando los roles.'), 500);
				} else {
					$response['status'] = 'EMPTY';
					if(!empty($data)){
						$response['status'] = 'OK';
					}
					$response['message'] = '';
					$response['total_rows'] = $this->rol_model->get_total_rows('groups');
					$response['data'] = $roles;

					$this->response($response, 200);
				}
			} else { //Session caducada
				$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
			}
		}
	}

	function userprofile_get(){
		$token = $this->get('token');
		$session = $this->_checksession($token);
		if($session == -1){
			//$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
			$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
		} else {
			if($session == 0){
				$data = array(
					'select' => '',
					'field' => 'token',
					'value' => $token,
				);
				$profile = $this->user_model->get_user($data, 'users');
				if($profile == false){
					$this->response($this->data_error_response('00', 'Error recuperando el usuario.'), 500);
				} else {
					$response['status'] = 'OK';
					$response['message'] = '';
					$response['data'] = array (
						'username' => $profile[0]['username'],
						'email' => $profile[0]['email'],
						'first_name' => $profile[0]['first_name'],
						'last_name' => $profile[0]['last_name'],
					);
					$this->response($response, 200);
				}
			} else { //Session caducada
				$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
			}
		}
	}

	function userprofile_post(){
		$token = $this->get('token');
		$session = $this->_checksession($token);
		if($session == -1){
			//$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
			$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
		} else {
			if($session == 0){
				$identity = array(
					'field' => 'token',
					'value' => $token
				);
				$data = array(
					'first_name' => $this->post('first_name'),
					'last_name' => $this->post('last_name'),
					'modified' => strtotime(date('Y-w-d h:i:s'))
				);
				$save = $this->user_model->update_user($identity, $data);
				if($save == false){
					$this->response($this->data_error_response('00', 'Error salvando el usuario.'), 500);
				} else {
					$response['status'] = 'OK';
					$response['message'] = null;
					$response['data'] = null;
					$this->response($response, 200);
				}
			} else { //Session caducada
				$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
			}
		}
	}

	private function _checksession($token) {
		//result = 0  Session actived
		//result = 1  Session expired
		//result = -1  Error
		return $this->session_model->check_session($token);
	}

}
