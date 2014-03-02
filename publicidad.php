<?php

class publicidad extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
		
		$this->load->model('Consultas_model','',TRUE);
		$this->load->model('Publicidad_model','',TRUE);
    }
    
    public function index(){
        if($this->caja_abierta()){};
        $data['table'] = ' ';  
        $publicidad = $this->Publicidad_model->listar_prov();
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Razon Social','Fecha de Publicidad','Importe','Saldo a Abonar','Acciones','');
	$i = 0;
	foreach ($publicidad as $publi)
	{
		$this->table->add_row($publi->Prov_RazonSocial,
                              $publi->Pub_Fecha,
                              $publi->Pub_Monto,
                              $publi->Pub_Saldo,
                              anchor('publicidad/pagar/'.$publi->Pub_Id,'Pagar',array('class'=>'money')),
							  anchor('publicidad/vercursos/'.$publi->Pub_Id,'Ver Cursos Publicados',array('class'=>'view'))
			);
	}
	$data['table'] = $this->table->generate();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('publicidad/publicidades', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
	
	public function vercursos($Pub_Id){
        if($this->caja_abierta()){};
        $data['table'] = ' ';  
        $publicidad = $this->Publicidad_model->listar_prov2($Pub_Id);
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Razon Social','Fecha de Publicidad','Importe','Saldo a Abonar','Acciones');
	$i = 0;
	foreach ($publicidad as $publi)
	{
		$this->table->add_row($publi->Prov_RazonSocial,
                              $publi->Pub_Fecha,
                              $publi->Pub_Monto,
                              $publi->Pub_Saldo,
                              anchor('publicidad/pagar/'.$publi->Pub_Id,'Pagar',array('class'=>'money'))
							  			);
	}
	
	$data['table'] = $this->table->generate();
	
	$publicid = $this->Publicidad_model->listar_cursos($Pub_Id);
	  $this->table->set_heading('Nombre del Curso','Dias de Dictado','Hora Desde','Hora Hasta');
	$i = 0;
	foreach ($publicid as $pub)
	{       
                $dia = $pub->Dia;
                if ($dia==1) {$Dias = 'Lunes';}
                else {if ($dia==2) {$Dias = 'Martes';}
                      else {if ($dia==3) {$Dias = 'Miercoles';}
                            else {if ($dia==4) {$Dias = 'Jueves';} 
                                  else {$Dias = 'Viernes';}
                                 }
                           } 
                     }
		$this->table->add_row($pub->Cur_Nombre,
                              $Dias,  
                              $pub->HoraDesde,
                              $pub->HoraHasta
			);
	}
	
	$data['table2'] = $this->table->generate();
	
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('publicidad/publicidades2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }   
    
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('proveedor');
        
	$proveedores = $this->Publicidad_model->buscar_prov($query);
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Razon Social','Fecha de Publicidad','Importe','Saldo a Abonar','Acciones','');
	$i = 0;
	foreach ($proveedores as $proveedor)
	{
		$this->table->add_row($proveedor->Prov_RazonSocial,
                              $proveedor->Pub_Fecha,
                              $proveedor->Pub_Monto,
                              $proveedor->Pub_Saldo,
                              anchor('publicidad/pagar/'.$proveedor->Pub_Id,'Pagar',array('class'=>'money')),
							  anchor('publicidad/vercursos/'.$proveedor->Pub_Id,'Ver Cursos Publicados',array('class'=>'view'))
			);
	}
	$data['table'] = $this->table->generate();
        
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('publicidad/publicidades', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function pagar($Pub_Id){
        
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Publicidad';
                $data['id']=$Pub_Id;
                $this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
                $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
                $this->form_validation->set_rules('desc','Descripcion','required|trim|max_length[50]|xss_clean');
                $this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
                
                // validacion del chequeForm
        if ($this->input->post('formaPago')=='Cheque'){
            $this->form_validation->set_rules('banco','banco','required|trim|xss_clean');
            $this->form_validation->set_rules('sucursal','sucursal','required|trim|xss_clean');
            $this->form_validation->set_rules('numero_cheque','numero_cheque','required|trim|xss_clean');
            
        }
                
                if ($this->form_validation->run() == FALSE){
                    $puesto=$this->session->userdata('puesto');
			
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
  
                    $data['id']=$Pub_Id;
                    $proveedores = $this->Publicidad_model->buscar_prov2($Pub_Id);

                    // generate table data

                    $this->table->set_empty("&nbsp;");
                    $i = 0;
                    foreach ($proveedores as $proveedor)
                    {   $data['proveedor']=$proveedor;
                        $this->table->add_row(
                                            "<b>Nombre: </b>$proveedor->Prov_RazonSocial",
                                            "<b>CUIT: </b>$proveedor->Prov_CUIT",
					    
					    "<b>Saldo a Abonar: </b>$proveedor->Pub_Saldo"

                                    );
                    }
					$rows= $this->Anticipos_model->buscarbancos();
        foreach ($rows as $row) {
			$bancos[$row->Banco_Nombre] = $row->Banco_Nombre;
		}
		$data['bancos']=$bancos;

					$data['Prov_RazonSocial'] = $proveedor->Prov_RazonSocial;
					$fecha = date('d-m-Y');
					$data['fecha'] = $fecha;
                    $data['table'] = $this->table->generate();
					$data['mensaje'] = '';
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('publicidad/pago_publicidad', $data, TRUE);
                }else{
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('desc');
                    $monto = $this->input->post('monto');
                    $fecha = $this->input->post('fecha');

                    $proveedores = $this->Publicidad_model->buscar_prov2($Pub_Id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($proveedores as $proveedor)
                    {   $data['Pub_Id']=$proveedor->Pub_Id;
                        $data['Prov_RazonSocial']=$proveedor->Prov_RazonSocial;
                        $data['Prov_CUIT']=$proveedor->Prov_CUIT;
                        $data['Pub_Monto']=$proveedor->Pub_Monto;
                        $data['Pub_Saldo']=$proveedor->Pub_Saldo; 
						$saldoaabonar =    $proveedor->Pub_Saldo; 
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
                    $data['id']= $Pub_Id;
					$data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
                    $data['fecha'] = $fecha;
                    
					if ($monto>$saldoaabonar){
					
					
					
					
					$puesto=$this->session->userdata('puesto');
			
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
  
                    $data['id']=$Pub_Id;
                    $proveedores = $this->Publicidad_model->buscar_prov2($Pub_Id);

                    // generate table data

                    $this->table->set_empty("&nbsp;");
                    $i = 0;
                    foreach ($proveedores as $proveedor)
                    {   $data['proveedor']=$proveedor;
                        $this->table->add_row(
                                            "<b>Nombre: </b>$proveedor->Prov_RazonSocial",
                                            "<b>CUIT: </b>$proveedor->Prov_CUIT",
					    
					    "<b>Saldo a Abonar: </b>$proveedor->Pub_Saldo"

                                    );
                    }
					$rows= $this->Anticipos_model->buscarbancos();
        foreach ($rows as $row) {
			$bancos[$row->Banco_Nombre] = $row->Banco_Nombre;
		}
		$data['bancos']=$bancos;

					$data['Prov_RazonSocial'] = $proveedor->Prov_RazonSocial;
					$fecha = date('d-m-Y');
					$data['fecha'] = $fecha;
                    $data['table'] = $this->table->generate();
					$data['mensaje'] = "El valor ingresado es: '$monto' y supera lo adeudado - DEUDA TOTAL: '$saldoaabonar'";
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('publicidad/pago_publicidad', $data, TRUE);

					
					
					
					
					
					}else{
					//$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                    $data['mensaje'] = '';
					$datoPrincipal ['contenidoPrincipal'] = $this->load->view('publicidad/confirmacion', $data, TRUE);
                    
					}
					
					}
                    $this->load->view('templates',$datoPrincipal);
                
    }
    
public function registrar($Pub_Id){
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Publicidad';
                $data['id']=$Pub_Id;
  				
				$autorizados= $this->Publicidad_model->listar_prov2($Pub_Id);
        		foreach ($autorizados as $autorizado)
				{
           			$nombre = $autorizado->Prov_RazonSocial;
				}
				
                $tipo_desc='Publicidad';
                $tipos= $this->Publicidad_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
                $gru_desc='Publicidad';
                $centros=  $this->Publicidad_model->centro_costo_grupo($gru_desc);
                foreach ($centros as $centro){
                    $sec=$centro->Sec_Id;
                    $dir=$centro->Dir_Id;
                    $gru=$centro->Gru_Id;
                    $cur=$centro->Cur_Id;
                    $Dic=$centro->Dic_Id;
                }
				$bco_id = 9;
				$suc_id = 2;
                $nro_cheque = $this->input->post('nro_cheque');
				$comp_nro_Externo = $this->input->post('comp_nro');
                
                $Pub_Id = $this->input->post('Pub_Id');
                $Prov_RazonSocial = $this->input->post('Prov_RazonSocial');
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
                $fecha=$this->Publicidad_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$Dic,$nombre);
                
                $movimientos=$this->Publicidad_model->get_id($caj_id,$fecha);
                foreach ($movimientos as $movimiento){
                    $mov_id=$movimiento->Mov_Id;
                
				}
				
                
				$descontar=$this->Publicidad_model->descuento($Pub_Id);
                foreach ($descontar as $descon){
                    $Pub_Saldo=$descon->Pub_Saldo;
                }
                $saldoapagar = $Pub_Saldo - $monto;
				$this->Publicidad_model->Actualizar($Pub_Id,$saldoapagar);
				
                $tipo_comp='RECIBO';
                $this->Publicidad_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
                //$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                
               //$this->MovimientosCaja_model->insertEgreso_movCli($caj_id,$mov_id,$Pub_Id,$Becado_ApeNom);
        $ids = $this->Publicidad_model->max_id();
        foreach ($ids as $id){
            $i_id=$id->id;
			
        }
		        
		$beca = $this->Publicidad_model->guardarPublicidad($Pub_Id,$i_id,$caj_id,$mov_id);
                //Armo la vista
                if ($beca==FALSE){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message'] = '<div class="success">Exito!</div>';
                
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('Publicidad/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
    
   function imprimir($Pub_Id){
        //Datos de cliente
        $publi= $this->Publicidad_model->buscardatospubliprov($Pub_Id);
        foreach ($publi as $pu)
        {   
            $cli_nom=$pu->Prov_RazonSocial;
            $cli_dir=$pu->Prov_Direccion;
            $cli_iva='';
            $cli_cuil=$pu->Prov_CUIT;   
            $caj_id= $pu->MovimientoCaja_Caj_Id;
            $mov_id= $pu->MovimientoCaja_Mov_Id;
        }
        
        $movs= $this->Publicidad_model->get_mov($caj_id,$mov_id);
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