<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends REST_Controller {

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
		$this->load->model('user_model');
	}

	function users_post(){
		$token = $this->get('token');
		$session = $this->_checksession($token);
		if($session == -1){
			$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
		} else {
			if($session == 0){
				$data = array('select' => array('users.id','username', 'email', 'first_name', 'last_name', 'last_login', 'active', 'company','description', 'login', 'rol_id', 'identification'));
				$users = $this->user_model->get_user($data, 'users', FALSE, array($this->post('start'),$this->post('size')));
				if($users == false){
					$this->response($this->data_error_response('00', 'Error recuperando los usuarios.'), 500);
				} else {
					$response['status'] = 'EMPTY';
					if(!empty($data)){
						$i = 0;
						foreach($users as $value){
							if($value['last_login'] != null){
								$users[$i]['last_login'] = date('Y-m-d H:i:s', $value['last_login']);
							}
							$i++;
						}
						$response['status'] = 'OK';
					}
					$response['message'] = '';
					$response['total_rows'] = $this->user_model->get_total_rows('users');
					$response['data'] = $users;

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
			$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
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
						'identification' => $profile[0]['identification'],
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
			$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
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

	function user_post(){
		$token = $this->get('token');
		$session = $this->_checksession($token);
		if($session == -1){
			$this->response($this->data_error_response('00', 'Error chequeando sesion.'), 500);
		} else {
			if($session == 0){
				if($this->post('action') == -1){
					$data = array(
						'username' => $this->post('username'),
						'identification' => $this->post('identification'),
						'password' => 'password',
						'email' => $this->post('email'),
						'created_on' => strtotime(date('Y-w-d h:i:s')),
						'active' => 1,
						'first_name' => $this->post('first_name'),
						'last_name' => $this->post('last_name'),
						'company' => $this->post('company'),
						'phone' => $this->post('phone'),
						'rol_id' => $this->post('rol_id')
					);
					$save = $this->user_model->add_user($data);
					if($save == false){
						$this->response($this->data_error_response('00', 'Error salvando el usuario.'), 500);
					} else {
						$response['status'] = 'OK';
						$response['message'] = null;
						$response['data'] = null;
						$this->response($response, 200);
					}
				} else {
					$identity = array(
						'field' => 'id',
						'value' => $this->post('action')
					);
					$data = array(
						'first_name' => $this->post('first_name'),
						'last_name' => $this->post('last_name'),
						'modified' => strtotime(date('Y-w-d h:i:s')),
						'active' => $this->post('active') == true ? 1 : 0,
						'company' => $this->post('company'),
						'rol_id' => $this->post('rol_id'),
					);
					$save = $this->user_model->update_user($identity, $data);
					if($save == false){
						$this->response($this->data_error_response('00', 'Error actualizando el usuario.'), 500);
					} else {
						$response['status'] = 'OK';
						$response['message'] = null;
						$response['data'] = null;
						$this->response($response, 200);
					}
				}
			} else { //Session caducada
				$this->response($this->data_error_response('01', 'Sesion caducada.'), 500);
			}
		}
	}

	function userexists_post(){
		$data_username = array(
			'select' => '',
			'field' => 'username',
			'value' => $this->post('username')
		);
		$username = $this->user_model->get_user($data_username, 'users');
		$data_id = array(
			'select' => '',
			'field' => 'identification',
			'value' => $this->post('identification')
		);
		$id = $this->user_model->get_user($data_id, 'users');
		$response['status'] = 'OK';
		$response['message'] = 'Verificando si existe usuario.';
		$response['username'] = true;
		$response['identification'] = true;

		if($username != false){
			$response['username'] = false;
		}
		if($id != false){
			$response['identification'] = false;
		}
		$this->response($response, 200);
	}

	private function _checksession($token) {
		//result = 0  Session actived
		//result = 1  Session expired
		//result = -1  Error
		return $this->session_model->check_session($token);
	}

}
