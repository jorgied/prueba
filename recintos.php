<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recintos extends MY_Controller {

//    public function index() {
//        $datos = $this->recintos_model->getRecintos();
//        $this->load->view('recintos/recintos', compact('datos'));
//    }

     public function index() {
       
        $datos = $this->recintos_model->getrecintos();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('recintos/recintos', compact('datos'), TRUE);
        $this->load->view('templates_admin', $datoPrincipal);
    }

    public function add() {
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/recintos")) {
                $data = array
                    (
                    'Rec_Id' => NULL,
                    'Rec_Nombre' => $this->input->post("nombre", true),
                    'Rec_Ubicacion' => $this->input->post("ubic", true),
                    
                );
                $guardar = $this->recintos_model->insertarRecintos($data);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha agregado el registro exitosamente.');
                    redirect(base_url() . 'recintos', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'recintos/add', 301);
                }
            }
        }
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('recintos/add','', TRUE);
        $this->load->view('templates_admin', $datoPrincipal);
    }

    public function edit($usr = null) {
        if (!$usr) {
            show_404();
        }
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/recintos")) {
                $data = array
                    (
                    'Rec_Id' => $this->input->post("usr", true),
                    'Rec_Nombre' => $this->input->post("nombre", true),
                    'Rec_Ubicacion' => $this->input->post("ubic", true),
                );
                $guardar = $this->recintos_model->modificarRecintos($data, $usr);

                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha editado el registro exitosamente.');
                    redirect(base_url() . 'recintos', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'recintos/edit' . $usr, 301);
                }
            }
        }
        $datos = $this->recintos_model->getRecId($usr);
        if (sizeof($datos) == 0) {
            show_404();
        }

        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('recintos/edit', compact('usr', 'datos'), TRUE);
        $this->load->view('templates_admin', $datoPrincipal);
    }

}
