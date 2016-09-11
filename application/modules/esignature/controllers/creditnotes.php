<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('display_errors', '1');

class creditnotes extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("creditnote_model");
    }

    public function index() {
        $this->load->view('../../../client/app/index.html');
    }

    public function getAllCreditnotes() {
        echo $this->javascript->generate_json($this->creditnote_model->get_all_creditnotes());
    }

    public function getMessagesByCreditnoteName($creditnote_name) {
        echo $this->javascript->generate_json($this->creditnote_model->getMessageByCreditnoteName($creditnote_name));
    }
    
    public function addCreditnote() {
        $date_now = date("Y-m-d");
        $year = date("Y");
        $path_unsigned = $this->config->item('path_xml') . '/' . 'notas_credito_electronicas_xml' . '/' . $year . '/' . $date_now . '/nofirmada';
       
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
                if($this->creditnote_model->addCreditnote($data)) {
                    echo $this->javascript->generate_json(array("respuesta" => "success"));
                } else {
                    echo $this->javascript->generate_json(array("respuesta" => "failedProcess"));
                }
            }
        }
    }
    
    public function getCreditnoteById ($id) {
        echo $this->javascript->generate_json($this->creditnote_model->getCreditnoteById($id));
    }
    
    public function getCreditnoteByName () {
        if ($this->creditnote_model->getCreditnoteByName($this->input->post('name'))) {
            echo $this->javascript->generate_json(array("response" => "exit"));
        } else {
            echo $this->javascript->generate_json(array("response" => "noexit"));
        }
    }

}
