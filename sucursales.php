<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sucursales extends MY_Admin {
    public function index($banco_seleccionado = null) {
                if (isset($banco_seleccionado)){$datos['banco_seleccionado']=$banco_seleccionado ;}
                
                $datos ['subtitulo']='Sucursales';
                $datos ['bancos']=$this->bancos_model->bancos_array();
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('sucursales/sucursales', $datos, TRUE);
                $this->load->view('templates_admin',$datoPrincipal);
    }
    
    function listado($banco_id){
        $datos ['subtitulo']='Sucursales';
        if (isset($banco_id)) { $datos['banco_seleccionado']=$banco_id;};
        $datos ['bancos']=$this->bancos_model->bancos_array();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('sucursales/sucursales', $datos, TRUE);
        $this->load->view('templates_admin',$datoPrincipal);
    }
    
    function sucursales_json($banco_id){
        $sucursales=$this->cuentas_model->obtener_sucursales_completo($banco_id);
        $this->output->set_header("Content-Type: text/json charset=UTF-8\r\n");
        echo json_encode($sucursales); 
    }
    
    function agregar(){
        $this->form_validation->set_rules('suc_nombre','suc_nombre','required|trim|max_length[30]|xss_clean');
        $this->form_validation->set_rules('suc_direccion','suc_direccion','required|trim|max_length[30]|xss_clean');
        $this->form_validation->set_rules('suc_telefono','suc_telefono','required|trim|max_length[15]|xss_clean|numeric');
            
        if ($this->form_validation->run() == FALSE){
            $datos ['subtitulo']='Agregar Sucursal';
            $datos ['bancos']=$this->bancos_model->bancos_array();
            
            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('sucursales/sucursales_agregar', $datos, TRUE);
            $this->load->view('templates_admin',$datoPrincipal);
            } else {
                    extract($_POST);
                    $this->cuentas_model->crear_sucursal($banco,$suc_nombre,$suc_direccion,$suc_telefono);     
                    redirect('sucursales/listado/'.$banco);
                    }
    }

    public function editar($sucursal_id){
        $this->form_validation->set_rules('suc_nombre','suc_nombre','required|trim|max_length[30]|xss_clean');
        $this->form_validation->set_rules('suc_direccion','suc_direccion','required|trim|max_length[30]|xss_clean');
        $this->form_validation->set_rules('suc_telefono','suc_telefono','required|trim|max_length[15]|xss_clean|numeric');
        $sucursal_datos =$this->cuentas_model->obtener_sucursal($sucursal_id);
        $datos['sucursal_datos']=$sucursal_datos;
        if ($this->form_validation->run() == FALSE){
            $datos ['subtitulo']='Editar datos de Sucursal '.$datos['sucursal_datos']['Suc_Nombre'];
            $datos ['sucursal_id']=$sucursal_id;
            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('sucursales/sucursales_editar', $datos, TRUE);
            $this->load->view('templates_admin',$datoPrincipal);
            } else {
                    extract($_POST);
                    $this->cuentas_model->editar_sucursal($sucursal_id,$suc_nombre,$suc_direccion,$suc_telefono);
                    redirect('sucursales/listado/'.$sucursal_datos['Banco_Id']);
                    }    
    }

}