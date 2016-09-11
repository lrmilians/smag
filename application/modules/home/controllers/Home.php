<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MX_Controller
{

	public function __construct()
	{

		parent::__construct();

	}

	//llamamos a la función data_users la cuál nos
	//entrega un array con los usuarios
	public function index()
	{
		//utilizamos el método say del módulo welcome
		$data['welcomeMsg'] =  Modules::run('welcome/say');
		var_dump($data);
	}

}
