<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Bills extends REST_Controller {

    /*public function __construct() {
        parent::__construct();
        $this->load->models('bill_model');
    }*/

    function __construct() {
        // Construct our parent class
        parent::__construct();
        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['bill_get']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['facs_get']['limit'] = 2; //500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
        $this->load->model('bill_model');
    }
    
    public function index() {
       
        $this->load->view('../../../client/app/index.html');
    }   
    
    public function getAllBills() {
        echo $this->javascript->generate_json($this->bill_model->get_all_bills());
    }

    function facs_get()
    {
        $bills = $this->bill_model->get_all_bills( $this->get('limit') );
        /*$bills = array(
            array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com'),
            array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com'),
            3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => array('hobbies' => array('fartings', 'bikes'))),
        );*/


        if($bills)
        {
            $this->response($bills, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any bills!'), 404);
        }
    }

    
    public function getMessagesByBillName($name){
        echo $this->javascript->generate_json($this->bill_model->getMessageByBillName($name));
    }
    
   /* public function editBill() {
        $this->form_validation->set_rules('name', 'name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'password', 'trim|required|xss_clean');

        if($this->form_validation->run() == false) {
            echo $this->javascript->generate_json(array("respuesta" => "failedform -> formulario no valido"));
        } else {
            $id = $this->input->post("id");
            $data = array(
                "name" => $this->input->post("name"),
                "password" => $this->input->post("password"),
                "active" => $this->input->post("active"),
                "modified" => date("Y-m-d H:i:s")
            );
            if($this->key_model->updateKey($id, $data)) {
                echo $this->javascript->generate_json(array("respuesta" => "success"));
            } else {
                echo $this->javascript->generate_json(array("respuesta" => "failedProcess"));
            }
        }
    }*/
    
    public function deleteBill() {
        $name = $this->input->post("name");
        unlink($this->config->item('path_xml_unsigned') . '/' . $name);
        unlink($this->config->item('path_xml_result') . '/' . $name);
        unlink($this->config->item('path_xml_signed') . '/' . $name);
        unlink($this->config->item('path_xml_back') . '/' . $name);
        $id = $this->input->post("id");
        if(!$id || !is_numeric($id)) {
            echo $this->javascript->generate_json(array("respuesta" => "error"));
        } else if ($this->bill_model->deleteBill($id)) {     
            echo $this->javascript->generate_json(array("respuesta" => "correcto"));
        }
    }
    
    public function addBill() {
        $date_now = date("Y-m-d");
        $year = date("Y");
        $path_unsigned = $this->config->item('path_xml') . '/' . 'facturas_electronicas_xml' . '/' . $year . '/' . $date_now . '/nofirmada';
       
        $config['upload_path'] = $path_unsigned;
        if(!is_dir($path_unsigned)){
            mkdir($path_unsigned, 0777, true);
        }
        $config['allowed_types'] = '*';
        $config['max_size']	= '5120';
        $config['overwrite'] = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if(!$this->upload->do_upload('file')){
            echo $this->upload->display_errors();
        }else{
            $this->form_validation->set_rules('name', 'name', 'trim|required|xss_clean');

            if($this->form_validation->run() == false) {
                echo $this->javascript->generate_json(array("respuesta" => "failedform -> formulario no valido"));
            } else {
                $date = date('Y-m-d H:i:s');
                $data = array(
                    'name'  =>  $this->input->post('name'),
                    'state'  =>  'NO PROCESADA',
                    'processed'  =>  false,
                    'created'  => $date,
                    'modified'   => $date
                );
                if($this->bill_model->addBill($data)) {
                    echo $this->javascript->generate_json(array("respuesta" => "success"));
                } else {
                    echo $this->javascript->generate_json(array("respuesta" => "failedProcess"));
                }
            }
        }
    }
    
    public function getBillById ($id) {
        echo $this->javascript->generate_json($this->key_model->getBillById($id));
    }
    
    public function getBillByName () {
        if ($this->bill_model->getBillByName($this->input->post('name'))) {
            echo $this->javascript->generate_json(array("response" => "exit"));
        } else {
            echo $this->javascript->generate_json(array("response" => "noexit"));
        }
    }
    
    
}