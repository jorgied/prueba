<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Becados extends CI_Controller {

    public function index() {
       // $this->load->model('becados_model');
        $datos = $this->becados_model->getBecados();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('becados/becados', compact('datos'), TRUE);
        $this->load->view('templates',$datoPrincipal);
    }

    public function add() {
        if ($this->input->post()) {



            if ($this->form_validation->run("formulario/addBecado")) {
                $data = array
                    (
                    //'Becado_Id' => $this->input->post("usu", true),
                    'Becado_DNI' => $this->input->post("dni", true),
                    'Becado_ApeNom' => $this->input->post("nombre", true),
                    'Becado_Direccion' => $this->input->post("dir", true),
                    'Becado_Telefono' => $this->input->post("tel", true),
                    'Becado_Email' => $this->input->post("email", true),
                );

               // $this->load->model('becados_model');
                $guardar = $this->becados_model->insertarBecado($data);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha agregado el registro exitosamente.');
                    redirect(base_url() . 'becados', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'becados/add', 301);
                }
            }
        }
        $this->load->view('becados/add');
    }

    




    public function edit($usr = null) {
        if (!$usr) {
            show_404();
        }
        if ($this->input->post()) {
            if ($this->form_validation->run("formulario/addBecado")) {
                $data = array
                    (
                    'Becado_Id' => $this->input->post("usr", true)  ,
                    'Becado_ApeNom' => $this->input->post("nombre", true),
                    'Becado_DNI' => $this->input->post("dni", true),
                    'Becado_Telefono' => $this->input->post("tel", true),
                    'Becado_Direccion' => $this->input->post("dir", true),
                    'Becado_Email' => $this->input->post("email", true),
                    
                );
                $guardar = $this->becados_model->modificarBecado($data, $usr);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha editado el registro exitosamente.');
                    redirect(base_url() . 'becados' , $usr, 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'becados/edit' . $usr, 301);
                }
            }
        }
        $datos = $this->becados_model->getBecId($usr);
        if (sizeof($datos) == 0) {
            show_404();
        }

        $this->load->view('becados/edit', compact('usr', 'datos'));
    }

}
