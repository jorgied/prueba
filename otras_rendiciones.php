<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Otras_rendiciones extends MY_Controller {
    
    public function index(){
        if($this->caja_abierta()){}  
        $data['cajas'] = $this->Cajas_model->listar_cajas();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otras_rendiciones/cobro', $data, TRUE);
  
        $this->load->view('templates',$datoPrincipal);
    } 
    
    public function cobrar(){
        if($this->caja_abierta()){}        
       
		$cajas = $this->Cajas_model->listar_cajas();
                
                $band=true;
                foreach ($cajas as $caja){
                    $id= $caja->Caj_Id;
                    
                    $this->form_validation->set_rules("$id", 'Monto', 'required|trim|xss_clean');         
                                       
                    if ($this->form_validation->run() == FALSE)	{
                        $band=false;
                        $data['cajas'] = $this->Cajas_model->listar_cajas();
                        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otras_rendiciones/cobro', $data, TRUE);
                    }else {  
                        $razonsocial=$caja->Usr_Apenom;
                        $monto[$id] = $this->input->post("$id");
                    }
                }
                if ($band==true){
                        $data['monto']=$monto;
                        
                        $data['cajas']=$cajas;
                        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otras_rendiciones/confirmacion', $data, TRUE);
                    }
            $this->load->view('templates',$datoPrincipal);        
    }
    
    public function registrar(){
                		
                $caj_id=$this->session->userdata('caja_id');
                
                $now= now();
                $hoy=  unix_to_human($now);
                $fecha=date('d-m-Y',strtotime($hoy));
                
                $tipo_desc='Otras Rendiciones';
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
                 
                $cajas= $this->Cajas_model->listar_cajas();
				$fecha = date('Y-m-d',strtotime($fecha));
                foreach ($cajas as $caja){
                    $id= $caja->Caj_Id;
                    $razonsocial=$caja->Usr_Apenom;
                    $monto[$id] = $this->input->post("monto_$id");
                    $this->RendicionesCaja_model->update_ingreso($caj_id,$monto[$id]);
                    $mov_id=$this->MovimientosCaja_model->insertMovimientoCaja($caj_id,$tm,$monto[$id],'Rendicion de Caja',$fecha,'Contado','TRUE',$sec,$dir,$gru,$cur,$dic,$razonsocial);
                    
					$query2 = $this->db->query("SELECT * FROM MovimientosCaja 
                WHERE Caj_Id='$caj_id'");     
           			 foreach ($query2->result_array() as $row) 
                        {  $mov_id = $row['Mov_Id'];}
                    $this->MovimientosCaja_model->insert_comprobante('RENDICION CAJA',$id,$caj_id,$mov_id);
                    //marcar como caja rendida
                    $this->Cajas_model->caja_rendida($id);
                    $mensaje=TRUE;
                    if (! $mov_id){
                        $mensaje=FALSE;
                    }
                }
                if($mensaje){
                    $mensaje='<div class="success">Exito!</div>';
                }else{
                    $mensaje='Error, no guardado.';
                }
                $data['cajas']=$cajas;
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otras_rendiciones/cobro', $data, TRUE);

                if ($mensaje <> NULL){
                    $data['mensaje']=$mensaje;
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otras_rendiciones/success', $data, TRUE);
                }    
            $this->load->view('templates',$datoPrincipal);
        }
        }
?>