<?php

class viaticos extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
		
		$this->load->model('Consultas_model','',TRUE);
		$this->load->model('Viaticos_model','',TRUE);
    }
    
    public function index(){
        if($this->caja_abierta()){};
        $data['table'] = ' ';  
        $viaticos = $this->Viaticos_model->buscar_viaticos();
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Nombre Profesor','DNI','Fecha Emision Viatico','Motivo','Importe','Acciones');
	$i = 0;
	foreach ($viaticos as $viatico)
	{
		$this->table->add_row($viatico->Pro_ApeNom,
                              $viatico->Pro_DNI,
                              date('d-m-Y',strtotime($viatico->Via_Fecha)),
                              $viatico->Via_Motivo,
							  $viatico->Via_Monto,
                              anchor('viaticos/cobrar/'.$viatico->Via_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('viaticos/viaticos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }   
    
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('profesor');
        
	$viaticos = $this->Viaticos_model->buscar_profesor($query);
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Nombre Profesor','DNI','Fecha Emision Viatico','Motivo','Importe','Acciones');
	$i = 0;
	foreach ($viaticos as $viatico)
	{
		$this->table->add_row($viatico->Pro_ApeNom,
                              $viatico->Pro_DNI,
                              date('d-m-Y',strtotime($viatico->Via_Fecha)),
                              $viatico->Via_Motivo,
							  $viatico->Via_Monto,
                              anchor('viaticos/cobrar/'.$viatico->Via_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('viaticos/viaticos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar($Via_id){
        
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Viatico';
                $data['id']=$Via_id;
                //$this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
                $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
                $this->form_validation->set_rules('descripcion','Descripcion','required|trim|max_length[50]|xss_clean');
                //$this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
                
                 // validacion del chequeForm
        if ($this->input->post('formaPago')=='Cheque'){
            $this->form_validation->set_rules('banco','banco','required|trim|xss_clean');
            $this->form_validation->set_rules('sucursal','sucursal','required|trim|xss_clean');
            $this->form_validation->set_rules('numero_cheque','numero_cheque','required|trim|xss_clean');
            
        }
        if ($this->form_validation->run() == FALSE){
            $puesto=$this->session->userdata('puesto');
			
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
     		$rbo_nro = $data['rbo_nro'];
                    $data['id']=$Via_id;
                    $viaticos= $this->Viaticos_model->bus($Via_id);

                    // generate table data

                    $this->table->set_empty("&nbsp;");
                    $i = 0;
                    foreach ($viaticos as $viatico)
                    {   $data['viatico']=$viatico;
                        $this->table->add_row(
                                            "<b>Nombre: </b>$viatico->Pro_ApeNom",
                                            "<b>DNI: </b>$viatico->Pro_DNI",
											"<b>Nro de Recibo: </b>$rbo_nro",
											"<b>Importe: </b>$viatico->Via_Monto"
											 
                                    );
									$monto = $viatico->Via_Monto;
                    }
					$rows= $this->Anticipos_model->buscarbancos();
        foreach ($rows as $row) {
			$bancos[$row->Banco_Nombre] = $row->Banco_Nombre;
		}
		$data['bancos']=$bancos;
					$fecha = date('d-m-Y');
					$data['fecha'] = $fecha;
					$data['monto'] = $monto;
                    $data['table'] = $this->table->generate();
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('viaticos/cobro_viatico', $data, TRUE);
                }else{
				
				    $puesto=$this->session->userdata('puesto');
					$rbo_nro=$this->Anticipos_model->recibo_nro($puesto);
                    $comp_nro = $rbo_nro;
					$viaticos= $this->Viaticos_model->bus($Via_id);
					foreach ($viaticos as $viatico)
					{$importe=$viatico->Via_Monto;}
					
					
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $importe;
					$fecha = $this->input->post('fecha');

                    $viaticos= $this->Viaticos_model->bus($Via_id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($viaticos as $viatico)
                    {   $data['Pro_Id']=$viatico->Pro_Id;
                        $data['Pro_ApeNom']=$viatico->Pro_ApeNom;
                        $data['Pro_Direccion']=$viatico->Pro_Direccion;
                        $data['Pro_DNI']=$viatico->Pro_DNI;
						$data['monto']=$viatico->Via_Monto;    
                        
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
                    $data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    //$data['monto']=$monto;
					$data['fecha'] = $fecha;
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
				    {$datoPrincipal ['contenidoPrincipal'] = $this->load->view('viaticos/confirmacion', $data, TRUE);
				    }
				   else { $data['mensaje'] = 'De acuerdo a los movimientos registrados, NO SE DISPONE DE DINERO SUSFICIENTE para el egreso';
				          $datoPrincipal ['contenidoPrincipal'] = $this->load->view('viaticos/confirmacionError', $data, TRUE);}
				   
                    }
                    $this->load->view('templates',$datoPrincipal);
                
    }
    
public function registrar($Via_Id){
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Viatico';
                $data['id']=$Via_Id;
    			
				$autorizados= $this->Viaticos_model->bus($Via_Id);	
        		foreach ($autorizados as $autorizado)
				{
           			$nombre = $autorizado->Pro_ApeNom;
					$sec=$autorizado->Sec_Id;
					$dir=$autorizado->Dir_Id;
					$gru=$autorizado->Gru_Id;
					$cur=$autorizado->Cur_Id;
					$dic=$autorizado->Dic_Id;
				}
				
                $tipo_desc='Viaticos';
                $tipos= $this->Viaticos_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
                $gru_desc='Viaticos';
                //$centros=  $this->Viaticos_model->centro_costo_grupo($gru_desc);
                //foreach ($centros as $centro){
                   // $sec=$centro->Sec_Id;
                    //$dir=$centro->Dir_Id;
                    //$gru=$centro->Gru_Id;
                    //$cur=$centro->Cur_Id;
                    //$Dic=$centro->Dic_Id;
                //}
				$bco_id = 9;
				$suc_id = 2;
                $nro_cheque = $this->input->post('nro_cheque');
				$comp_nro_Externo = $this->input->post('comp_nro');
                $Pro_Id = $this->input->post('Pro_Id');
                $Pro_ApeNom = $this->input->post('Pro_ApeNom');
                $comp_nro = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
				$fecha = $this->input->post('fecha');
				
				
				$Cheq_Librador = 'Cooperadora';
  				$Cue_id = 1;
 			   
			   $Descripcion = $desc;
 			   $Cheq_Librador = 'Cooperadora';
               $fecha = date('d-m-Y',strtotime($fecha));
			   $Cheq_FechaCobro = $fecha;
			   if($formaPago=='Cheque'){
                $carga=$this->Anticipos_model->insertcheque($Cue_id,$bco_id,$suc_id,$nro_cheque,$Cheq_Librador,$monto,$fecha,$Cheq_FechaCobro,$Descripcion);
				}
				
				
				$fecha = date('Y-m-d',strtotime($fecha));
                
                //cambiar caj_id cuando tengamos sesiones
                
                
                $caj_id=$this->session->userdata('caja_id');
                $fecha=$this->Viaticos_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$dic,$nombre);
                
                $movimientos=$this->Viaticos_model->get_id($caj_id,$fecha);
                foreach ($movimientos as $movimiento){
                    $mov_id=$movimiento->Mov_Id;
                }
                
                
                $tipo_comp='RECIBO';
                $this->Viaticos_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
				$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                //$this->Viaticos_model->update_egreso($caj_id,$monto);
                
                //$this->Viaticos_model->insertEgreso_movCli($caj_id,$mov_id,$Pro_Id,$Pro_ApeNom);
         $Via_Fecha = date('Y-m-d');   
		 
		
		 $viat = $this->Viaticos_model->update2($Via_Id,$caj_id,$mov_id);
                //Armo la vista
                if ($viat==FALSE){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message'] = '<div class="success">Exito!</div>';
                
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('viaticos/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
    
    function imprimir($Via_Id){
        //Datos de cliente
        $profesor= $this->Viaticos_model->bus2($Via_Id);
        foreach ($profesor as $profe)
        {   
            $cli_nom=$profe->Pro_ApeNom;
            $cli_dir=$profe->Pro_Direccion;
            $cli_iva='Consumidor Final';
            $cli_cuil=$profe->Pro_DNI;   
            $caj_id= $profe->MovimientoCaja_Caj_Id;
            $mov_id= $profe->MovimientoCaja_Mov_Id;
        }
        
        $movs= $this->Viaticos_model->get_mov($caj_id,$mov_id);
        foreach ($movs as $mov){
            $fecha= date('d-m-Y',strtotime($mov->Mov_FechaHora));
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

        //$impresion->recibo($fecha,$cli_nom,$cli_dir,$cli_cuil,$cli_iva,$formaPago,$monto,$desc);
        
        $this->load->library('cezpdf');
		$this->load->helper('pdf');
		
                $this->cezpdf->ezText('RECIBO [C]', 12, array('justification' => 'center'));
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
                
$this->cezpdf->ezText('RECIBO [C]', 12, array('justification' => 'center'));
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