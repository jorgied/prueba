<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rendiciones extends MY_Controller {
    
    public function index(){
        
        $now= now();
        $fecha=  unix_to_human($now);
        $hoy=date('d-m-Y',strtotime($fecha));
        $user = $this->session->userdata('user_id');
	$rendiciones = $this->RendicionesCaja_model->last($user);
        $data['hoy']= $hoy;
        //$user = $this->session->userdata('user_id');
	//$rendiciones = $this->RendicionesCaja_model->list_all($user);
	if(! $rendiciones){
            $data['table']='<p>No se efecturaron Rendiciones.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Caja Nro.','Fecha de Apertura','Monto Apertura','Total Ingresos','Total Egresos','TOTAL','Acciones');
	
	foreach ($rendiciones as $rendicion)
	{       $total= $rendicion->Caj_MontoApertura + $rendicion->Caj_TotalIngreso - $rendicion->Caj_TotalEgreso;
		$this->table->add_row($rendicion->Caj_Id,date('d-m-Y h:m',strtotime($rendicion->Caj_FechaHoraApertura)),
                              "$ $rendicion->Caj_MontoApertura",
                              "$ $rendicion->Caj_TotalIngreso",
                              "$ $rendicion->Caj_TotalEgreso",
                              "$ $total",
                              anchor('rendiciones/ver/'.$rendicion->Caj_Id,'Ver',array('class'=>'view'))
			);
	}
	$data['table'] = $this->table->generate();
        $data['hoy']= $hoy;
        }
        		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('rendicionesDiarias/ver', $data, TRUE);
        
        if ($this->caja_abierta_boolean()){		
        
        $this->load->view('templates',$datoPrincipal);}
        else{
        $this->load->view('templates_caja_cerrada',$datoPrincipal);
        }
    }
    
    function buscar(){
       
        $now= now();
        $fecha=  unix_to_human($now);
        $hoy=date('d-m-Y',strtotime($fecha));
        $user = $this->session->userdata('user_id');
	$rendiciones = $this->RendicionesCaja_model->last($user);
        $data['hoy']= $hoy;
        $desde = $this->input->post('desde');
        $hasta = $this->input->post('hasta');
        $user = $this->session->userdata('user_id');
	$rendiciones = $this->RendicionesCaja_model->buscar_por_fecha($user,$desde,$hasta);
        
        //$user = $this->session->userdata('user_id');
	//$rendiciones = $this->RendicionesCaja_model->list_all($user);
	if(! $rendiciones){
            $data['table']='<p>No se efecturaron Rendiciones.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Fecha','Apertura','Ingresos','Egresos','TOTAL','Acciones');
	$i = 0;
	foreach ($rendiciones as $rendicion)
	{       $total= $rendicion->Caj_MontoApertura + $rendicion->Caj_TotalIngreso - $rendicion->Caj_TotalEgreso;
		$this->table->add_row(++$i,$rendicion->Caj_FechaHoraApertura,
                              "$ $rendicion->Caj_MontoApertura",
                              "$ $rendicion->Caj_TotalIngreso",
                              "$ $rendicion->Caj_TotalEgreso",
                              "$ $total",
                              anchor('rendiciones/ver/'.$rendicion->Caj_Id,'Ver',array('class'=>'view'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('rendicionesDiarias/ver', $data, TRUE);
        
        if ($this->caja_abierta_boolean()){		
        
        $this->load->view('templates',$datoPrincipal);}
        else{
        $this->load->view('templates_caja_cerrada',$datoPrincipal);
        }
    }


    function ver($caja_id){
        $i = 0;
        $estampillas = $this->RendicionesCaja_model->estampillas($caja_id);
        
        $reintegros= $this->RendicionesCaja_model->reintregros($caja_id);
        $rendiciones = $this->RendicionesCaja_model->ver($caja_id);
        $data['caja_id']=$caja_id;
        
       $cajas=$this->MovimientosCaja_model->get_caja($caja_id);
        foreach ($cajas as $caja){
            $fechaApertura=date('d-m-Y h:m',strtotime($caja->Caj_FechaHoraApertura));
            if($caja->Caj_FechaHoraCierre){
            $fechaCierre=date('d-m-Y h:m',strtotime($caja->Caj_FechaHoraCierre));
            }else{
              $fechaCierre='';  
            }
            $puestos=$this->MovimientosCaja_model->nombre_puesto($caja->Pue_Id);
            foreach($puestos as $puesto){
                $pue= $puesto->Pue_Ubicacion;
                
            }
            $this->table->add_row(
                    "<b>Caja Nro: </b>$caja->Caj_Id",                
                    "<b>Puesto: </b>$pue"
                            );
             $this->table->add_row(
                                    "<b>Fecha y hora de Apertura: </b>$fechaApertura",
                                    "<b>Monto de Apertura: </b>$ $caja->Caj_MontoApertura"
                            );
             $this->table->add_row(
                                    "<b>Fecha y hora de Cierre: </b> $fechaCierre",
                                    "<b>Monto de Cierre: </b>$ $caja->Caj_MontoCierre"
                            );
            $data['caja'] = $this->table->generate(); 
        }
        
        if (!$estampillas and !$rendiciones and !$reintegros){
            $data['table']='<p>No se efecturaron Movimientos.</p>';
        }else{
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");
            $this->table->set_heading(' ','Tipo y NºComprobante','Razón Social','Descripcion','Ingresos','Egresos');

            if($estampillas){
                    $est_monto=0;
                    foreach ($estampillas as $estampilla){
                        $est_monto=$est_monto + $estampilla->Mov_Mono;
                    }

                    $this->table->add_row(++$i,"Estampillas",
                                        $estampilla->Mov_RazonSocial,$estampilla->Mov_Descripcion,
                                        "$ $est_monto","$ 0"
                            );
            }
            if($reintegros){
                foreach ($reintegros as $reintegro){
                    $this->table->add_row(++$i,"Reintegro de Anticipo.",
                                    $reintegro->Mov_RazonSocial,$reintegro->Mov_Descripcion,
                                    "$ $reintegro->Mov_Mono","$ 0"
                            );
                }
            
            }
            if($rendiciones){
                foreach ($rendiciones as $rendicion)
                {       
                        //Tipo Comprobante 1 = RECIBO
                        //Tipo Comprobante 2 = FACTURA
                        //Tipo Comprobante 3 = VALE
                        //Tipo Comprobante 4 = OTRO
                        switch ($rendicion->Tipo_Comprobante){
                            case 1:$tipo_comp='RBO.';
                            break;
                            case 2:$tipo_comp='FACT';
                            break;
                            case 3:$tipo_comp='VALE';
                            break;
                            case 6:$tipo_comp='RENDICION CAJA';
                            break;
                            default:$tipo_comp='OTRO';
                    }
                    if($rendicion->Mov_IngresoEgreso==0){
                            $ingreso=$rendicion->Mov_Mono;
                            $egreso=0.0;
                    }else{
                            $ingreso=0.0;
                            $egreso=$rendicion->Mov_Mono;
                    }
                    $this->table->add_row(++$i,"$tipo_comp Nº $rendicion->Comp_Nro_Externo",
                                    $rendicion->Mov_RazonSocial,$rendicion->Mov_Descripcion,

                                    "$ $ingreso","$ $egreso"
                                );
                }
            }
            $data['table'] = $this->table->generate();
        }
                		
        
        if($this->caja_abierta()){
            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('rendicionesDiarias/movimientos', $data, TRUE);
            $this->load->view('templates',$datoPrincipal);
        }else{
            $datoPrincipal ['contenido'] = $this->load->view('rendicionesDiarias/movimientos', $data, TRUE);
           $this->load->view('templates_caja_cerrada_1',$datoPrincipal); 
        }
    }
    
    function anular_mov($mov_id){
        
    }
    
    function imprimir($caja_id){
        $this->load->library('cezpdf');
		$this->load->helper('pdf');
		
                $this->cezpdf->ezText("RENDICION DE CAJA $caja_id", 12, array('justification' => 'center'));
		$this->cezpdf->ezSetDy(-10);
        $cajas=$this->MovimientosCaja_model->get_caja($caja_id);
        foreach($cajas as $caja){
            $apenom=$caja->Usr_Apenom;
            $montoA=$caja->Caj_MontoApertura;
            $montoC=$caja->Caj_MontoCierre;
            $fechaApertura=date('d-m-Y h:m',strtotime($caja->Caj_FechaHoraApertura));
            if($caja->Caj_FechaHoraCierre){
            $fechaCierre=date('d-m-Y h:m',strtotime($caja->Caj_FechaHoraCierre));
            }else{
              $fechaCierre='';  
            }
            $puestos=$this->MovimientosCaja_model->nombre_puesto($caja->Pue_Id);
            foreach($puestos as $puesto){
                $pue= $puesto->Pue_Ubicacion;
                
            }
            
        }
   
  $dbdata[] = array('a' => "Fecha de apertura: $fechaApertura", 
                     'b' => "Monto de Apertura: $ $montoA" 
                    );
  $dbdata[] = array('a' => "Fecha de cierre: $fechaCierre", 
                     'b' => "Monto de Cierre: $ $montoC" 
                    );
 $colnames = array(
                        'a' => "Cajero: $apenom" ,
			'b' => "Puesto: $pue"
			
		);
		
		$this->cezpdf->ezTable($dbdata, $colnames, ' ', array('width'=>550));      
$content = 
"

DETALLE DE MOVIMIENTOS";                     

		$this->cezpdf->ezText($content, 10, array('justification' => 'left'));
                $this->cezpdf->ezSetDy(-10);     
        $i = 0;
        $estampillas = $this->RendicionesCaja_model->estampillas($caja_id);
        $reintegros= $this->RendicionesCaja_model->reintregros($caja_id);
        $rendiciones = $this->RendicionesCaja_model->ver($caja_id);
        if (!$estampillas and !$rendiciones and !$reintegros){
           $content = 
"No se efectuaron movimientos.";

            $this->cezpdf->ezText($content, 10, array('justification' => 'left'));
            $this->cezpdf->ezSetDy(-10);
                
//agregar mostrar observaciones
        }else{
            $totalIngresos=0;
            $totalEgresos=0;
            $total=0;
            if($estampillas){
                $est_monto=0;
                foreach ($estampillas as $estampilla){
                    $est_monto=$est_monto + $estampilla->Mov_Mono;
                }
                $db_data[] = array('i' => ++$i, 
                                        'tipoynro' => 'Estampillas', 
                                        'razonsocial' => 'Estampillas',
                                        'descripcion' => 'Estampillas',
                                        'ingreso' => "$ $est_monto",
                                        'egreso' => '$ 0');
                $totalIngresos=$est_monto;
            }
            if($reintegros){
                foreach ($reintegros as $reintegro){
                    
                    $db_data[] = array('i' => ++$i, 
                                        'tipoynro' => "Reintegro de Anticipo.",
                                        'razonsocial' => $reintegro->Mov_RazonSocial,
                                        'descripcion' => $reintegro->Mov_Descripcion,
                                        'ingreso' => "$ $reintegro->Mov_Mono",
                                        'egreso' => '$ 0');
                    $totalIngresos=$reintegro->Mov_Mono;
                }
            
            }
            if($rendiciones){
                foreach ($rendiciones as $rendicion)
                {       
                        //Tipo Comprobante 1 = RECIBO
                        //Tipo Comprobante 2 = FACTURA
                        //Tipo Comprobante 3 = VALE
                        //Tipo Comprobante 4 = OTRO
                        switch ($rendicion->Tipo_Comprobante){
                            case 1:$tipo_comp='RBO.';
                            break;
                            case 2:$tipo_comp='FACT';
                            break;
                            case 3:$tipo_comp='VALE';
                            break;
                            case 6:$tipo_comp='RENDICION CAJA';
                            break;
                            default:$tipo_comp='OTRO';
                        }
                        if($rendicion->Mov_IngresoEgreso==0){
                            $ingreso=$rendicion->Mov_Mono;
                            $egreso=0.0;
                            $totalIngresos= $totalIngresos + $ingreso;
                        }else{
                            $ingreso=0.0;
                            $egreso=$rendicion->Mov_Mono;
                            $totalEgresos= $totalEgresos + $egreso;
                        }
                        $db_data[] = array('i' => ++$i, 
                                        'tipoynro' => "$tipo_comp Nro $rendicion->Comp_Nro_Externo", 
                                        'razonsocial' => $rendicion->Mov_RazonSocial,
                                        'descripcion' => $rendicion->Mov_Descripcion,
                                        'ingreso' => "$ $ingreso",
                                        'egreso' => "$ $egreso");

                }
            }
            $db_data[] = array('i' => '', 
                                        'tipoynro' => " ", 
                                        'razonsocial' => ' ',
                                        'descripcion' => 'TOTALES',
                                        'ingreso' => "$ $totalIngresos",
                                        'egreso' => "$ $totalEgresos");
        $this->load->library('cezpdf');
	$this->load->helper('pdf');
		
		prep_pdf(); // creates the footer for the document we are creating.

		
				
		$col_names = array(
                        'i' => '',
			'tipoynro' => 'Tipo y Nro Comprobante',
			'razonsocial' => 'Razon Social',
			'descripcion' => 'Descripcion',
                        'ingreso' => 'Ingresos',
                        'egreso' => 'Egresos',
		);
		
		$this->cezpdf->ezTable($db_data, $col_names, ' ', array('width'=>550));
                if($montoC != ''){
                $total = $montoA + $totalIngresos - $totalEgresos;
                if($total < $montoC){
                    $saldo=$total-$montoC;
                    $content = 
"
                    
                    
*OBSERVACIONES
 SOBRANTE DE CAJA $ $saldo --";

            $this->cezpdf->ezText($content, 10, array('justification' => 'left'));
            $this->cezpdf->ezSetDy(-10);
        }elseif($total > $montoC){
            $saldo=$montoC-$total;
                    $content = 
"
            
            
*OBSERVACIONES
 FALTANTE DE CAJA $ $saldo --";

            $this->cezpdf->ezText($content, 10, array('justification' => 'left'));
            $this->cezpdf->ezSetDy(-10);
        }
        }
        }
		$this->cezpdf->ezStream();
                
    
    

    
    
}
    }   
?>