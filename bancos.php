<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bancos extends MY_Controller {

    public function index() {
        $datos = $this->bancos_model->getBancos();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('bancos/bancos', compact('datos'), TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

    public function add() {
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/bancos")) {
                $data = array
                    (
                    'Banco_Id' => NULL,
                    'Banco_Nombre' => $this->input->post("nombre", true),
                    
                );
                $guardar = $this->bancos_model->insertarBancos($data);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha agregado el registro exitosamente.');
                    redirect(base_url() . 'bancos', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'bancos/add', 301);
                }
            }
        }
        $this->load->view('bancos/add');
    }

    public function edit($usr = null) {
        if (!$usr) {
            show_404();
        }
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/bancos")) {
                $data = array
                    (
                    'Banco_Id' => $this->input->post("usr", true),
                    'Banco_Nombre' => $this->input->post("nombre", true),
                   
                );
                $guardar = $this->bancos_model->modificarBancos($data, $usr);

                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha editado el registro exitosamente.');
                    redirect(base_url() . 'bancos', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'bancos/edit' . $usr, 301);
                }
            }
        }
        $datos = $this->bancos_model->getBanId($usr);
        if (sizeof($datos) == 0) {
            show_404();
        }

        $this->load->view('bancos/edit', compact('usr', 'datos'));
    }

}
