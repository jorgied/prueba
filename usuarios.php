<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usuarios extends MY_Controller {

    public function index() {
//        $datos = $this->usuarios_model->getUsuarios();
//        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('usuarios/usuarios', compact('datos'), TRUE);
//        $this->load->view('templates_admin',$datoPrincipal);

        if ($this->uri->segment(3)) {
            $pagina = $this->uri->segment(3);
        } else {
            $pagina = 0;
        }
        $porpagina = 20;
        $datos = $this->usuarios_model->getUsuariosPagination($pagina, $porpagina, "limit");
        $cuantos = $this->usuarios_model->getUsuariosPagination($pagina, $porpagina, "cuantos");
        $config['base_url'] = base_url() . 'usuarios/index';
        $config['total_rows'] = $cuantos;
        $config['per_page'] = $porpagina;
        $config['uri_segment'] = '3';
        $config['num_links'] = '4';
        $config['first_link'] = 'Primero';
        $config['next_link'] = 'Siguiente';
        $config['prev_link'] = 'Anterior';
        $config['last_link'] = 'Ultimo';
        $this->pagination->initialize($config);

        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('usuarios/usuarios', compact("datos", "cuantos"), TRUE);
        $this->load->view('templates_admin', $datoPrincipal);
    }

    public function add() {
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/usuarios")) {
                $data = array
                    (
                    'Usr_Login' => $this->input->post("usu", true),
                    'Usr_Clave' => sha1($this->input->post("clave", true)),
                    'Usr_Perfil' => $this->input->post("perfil", true),
                    'Usr_DNI' => $this->input->post("dni", true),
                    'Usr_Apenom' => $this->input->post("nombre", true),
                    'Usr_Direccion' => $this->input->post("dir", true),
                    'Usr_Telefono' => $this->input->post("tel", true),
                    'Usr_Email' => $this->input->post("email", true),
                );
                $guardar = $this->usuarios_model->insertarUsuario($data);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha agregado el registro exitosamente.');
                    redirect(base_url() . 'usuarios', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'usuarios/add', 301);
                }
            }
        }
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('usuarios/add', '', TRUE);
        $this->load->view('templates_admin', $datoPrincipal);
    }

    public function edit($usr = null) {
        if (!$usr) {
            show_404();
        }
        if ($this->input->post()) {
            if ($this->form_validation->run("validar/usuarios")) {
                $data = array
                    (
                    'Usr_Login' => $this->input->post("usu", true),
                    'Usr_Clave' => sha1($this->input->post("clave", true)),
                    'Usr_Perfil' => $this->input->post("perfil", true),
                    'Usr_DNI' => $this->input->post("dni", true),
                    'Usr_Apenom' => $this->input->post("nombre", true),
                    'Usr_Direccion' => $this->input->post("dir", true),
                    'Usr_Telefono' => $this->input->post("tel", true),
                    'Usr_Email' => $this->input->post("email", true),
                );
                $guardar = $this->usuarios_model->modificarUsuario($data, $usr);
                if ($guardar) {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha editado el registro exitosamente.');
                    redirect(base_url() . 'usuarios', 301);
                } else {
                    $this->session->set_flashdata('ControllerMessage', 'Se ha producido un error. Inténtelo nuevamente por favor.');
                    redirect(base_url() . 'usuarios/edit' . $usr, 301);
                }
            }
        }
        $datos = $this->usuarios_model->getUsrId($usr);
        if (sizeof($datos) == 0) {
            show_404();
        }


        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('usuarios/edit', compact('usr', 'datos'), TRUE);
        $this->load->view('templates_admin', $datoPrincipal);
    }

}
