<?php

class liquidaciones extends MY_Controller {
     
	public function __construct() {
        parent::__construct();
		$this->load->model('Liquidaciones_model','',TRUE);
    }
    
    public function index(){
        if($this->caja_abierta()){};
        $data['table'] = ' ';    
        
        $liquidaciones = $this->Liquidaciones_model->buscar_liquidaciones();
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Curso','Lugar de Dictado','Profesor', 'Fecha de Liquidacion','Fecha Inicio', 'Fecha Fin', 'Importe', 'Acciones');
	$i = 0;
	foreach ($liquidaciones as $liquidacion)
	{
		$this->table->add_row($liquidacion->Cur_Nombre,
                                $liquidacion->Dic_LugarDictado,
                                $liquidacion->Pro_ApeNom,
                                date('d-m-Y',strtotime($liquidacion->Liq_Fecha)),
                                date('d-m-Y',strtotime($liquidacion->Liq_Desde)),
				date('d-m-Y',strtotime($liquidacion->Liq_Hasta)),
                                $liquidacion->Liq_Monto,
                                anchor('liquidaciones/cobrar/'.$liquidacion->Liq_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('liquidaciones/liquidaciones', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }   
    
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('Cur_Nombre');
        
	$liquidaciones = $this->Liquidaciones_model->buscar_curso($query);
			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Curso','Lugar de Dictado','Profesor', 'Fecha de Liquidacion','Fecha Inicio', 'Fecha Fin', 'Importe', 'Acciones');
	$i = 0;
	foreach ($liquidaciones as $liquidacion)
	{
		$this->table->add_row($liquidacion->Cur_Nombre,
                                      $liquidacion->Dic_LugarDictado,
                                      $liquidacion->Pro_ApeNom,
                                      date('d-m-Y',strtotime($liquidacion->Liq_Fecha)),
                                      date('d-m-Y',strtotime($liquidacion->Liq_Desde)),
                                      date('d-m-Y',strtotime($liquidacion->Liq_Hasta)),
				      $liquidacion->Liq_Monto,
                                      anchor('liquidaciones/cobrar/'.$liquidacion->Liq_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('liquidaciones/liquidaciones', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar($Liq_Id){
        
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Pago de Honorarios';
                $data['id']=$Liq_Id;
                //$this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
                $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
                $this->form_validation->set_rules('descripcion','Descripción','required|trim|max_length[50]|xss_clean');
                
                
                 // validacion del chequeForm
        if ($this->input->post('formaPago')=='Cheque'){
            $this->form_validation->set_rules('banco','banco','required|trim|xss_clean');
            $this->form_validation->set_rules('sucursal','sucursal','required|trim|xss_clean');
            $this->form_validation->set_rules('numero_cheque','numero_cheque','required|trim|xss_clean');
            
        }
                
                if ($this->form_validation->run() == FALSE){
                     $puesto=$this->session->userdata('puesto');
			
            		$data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
  
                    $data['id']=$Liq_Id;
                    $liquidaciones= $this->Liquidaciones_model->buscar($Liq_Id);

                    // generate table data

                    $this->table->set_empty("&nbsp;");
                    //$i = 0;
                    foreach ($liquidaciones as $liquidacion)
                    {   $data['liquidacion']=$liquidacion;
                        $this->table->add_row(
                                            "<b>Nombre: </b>$liquidacion->Pro_ApeNom",
                                            "<b>DNI: </b>$liquidacion->Pro_DNI",
											"<b>Nombre del Curso: </b>$liquidacion->Cur_Nombre",
											"<b>Importe: </b>$liquidacion->Liq_Monto"

                                    );$monto = $liquidacion->Liq_Monto;
                    }
					$rows= $this->Anticipos_model->buscarbancos();
        			foreach ($rows as $row) {
					$bancos= $row->Banco_Nombre;
		}
		$data['bancos']='Banco Hipotecario';
					$fecha = date('d-m-Y');
					$data['fecha'] = $fecha;
					$data['monto'] = $monto;
                    $data['table'] = $this->table->generate();
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('Liquidaciones/cobro_liquidacion', $data, TRUE);
                }else{
				
					$liquidaciones= $this->Liquidaciones_model->buscar($Liq_Id);
					foreach ($liquidaciones as $liquidacion)
					{$importe=$liquidacion->Liq_Monto;}
                   // $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $importe;
					$fecha = $this->input->post('fecha');
					
                    $liquidaciones= $this->Liquidaciones_model->buscar($Liq_Id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($liquidaciones as $liquidacion)
                    {   $data['apeNom']=$liquidacion->Pro_ApeNom;
                        $data['dni']=$liquidacion->Pro_DNI;
                        $data['CurNombre']=$liquidacion->Cur_Nombre;
                        $data['lugardictado']=$liquidacion->Dic_LugarDictado;    
                    	$data['monto']=$liquidacion->Liq_Monto;
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
						 $data['numero_cheque'] = $nro_cheque;
						}else{$nro_cheque=0;
							  $data['numero_cheque'] = $nro_cheque;
							  $data['bco_id'] = 0;
                         	  $data['suc_id'] = 0;}
					
                    // Genero la tabla que muestra el detalle del movimiento
                    //$data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    
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
				    {$datoPrincipal ['contenidoPrincipal'] = $this->load->view('Liquidaciones/confirmacion', $data, TRUE);
				    }
				   else { $data['mensaje'] = 'De acuerdo a los movimientos registrados, NO SE DISPONE DE DINERO SUSFICIENTE para el egreso';
				          $datoPrincipal ['contenidoPrincipal'] = $this->load->view('Liquidaciones/confirmacionError', $data, TRUE);}
				   
				   
				   
				   
				   
                    }
                    $this->load->view('templates',$datoPrincipal);
                
    }
    
public function registrar($Liq_Id){
                if($this->caja_abierta()){};
                $data ['subtitulo']='EGRESOS - Pago de Honorarios';
                $data['id']=$Liq_Id;
    			
				$autorizados= $this->Liquidaciones_model->buscarpro($Liq_Id);
        		foreach ($autorizados as $autorizado)
				{
           			$nombre = $autorizado->Pro_ApeNom;
				}
				
				
                $tipo_desc='liquidacion';
                $tipos= $this->Liquidaciones_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
                $gru_desc='liquidacion';
                $centros=  $this->Liquidaciones_model->centro_costo_grupo($gru_desc);
                foreach ($centros as $centro){
                    $sec=$centro->Sec_Id;
                    $dir=$centro->Dir_Id;
                    $gru=$centro->Gru_Id;
                    $cur=$centro->Cur_Id;
                    $Dic=$centro->Dic_Id;
                }
                //$com_nro = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
                $fecha = $this->input->post('fecha');
				$fecha = date('Y-m-d',strtotime($fecha));
                
                //cambiar caj_id cuando tengamos sesiones
                
                
				$bco_id = 9;
				$suc_id = 2;
                $nro_cheque = $this->input->post('numero_cheque');
				$comp_nro_Externo = $this->input->post('comp_nro');
                
				
  				$Descripcion = $desc;
  
  
  				$Cheq_Librador = 'Cooperadora';
  				$Cue_id = 1;
 			   
			   
 			   $Cheq_Librador = 'Cooperadora';
               $fecha = date('d-m-Y',strtotime($fecha));
			   $Cheq_FechaCobro = $fecha;
			   if($formaPago=='Cheque'){
                $carga=$this->Anticipos_model->insertcheque($Cue_id,$bco_id,$suc_id,$nro_cheque,$Cheq_Librador,$monto,$fecha,$Cheq_FechaCobro,$Descripcion);
				}
				
				
                $caj_id=$this->session->userdata('caja_id');
				$fecha = date('Y-m-d',strtotime($fecha));
				
                $fecha=$this->Liquidaciones_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$Dic,$nombre);
                
               // $movimientos=$this->Liquidaciones_model->get_id($caj_id,$fecha);
                //foreach ($movimientos as $movimiento){
                  //  $mov_id=$movimiento->Mov_Id;
                //}
				$query2 = $this->db->query("SELECT * FROM MovimientosCaja 
                WHERE Caj_Id='$caj_id'");     
           			 foreach ($query2->result_array() as $row) 
                        {  $mov_id = $row['Mov_Id'];}
                
                
                $tipo_comp='RECIBO';
               //$this->MovimientosCaja_model->insert_comprobante($tipo_comp,$com_nro,$caj_id,$mov_id);
               //$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                $becas ='';
		$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
		$liquidaciones = $this->Liquidaciones_model->update($Liq_Id,$caj_id,$mov_id);
                //Armo la vista
                if ($liquidaciones==FALSE){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message'] = '<div class="success">Exito!</div>';
                
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('liquidaciones/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
    
}

?>