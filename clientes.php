<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clientes extends MY_Controller {
    
   
    public function ver($cli_id){
        if($this->caja_abierta()){}
        $data ['titulo']= 'SysCoop';
        $data['subtitulo']='Cliente';
        
        $this->load->model('Clientes_model');
        $data['clientes']= $this->Clientes_model->get_by_id($cli_id);
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('clientes/ver_cliente', $data, TRUE);
        $this->load->view('templates',$datoPrincipal); 
        
    }
    public function nuevo(){
        if($this->caja_abierta()){}
        $data ['titulo']= 'SysCoop';
        $data['subtitulo']='Nuevo Cliente';
        
        $this->form_validation->set_rules('nom', 'Apellido y Nombre', 'required');
	$this->form_validation->set_rules('cuil', 'CUIL', 'required');
	$this->form_validation->set_rules('dni', 'DNI', 'required');
        $this->form_validation->set_rules('dir', 'Domicilio', 'required');
	$this->form_validation->set_rules('email', 'e-mail', 'required');
        $this->form_validation->set_rules('empresa', 'Empresa', 'required');
        $this->form_validation->set_rules('tel', 'Teléfono', 'required');
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('clientes/nuevo_cliente', $data, TRUE);
        $this->load->view('templates',$datoPrincipal); 
    }
    
    public function registrar(){
        if($this->caja_abierta()){}
        $data ['titulo']= 'SysCoop';
        $data['subtitulo']='Cliente';
        
        $this->form_validation->set_rules('nom', 'Apellido y Nombre', 'required');
	$this->form_validation->set_rules('cuil', 'CUIL', 'required');
	$this->form_validation->set_rules('dni', 'DNI', 'required');
        $this->form_validation->set_rules('dir', 'Domicilio', 'required');
	$this->form_validation->set_rules('email', 'e-mail', 'required');
        $this->form_validation->set_rules('empresa', 'Empresa', 'required');
        $this->form_validation->set_rules('tel', 'Teléfono', 'required');

	if ($this->form_validation->run() == FALSE)
	{
		$datoPrincipal ['contenidoPrincipal'] = $this->load->view('clientes/nuevo_cliente', $data, TRUE);
	}
	else
	{
		$nom = $this->input->post('nom');
                $cuil = $this->input->post('cuil');
                $dni = $this->input->post('dni');
                $dir = $this->input->post('dir');
                $email = $this->input->post('email');
                $empresa = $this->input->post('empresa');
                $tel = $this->input->post('tel');

                $this->load->model('Clientes_model');
                $cliente= $this->Clientes_model->nuevo_cliente($nom,$cuil,$dni,$dir,$email,$empresa,$tel);
                if ($radio=null){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message']='Exito.';
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('success', $data, TRUE);
        }
        $this->load->view('templates',$datoPrincipal); 
    }
    public function editar($cli_id){
       if($this->caja_abierta()){} 
        // set common properties
	$data['titulo'] = 'SysCoop';
	$data['subtitulo'] = 'Editar Cliente';
	//$data['action'] = site_url('index/editar_cliente');
        //$data['cancelar'] = site_url('index/');
	//$data['link_back'] = anchor('person/index/','Back to list of persons',array('class'=>'back'));
        
        // set validation properties
	//$this->_set_rules();
		
	// prefill form values
        $this->load->model('Clientes_model');
	$clientes = $this->Clientes_model->get_by_id($cli_id);
        foreach ($clientes as $cliente){
            $this->form_data->nom = $cliente->Cli_ApeNom;
            $this->form_data->dni = $cliente->Cli_DNI;
            $this->form_data->cuil= $cliente->Cli_CUIL;
            $this->form_data->dir = $cliente->Cli_Direccion;
            $this->form_data->tel = $cliente->Cli_Telefono;
            $this->form_data->email = $cliente->Cli_Email;
            $this->form_data->empresa = $cliente->Cli_NomEmpresa;
            $data['cliente']=$cliente;
        }
	$datoPrincipal ['contenidoPrincipal'] = $this->load->view('clientes/editar_cliente', $data, TRUE);
        $this->load->view('templates',$datoPrincipal); 
    }
    public function editar_($cli_id){
        if($this->caja_abierta()){}
        $data['titulo'] = 'SysCoop';
	$data['subtitulo'] = 'Editar Cliente';
        $nom = $this->input->post('nom');
        $cuil = $this->input->post('cuil');
        $dni = $this->input->post('dni');
        $dir = $this->input->post('dir');
        $email = $this->input->post('email');
        $empresa = $this->input->post('empresa');
        $tel = $this->input->post('tel');
                
        $this->load->model('Clientes_model');
	$cliente = $this->Clientes_model->editar_cliente($cli_id,$nom,$cuil,$dni,$dir,$email,$empresa,$tel);
        if ($cliente=null){
            $data['message']='Error, no guardado.';
         }else{
             $data['message']='Exito.';
         }
		                
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('success', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);        
    }
           


    
}

?>
