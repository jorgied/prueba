<?php

class Becas extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
    	// load library
		$this->load->library(array('table','form_validation'));
		
		// load helper
		$this->load->helper('url');
		
		// load model
		$this->load->model('Becas_model','',TRUE);
	}
    
    public function index(){
        if($this->caja_abierta()){};
        $data['table'] = ' '; 
        $becas = $this->Becas_model->buscar_becado2();
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Nombre Becado','DNI','Beca','Importe','Fecha Desde','Fecha Hasta','Acciones');
	$i = 0;
	foreach ($becas as $beca)
	{
		$this->table->add_row($beca->Becado_ApeNom,
                              $beca->Becado_DNI,
                              $beca->Beca_Descripcion,
							  $beca->Beca_MontoMensual,
                              date('d-m-Y',strtotime($beca->Beca_Desde)),
                              date('d-m-Y',strtotime($beca->Beca_Hasta)),
                              anchor('becas/cobrar/'.$beca->Beca_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('becas/becas', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }   
    
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('becado');
        
	$becas = $this->Becas_model->buscar_becado($query);
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Nombre Becado','DNI','Beca','Importe','Fecha Desde','Fecha Hasta','Acciones');
	$i = 0;
	foreach ($becas as $beca)
	{
		$this->table->add_row($beca->Becado_ApeNom,
                              $beca->Becado_DNI,
                              $beca->Beca_Descripcion,
							  $beca->Beca_MontoMensual,
                              date('d-m-Y',strtotime($beca->Beca_Desde)),
                              date('d-m-Y',strtotime($beca->Beca_Hasta)),
                              anchor('becas/cobrar/'.$beca->Beca_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('becas/becas', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar($Beca_Id){
        
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Beca';
                $data['id']=$Beca_Id;
                //$this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
                $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
                $this->form_validation->set_rules('descripcion','Descripci�n','required|trim|max_length[50]|xss_clean');
                
                
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
                    $data['id']=$Beca_Id;
                    $becas= $this->Becas_model->buscar($Beca_Id);
	
                    // generate table data

                    $this->table->set_empty("&nbsp;");
                    $i = 0;
                    foreach ($becas as $beca)
                    {   $data['beca']=$beca;
                        $this->table->add_row(
                                            "<b>Nombre: </b>$beca->Becado_ApeNom",
                                            "<b>DNI: </b>$beca->Becado_DNI",
											"<b>Nro de Recibo: </b>$rbo_nro",
											"<b>Importe: </b>$beca->Beca_MontoMensual"

                                    );$monto = $beca->Beca_MontoMensual;
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
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('becas/cobro_becas', $data, TRUE);
                }else{
				
					 $puesto=$this->session->userdata('puesto');
					$rbo_nro=$this->Anticipos_model->recibo_nro($puesto);
                    $comp_nro = $rbo_nro;
					$becados= $this->Becas_model->buscar($Beca_Id);
					foreach ($becados as $becado)
					{$importe=$becado->Beca_MontoMensual;}
					
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $importe;
					$fecha = $this->input->post('fecha');

                    $becas= $this->Becas_model->buscar($Beca_Id);			

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($becas as $beca)
                    {   $data['Becado_Id']=$beca->Becado_Id;
                        $data['Becado_ApeNom']=$beca->Becado_ApeNom;
                        $data['Becado_Direccion']=$beca->Becado_Direccion;
                        $data['Becado_DNI']=$beca->Becado_DNI; 
						$data['monto']=$beca->Beca_MontoMensual;   
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
                    $data['comp_nro']=$comp_nro;
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
				    {$datoPrincipal ['contenidoPrincipal'] = $this->load->view('becas/confirmacionbecas', $data, TRUE);
				    }
				   else { $data['mensaje'] = 'De acuerdo a los movimientos registrados, NO SE DISPONE DE DINERO SUSFICIENTE para el egreso';
				          $datoPrincipal ['contenidoPrincipal'] = $this->load->view('becas/confirmacionbecasError', $data, TRUE);}
				   
				   
				   
                    }
                    $this->load->view('templates',$datoPrincipal);
                
    }
    
public function registrar($Beca_Id){
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Beca';
                $data['id']=$Beca_Id;
  
  				$autorizados= $this->Becas_model->buscar($Beca_Id);
        		foreach ($autorizados as $autorizado)
				{
           			$nombre = $autorizado->Becado_ApeNom;
					$sec=$autorizado->Sec_Id;
					$dir=$autorizado->Dir_Id;
					$gru=$autorizado->Gru_Id;
					$cur=$autorizado->Cur_Id;
					$dic=$autorizado->Dic_Id;
				}
  				
  
                $tipo_desc='Becas';
                $tipos= $this->Becas_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
                $gru_desc='Becas';
                //$centros=  $this->Becas_model->centro_costo_grupo($gru_desc);
                //foreach ($centros as $centro){
                  //  $sec=$centro->Sec_Id;
                  //  $dir=$centro->Dir_Id;
                  //  $gru=$centro->Gru_Id;
                   // $cur=$centro->Cur_Id;
                  //  $Dic=$centro->Dic_Id;
               // }
				$bco_id = 9;
				$suc_id = 2;
                $nro_cheque = $this->input->post('nro_cheque');
				
                $Becado_Id = $this->input->post('Becado_Id');
                $Becado_ApeNom = $this->input->post('Becado_ApeNom');
                $comp_nro = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
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
				$fecha = date('Y-m-d',strtotime($fecha));
                
				
                //cambiar caj_id cuando tengamos sesiones
                
                
                $caj_id=$this->session->userdata('caja_id');
                $fecha=$this->Becas_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$dic,$nombre);
                
                $movimientos=$this->Becas_model->get_id($caj_id,$fecha);
                foreach ($movimientos as $movimiento){
                    $mov_id=$movimiento->Mov_Id;
                }
                
                $tipo_comp='RECIBO';
                $this->Becas_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
				$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
               // $this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                
               //$this->MovimientosCaja_model->insertEgreso_movCli($caj_id,$mov_id,$Becado_Id,$Becado_ApeNom);
                
		$beca = $this->Becas_model->update($Beca_Id,$caj_id,$mov_id);
                //Armo la vista
                if ($beca==FALSE){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message'] = '<div class="success">Exito!</div>';
                
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('becas/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
    
   function imprimir($beca_id){
        //Datos de cliente
        $alquileres= $this->Becas_model->buscarimprimir($beca_id);
        foreach ($alquileres as $alquiler)
        {   
            $cli_nom=$alquiler->Becado_ApeNom;
            $cli_dir=$alquiler->Becado_Direccion;
            $cli_iva='Exento';
            $cli_cuil=$alquiler->Becado_DNI;   
            $caj_id= $alquiler->MovimientoCaja_Caj_Id;
            $mov_id= $alquiler->MovimientoCaja_Mov_Id;
        }
        
        $movs= $this->Becas_model->get_mov($caj_id,$mov_id);
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