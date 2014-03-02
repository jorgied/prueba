<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Proveedores extends MY_Controller {

//    public function index() {
//        $datos = $this->proveedores_model->getProveedores();
//        $this->load->view('proveedores/proveedores', compact('datos'));
//    }
    public function index() {
     
        $datos = $this->proveedores_model->getProveedores();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('proveedores/proveedores', compact('datos'), TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

    public function add() {
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/proveedores")) {
                $data = array
                    (
                    'Prov_Id' => NULL,
                    'Prov_RazonSocial' => $this->input->post("nombre", true),
                    'Prov_CUIT' => $this->input->post("cuit", true),
                    'Prov_Telefono' => $this->input->post("tel", true),
                    'Prov_Direccion' => $this->input->post("dir", true),
                    'Prov_Email' => $this->input->post("email", true),
                );
                $guardar = $this->proveedores_model->insertarProveedores($data);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha agregado el registro exitosamente.');
                    redirect(base_url() . 'proveedores', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'proveedores/add', 301);
                }
            }
        }
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('proveedores/add','', TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

    public function edit($usr = null) {
        if (!$usr) {
            show_404();
        }
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/proveedores")) {
                $data = array
                    (
                    'Prov_Id' => $this->input->post("usr", true),
                    'Prov_RazonSocial' => $this->input->post("nombre", true),
                    'Prov_CUIT' => $this->input->post("cuit", true),
                    'Prov_Telefono' => $this->input->post("tel", true),
                    'Prov_Direccion' => $this->input->post("dir", true),
                    'Prov_Email' => $this->input->post("email", true),
                );
                $guardar = $this->proveedores_model->modificarProveedores($data, $usr);

                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha editado el registro exitosamente.');
                    redirect(base_url() . 'proveedores', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'proveedores/edit' . $usr, 301);
                }
            }
        }
        $datos = $this->proveedores_model->getProvId($usr);
        if (sizeof($datos) == 0) {
            show_404();
        }

        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('proveedores/edit', compact('usr', 'datos'), TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }

}
