<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Anticipos extends MY_Controller {
     public function __construct() {
        parent::__construct();
		$this->load->model('Anticipos_model','',TRUE);
    }																																																																												
    public function index(){
        if($this->caja_abierta()){  
        $autorizados = $this->Autorizados_model->buscar_autorizado('%');
	if(! $autorizados){
            $data['table']='<p>No encontrado.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Apellido y Nombre','DNI','Acciones');
	$i = 0;
	foreach ($autorizados as $autorizado)
	{
		$this->table->add_row($autorizado->Aut_Apenom,
                              $autorizado->Aut_DNI,
                              anchor('anticipos/pagar/'.$autorizado->Aut_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('anticipos/anticipos', $data, TRUE);
  
        $this->load->view('templates',$datoPrincipal);
    } }
    
    public function buscar(){
        if($this->caja_abierta()){   
	$query = $this->input->post('autorizado');
        
	$autorizados = $this->Autorizados_model->buscar_autorizado($query);
	if(! $autorizados){
            $data['table']='<p>No encontrado.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Apellido y Nombre','DNI','Acciones');
	$i = 0;
	foreach ($autorizados as $autorizado)
	{
		$this->table->add_row($autorizado->Aut_Apenom,
                              $autorizado->Aut_DNI,
                              anchor('anticipos/pagar/'.$autorizado->Aut_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('anticipos/anticipos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }}
    
    function pagar($aut_id){
        if($this->caja_abierta()){  
        $data['id']=$aut_id;
         //$this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
         $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
		 $this->form_validation->set_rules('descripcion','Descripción','required|trim|max_length[500]|xss_clean');
         //$this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
         $this->load->model('Autorizados_model');
		 
		 // validacion del chequeForm
        if ($this->input->post('formaPago')=='Cheque'){
            $this->form_validation->set_rules('banco','banco','required|trim|xss_clean');
            $this->form_validation->set_rules('sucursal','sucursal','required|trim|xss_clean');
            $this->form_validation->set_rules('numero_cheque','numero_cheque','required|trim|xss_clean');
            
        }
        if ($this->form_validation->run() == FALSE){
            $puesto=$this->session->userdata('puesto');
			
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
  			$vale = $data['rbo_nro']; 
        $autorizados= $this->Autorizados_model->get_by_id($aut_id);
        
        // generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        
	foreach ($autorizados as $autorizado)
	{
            $this->table->add_row(
                                "<b>Nombre: </b>$autorizado->Aut_Apenom",
                                "<b>DNI: </b>$autorizado->Aut_DNI",
								"<b>Nro de Vale: </b>$vale",
								"<b>Importe: </b>$autorizado->Aut_Importe"
                                );
			$importe=$autorizado->Aut_Importe;
	}
	
   
	    
        $data['table'] = $this->table->generate();
        
       // $rows= $this->Anticipos_model->centro_costo_todas_sec();
        //foreach ($rows as $row) {
			//$centros[$row->Gru_Descripcion] = $row->Gru_Descripcion;
		//}
        //$data['centros']=$centros;
		$rows= $this->Anticipos_model->buscarbancos();
        foreach ($rows as $row) {
			$bancos[$row->Banco_Nombre] = $row->Banco_Nombre;
		}
		$data['bancos']=$bancos;
		$fecha = date('d-m-Y');
		$data['fecha'] = $fecha;
	$datoPrincipal ['contenidoPrincipal'] = $this->load->view('anticipos/pago', $data, TRUE);
        }else {
                    $puesto=$this->session->userdata('puesto');
					$vale=$this->Anticipos_model->recibo_nro($puesto);
					$autorizados= $this->Autorizados_model->get_by_id($aut_id);
					foreach ($autorizados as $autorizado)
					{$importe=$autorizado->Aut_Importe;}
					
					$comp_nro = $vale;
                    $formaPago = $this->input->post('formaPago');
					//$centros = $this->input->post('centros');
                    $desc = $this->input->post('descripcion');
                    $monto = $importe;
					$fecha = $this->input->post('fecha');
					

                    $autorizados= $this->Autorizados_model->get_by_id($aut_id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($autorizados as $autorizado)
                    {   $data['id']=$aut_id;
                        $data['apeNom']=$autorizado->Aut_Apenom;
                        $data['dni']=$autorizado->Aut_DNI;
                            
                    }
                    
					//CARGO LOS DATOS DEL CHEQUE
					if($formaPago=='Cheque'){
                        
                         $bco = $this->input->post('banco');
                         $suc = $this->input->post('sucursal');
                         $nro_cheque = $this->input->post('numero_cheque');
                       
                         
                         $nombres=  $this->Cheque_model->nombres_bancosuc($bco,$suc);
                         foreach ($nombres as $nombre){
                             $banco=$nombre->Banco_Nombre;
                             $sucursal=$nombre->Suc_Nombre;
                         }
                         
                         $data['banco'] = $banco;
                         $data['sucursal'] = $sucursal;
                         $data['bco_id'] = $bco;
                         $data['suc_id'] = $suc;
						 $data['nro_cheque'] = $nro_cheque;
						}else{$nro_cheque=0;
							  $data['nro_cheque'] = $nro_cheque;
							  $data['bco_id'] = 0;
                         	  $data['suc_id'] = 0;}
					
                    // Genero la tabla que muestra el detalle del movimiento
                    //$data['centros']=$centros;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
					$data['fecha'] = $fecha;
					$data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    //$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                   
				   
				   //controlo mandar a una pantalla u otra dependiendo de si tengo dinero disponible para el egreso
				   $user = $this->session->userdata('user_id');
				   $rendiciones = $this->RendicionesCaja_model->last($user);
				   foreach ($rendiciones as $rendicion)
				   				{$ca =$rendicion->Caj_Id;
								$caja_id = $rendicion->Caj_Id;
								$tot = $rendicion->Caj_MontoApertura;
								}
					 $query2 = $this->db->query("SELECT * FROM cajas WHERE Usr_Login = '$user' AND Caj_FechaHoraCierre IS NULL AND Caj_Id = '$ca'");     				 foreach ($query2->result_array() as $row) 
                        {		    $Caj_Id = $row['Caj_Id'];
                         }
				   
				    
	 				$rendiciones2 = $this->RendicionesCaja_model->ver($caja_id);
					$totalIngresos = 0;
					$totalEgresos = 0;
					foreach ($rendiciones2 as $rendicion2)
							{
							if($rendicion2->Mov_IngresoEgreso==0){
							$ingreso=$rendicion2->Mov_Mono;
							$egreso=0.0;
							$totalIngresos= $totalIngresos + $ingreso;
							 }else{
							$ingreso=0.0;
							$egreso=$rendicion2->Mov_Mono;
							$totalEgresos= $totalEgresos + $egreso;}
							}
				    $total = $tot + $totalIngresos - $totalEgresos;
				   
				   
				   
				   if ($monto<$total)
				    {$datoPrincipal ['contenidoPrincipal'] = $this->load->view('anticipos/confirmacion', $data, TRUE);
				    }
				   else { $data['mensaje'] = 'De acuerdo a los movimientos registrados, NO SE DISPONE DE DINERO SUSFICIENTE para el egreso';
				          $datoPrincipal ['contenidoPrincipal'] = $this->load->view('anticipos/confirmacionError', $data, TRUE);}
				   
				   
				   
				   
				   
				   
				  
        }
        $this->load->view('templates',$datoPrincipal);      
    }}
    
    public function registrar($aut_id){
                if($this->caja_abierta()){}   		
                $comp_tipo = 'VALE';
                
				$autorizados= $this->Autorizados_model->get_by_id($aut_id);
        		foreach ($autorizados as $autorizado)
				{
           			$nombre = $autorizado->Aut_Apenom;
				}
				
				$autorizados= $this->Autorizados_model->get_by_id($aut_id);
				foreach ($autorizados as $autorizado)
				{$importe=$autorizado->Aut_Importe;
				$sec=$autorizado->Sec_Id;
				$dir=$autorizado->Dir_Id;
				$gru=$autorizado->Gru_Id;
				$cur=$autorizado->Cur_Id;
				$dic=$autorizado->Dic_Id;
				
				}
				
                $cc = $this->input->post('centros');
				$bco_id = 9;
				$suc_id = 2;
                $nro_cheque = $this->input->post('nro_cheque');
				//$comp_nro_Externo = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $importe;
				$fecha = $this->input->post('fecha');
				
				
  				$Descripcion = $desc;
  
  
  				$Cheq_Librador = 'Cooperadora';
  				$Cue_id = 1;
 			   
			   
 			   $Cheq_Librador = 'Cooperadora';
               $fecha = date('d-m-Y',strtotime($fecha));
			   $Cheq_FechaCobro = $fecha;
			   if($formaPago=='Cheque'){
                $carga=$this->Anticipos_model->insertcheque($Cue_id,$bco_id,$suc_id,$nro_cheque,$Cheq_Librador,$monto,$fecha,$Cheq_FechaCobro,$Descripcion);
				}
				
 
				 
				 
				                
                //dar a elegrir TIPO DE MOVIMIENTO DE CAJA
                $tipo_desc='Gastos Varios';
                $tipos= $this->Anticipos_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }

                //Completar con Centros de Costos Completos.
                //$centros=  $this->Anticipos_model->centro_costo_grupo($cc);
                //foreach ($centros as $centro){
                    //$sec=$centro->Sec_Id;
                    //$dir=$centro->Dir_Id;
                    //$gru=$centro->Gru_Id;
               // }
				$fecha = date('d-m-Y',strtotime($fecha));
                //cambiar caj_id cuando tengamos sesiones
                $caj_id=$this->session->userdata('caja_id');
                //Egreso >> FALSE
				$fecha = date('Y-m-d',strtotime($fecha));
				
				
                $fecha=$this->Anticipos_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$dic,$nombre);
                
                $movimientos=$this->Anticipos_model->get_id($caj_id,$fecha);
                
				
				/*agrege para  que saque bien el mov id*/
				$query2 = $this->db->query("SELECT * FROM MovimientosCaja 
                WHERE Caj_Id='$caj_id'");     
           			 foreach ($query2->result_array() as $row) 
                        {  $mov_id = $row['Mov_Id'];}
				
				
				
                /*foreach ($movimientos as $movimiento){
                    $mov_id=$movimiento->Mov_Id;
                }*/
                
				
				$puesto=$this->session->userdata('puesto');
				$comp_nro_Externo=$this->Anticipos_model->recibo_nro($puesto);
					
				$this->Anticipos_model->actualizar_autorizado($caj_id,$mov_id,$aut_id);
								
				$this->Anticipos_model->insert_vale($caj_id,$mov_id,$comp_nro_Externo);
				 $this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                $vales= $this->Anticipos_model->get_id_comprobante($caj_id,$mov_id);
                foreach ($vales as $vale){
                    $comp_nro=$vale->Comp_Nro;
                }
                //$this->MovimientosCaja_model->nro_vale($comp_nro_Externo);
                //$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                $autorizados=$this->Autorizados_model->get_by_id($aut_id);
                foreach($autorizados as $autorizado){
                    $razonsocial=$autorizado->Aut_Apenom;
                }
               // $this->MovimientosCaja_model->insertEgreso_movCli($caj_id,$mov_id,$aut_id,$razonsocial);
                $egreso = $this->Anticipos_model->insert($aut_id,$caj_id,$mov_id);
                if ($egreso ==0){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message']='<div class="success">Exito!</div>';
                    $data['id']=$mov_id;
                }
		                
		
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('anticipos/imprimir', $data, TRUE);
                $this->load->view('templates',$datoPrincipal);        
    }
    
    function imprimir($mov_id){
        //Datos de cliente
        $anticip= $this->Anticipos_model->datosanticipo($mov_id);
        foreach ($anticip as $anti)
        {   
            $cli_nom=$anti->Aut_Apenom;
            $cli_dir=$anti->Aut_Direccion;
            $cli_iva='Consumidor Final';
            $cli_cuil=$anti->Aut_DNI;   
            $caj_id= $anti->Entrega_Caj_Id;
            $mov_id= $anti->Entrega_Mov_Id;
        }
        
		
		
        $movs= $this->Anticipos_model->get_mov($caj_id,$mov_id);
        foreach ($movs as $mov){
            //$fecha= date('d-m-Y',strtotime($mov->Mov_FechaHora));
            $formaPago= $mov->Mov_FormaDePago;
            $monto= $mov->Mov_Mono;
            $desc= $mov->Mov_Descripcion;
        }
		$numeroTexto=  $this->MovimientosCaja_model->numerotexto($monto);
        if($fPago=1){
            $formaPago='Contado';
        }else{
            $formaPago='Cheque';
        }
        
		$compro= $this->Anticipos_model->comprobante($caj_id,$mov_id);
        foreach ($compro as $com){
                      $numerocompro= $com->Comp_Nro_Externo;
        }
        $now= now();
        $fech=  unix_to_human($now);
        $fecha=date('d-m-Y',strtotime($fech));

        //$impresion->recibo($fecha,$cli_nom,$cli_dir,$cli_cuil,$cli_iva,$formaPago,$monto,$desc);
        
        $this->load->library('cezpdf');
		$this->load->helper('pdf');
		
                $this->cezpdf->ezText('Vale', 12, array('justification' => 'center'));
		$this->cezpdf->ezSetDy(-10);
                
$content = 
"N* $numerocompro
FECHA: $fecha";

		$this->cezpdf->ezText($content, 10, array('justification' => 'right'));
                $this->cezpdf->ezSetDy(-10);
		

$content = 
"Asociacion Cooperadora de la  UTN - FRRE
DOMICILIO: French 414
		                                                                     							
CUIT: 30-67019023-0														   					 									 
Pers.Juridica Matricula 1321 - Decreto 167																I.V.A.: $cli_iva  
Inic. Acti.: Septiembre 1990																			
Ing. Brutos: 87701/1";

		$this->cezpdf->ezText($content, 10, array('justification' => 'left'));
                $this->cezpdf->ezSetDy(-10);

$content = 
"DESTINATARIO: $cli_nom
DNI: $cli_cuil
CONDICIONES DE PAGO: $formaPago


RECIBI(MOS) LA SUMA DE PESOS $numeroTexto.--
----------------------------------------------------------------------------------------------------------------------------------------------------------------

EN CONCEPTO DE $desc.------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = '
CHEQUE Bco.:-------------------------------------------------
CHEQUE N*.:-------------------------------------------------- 
';                
                $this->cezpdf->ezText($content, 8, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = "TOTAL $         $monto";
                $this->cezpdf->ezText($content, 15, array('justification' => 'right'));
                $this->cezpdf->ezSetDy(-60);
                
$this->cezpdf->ezText('Vale', 12, array('justification' => 'center'));
		$this->cezpdf->ezSetDy(-10);
                
$content = 
"N* $numerocompro
FECHA: $fecha";

		$this->cezpdf->ezText($content, 10, array('justification' => 'right'));
                $this->cezpdf->ezSetDy(-10);
		

$content = 
"Asociacion Cooperadora de la  UTN - FRRE
DOMICILIO: French 414
		                                                                     							
CUIT: 30-67019023-0														   					 									 
Pers.Juridica Matricula 1321 - Decreto 167																I.V.A.: $cli_iva  
Inic. Acti.: Septiembre 1990																			
Ing. Brutos: 87701/1";

		$this->cezpdf->ezText($content, 10, array('justification' => 'left'));
                $this->cezpdf->ezSetDy(-10);

$content = 
"DESTINATARIO: $cli_nom
DNI: $cli_cuil
CONDICIONES DE PAGO: $formaPago


RECIBI(MOS) LA SUMA DE PESOS $numeroTexto.--
----------------------------------------------------------------------------------------------------------------------------------------------------------------

EN CONCEPTO DE $desc.------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------";
                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = '
CHEQUE Bco.:-------------------------------------------------
CHEQUE N*.:-------------------------------------------------- 
';                
                $this->cezpdf->ezText($content, 8, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = "TOTAL $         $monto";
                $this->cezpdf->ezText($content, 15, array('justification' => 'right'));
                $this->cezpdf->ezSetDy(-60);
                
        $this->cezpdf->ezStream();
    }
}

?>
