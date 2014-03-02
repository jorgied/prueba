<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Estampillas extends MY_Controller {
    
    public function index(){
        if($this->caja_abierta()){}
        
        //$this->_set_rules();
	$data['estampillas'] = $this->Estampillas_model->listar_por_valores();
			
	$datoPrincipal ['contenidoPrincipal'] = $this->load->view('estampillas/cobro_estampillas', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar(){
        if($this->caja_abierta()){}        
       
		$estampillas = $this->Estampillas_model->listar_por_valores();
                
                $band=true;
                foreach ($estampillas as $estampilla){
                    $id= $estampilla->Est_Id;
                    $stock=$estampilla->Est_Stock;
                    $st=$stock+1;
                    $this->form_validation->set_rules("cant_$id", 'Cantidad', "less_than[$st]");         
                   
                    if ($this->form_validation->run() == FALSE)	{
                        $band=false;
                        $data['estampillas'] = $this->Estampillas_model->listar_por_valores();
			$datoPrincipal ['contenidoPrincipal'] = $this->load->view('estampillas/cobro_estampillas', $data, TRUE);
                      }else {  
                        $cant[$id] = $this->input->post("cant_$id");
                        $stock_actual[$id]= $stock - $cant[$id];
                      }
                }
                if ($band==true){
                        $data['cant']=$cant;
                        $data['stock_actual']=$stock_actual;
                        $data['estampillas']=$estampillas;
                        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('estampillas/confirmacion', $data, TRUE);
                    }
            $this->load->view('templates',$datoPrincipal);        
    }
    
    function registrar(){
        if($this->caja_abierta()){}
        $monto= $this->input->post("total");
        $tipo_desc='Estampillas';
                $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
        $sec_desc='ESTAMPILLAS';
        $centros=  $this->MovimientosCaja_model->centro_costo_sec($sec_desc);
        foreach ($centros as $centro){
                    $sec=$centro->Sec_Id;
                    $dir=$centro->Dir_Id;
                    $gru=$centro->Gru_Id;
                    $cur=$centro->Cur_Id;
                    $Dic=$centro->Dic_Id;
        }
        
        $caj_id=$this->session->userdata('caja_id');
        $now= now();
        $hoy=  unix_to_human($now);
        $fecha=date('d-m-Y',strtotime($hoy));
        $mov_id=$this->MovimientosCaja_model->insert($caj_id,$tm,$monto,'Estampillas',$fecha,'Contado','TRUE',$sec,$dir,$gru,$cur,$Dic,'Estampillas');
                
        $movimientos=$this->MovimientosCaja_model->get_id($caj_id,$hoy);
        foreach ($movimientos as $movimiento){
            $mov_id=$movimiento->Mov_Id;
        }
        $this->RendicionesCaja_model->update_ingreso($caj_id,$monto);
       
        $estampillas = $this->Estampillas_model->listar_por_valores();
        
        foreach ($estampillas as $estampilla){
            $id= $estampilla->Est_Id;
            //$stock=$estampilla->Est_Stock;
            $cant[$id] = $this->input->post("cant_$id");
            $venta = $this->Estampillas_model->insert($id,$cant[$id],$caj_id,$mov_id);
            $mensaje=TRUE;
            if ($venta==TRUE){
                $this->Estampillas_model->update_stock($id,$cant[$id]);
                
                
            }else{
                $mensaje=FALSE;
            }
        }
        if($mensaje){
            $mensaje='<div class="success">Exito!</div>';
        }else{
            $mensaje='Error, no guardado.';
        }
        $data['estampillas']=$estampillas;
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('estampillas/cobro_estampillas', $data, TRUE);
                    
        if ($mensaje <> NULL){
            $data['mensaje']=$mensaje;
            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('estampillas/success', $data, TRUE);
        }    
    $this->load->view('templates',$datoPrincipal);
}
}
?>
