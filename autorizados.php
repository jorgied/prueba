<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Autorizados extends MY_Controller {

    public function index() {
        $datos = $this->autorizados_model->getAutorizados();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('autorizados/autorizados', compact('datos'), TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

    public function add() {
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/autorizados")) {
                $data = array
                 (       
                    'Aut_Id' => NULL,
                    'Aut_Apenom' => $this->input->post("nombre", true),
                    'Aut_DNI' => $this->input->post("dni", true),
                    'Aut_Direccion' => $this->input->post("dir", true),
                    'Aut_Telefono' => $this->input->post("tel", true),
                    'Aut_Mail' => $this->input->post("email", true),
                );
                $guardar = $this->autorizados_model->insertarAutorizados($data);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha agregado el registro exitosamente.');
                    redirect(base_url() . 'autorizados', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'autorizados/add', 301);
                }
            }
        }
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('autorizados/add','', TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

    public function edit($usr = null) {
        if (!$usr) {
            show_404();
        }
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/autorizados")) {
                $data = array
                    (
                    'Aut_Id' => $this->input->post("usr", true),
                    'Aut_Apenom' => $this->input->post("nombre", true),
                    'Aut_DNI' => $this->input->post("dni", true),
                    'Aut_Direccion' => $this->input->post("dir", true),
                    'Aut_Telefono' => $this->input->post("tel", true),
                    'Aut_Mail' => $this->input->post("email", true),
                );
                $guardar = $this->autorizados_model->modificarAutorizados($data, $usr);

                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha editado el registro exitosamente.');
                    redirect(base_url() . 'autorizados', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'autorizados/edit' . $usr, 301);
                }
            }
        }
        $datos = $this->autorizados_model->getAutId($usr);
        if (sizeof($datos) == 0) {
            show_404();
        }

        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('autorizados/edit', compact('usr', 'datos'), TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

}
