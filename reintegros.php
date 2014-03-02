<?php

class reintegros extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
    }
    
   public function index(){
        if($this->caja_abierta()){};
        $anticipos = $this->Reintegros_model->buscar_anticipo('%');
	if(! $anticipos){
            $data['table']='<p style="text-align: left">No se encontraron anticipos no rendidos.</p>';
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Apellido y Nombre','DNI','Fecha','Importe','Acciones');
	$i = 0;
	foreach ($anticipos as $anticipo)
	{
		$this->table->add_row(++$i, $anticipo->Aut_Apenom,
                              $anticipo->Aut_DNI,
                              date('d-m-Y',strtotime($anticipo->Mov_FechaHora)),
                              $anticipo->Mov_Mono,
                              anchor('reintegros/cobrar/'.$anticipo->APG_Id,'Registrar reintegro',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        } 
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('reintegros/reintegros', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('autorizado');
        $anticipos = $this->Reintegros_model->buscar_anticipo($query);
	if(! $anticipos){
            $data['table']="<p style='text-align: left'>No se encontraron a $autorizado con anticipos no rendidos.</p>";
        }else{	
	$this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Apellido y Nombre','DNI','Fecha','Importe','Acciones');
	$i = 0;
	foreach ($anticipos as $anticipo)
	{
		$this->table->add_row(++$i, $anticipo->Aut_Apenom,
                              $anticipo->Aut_DNI,
                              date('d-m-Y',strtotime($anticipo->Mov_FechaHora)),
                              $anticipo->Mov_Mono,
                              anchor('reintegros/cobrar/'.$anticipo->APG_Id,'Registrar reintegro',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        } 
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('reintegros/reintegros', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar($apg_id){
        
                if($this->caja_abierta()){};
                
                $data['id']=$apg_id;
                $now= now();
                $fecha=  unix_to_human($now);
                $hoy=date('d-m-Y',strtotime($fecha));
                $data['hoy']= $hoy;
                
                $anticipos= $this->Reintegros_model->buscar($apg_id);
                foreach ($anticipos as $anticipo)
                {
                    $data['nombre']=$anticipo->Aut_Apenom;
                    $data['dni']=$anticipo->Aut_DNI;
                    $data['desc']=$anticipo->Mov_Descripcion;
                    $data['monto']=$anticipo->Mov_Mono;
                    $data['fecha']=date('d-m-Y',strtotime($anticipo->Mov_FechaHora));
                    
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('reintegros/confirmacion', $data, TRUE);
                }   
                    $this->load->view('templates',$datoPrincipal);
                
    }
    
public function registrar($apg_id){
                if($this->caja_abierta()){};
                
                $data['id']=$apg_id;
    
                $tipo_desc='Reintegros de anticipos';
                $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
             	
				 $query2 = $this->db->query("SELECT * FROM grupos WHERE  grupos.Gru_Descripcion = 'COOPERADORA'");     			
		 	 foreach ($query2->result_array() as $row) 
                        {		    $sec = $row['Sec_Id'];
						 			$dir = $row['Dir_Id'];
						 			$gru = $row['Gru_Id'];
					    }
				$cur = 0;
				$dic = 0;
                $nombre = $this->input->post('nombre');
                $dni = $this->input->post('dni');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
                $fecha = $this->input->post('fecha');
                
                //cambiar caj_id cuando tengamos sesiones
                
                
                $caj_id=$this->session->userdata('caja_id');
				$fecha = date('Y-m-d',strtotime($fecha));
                $mov_id=$this->MovimientosCaja_model->insertMovimientoCaja($caj_id,$tm,$monto,'Reintegro de anticipos',$fecha,'Contado','TRUE',$sec,$dir,$gru,$cur,$dic,$nombre);
                
                /*$tipo_comp='RECIBO';
                $this->MovimientosCaja_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);*/
                $this->RendicionesCaja_model->update_ingreso($caj_id,$monto);
                $query2 = $this->db->query("SELECT * FROM MovimientosCaja 
                WHERE Caj_Id='$caj_id'");     
           			 foreach ($query2->result_array() as $row) 
                        {  $mov_id = $row['Mov_Id'];}
				
                $anticipo = $this->Reintegros_model->update($apg_id,$caj_id,$mov_id);
                //Armo la vista
                if ($anticipo==FALSE){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message'] = '<div class="success">Exito!</div>';
                
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('reintegros/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
 
}

?>