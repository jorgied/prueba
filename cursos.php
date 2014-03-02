<?php

class cursos extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
		$this->load->model('Cursos_model','',TRUE);
		$this->load->model('Consultas_model','',TRUE);
    }
    
   public function index(){
        if($this->caja_abierta()){};
        $cursos = $this->Cursos_model->buscar_curso();
	if(! $cursos){
            $data['table']='<p style="text-align: left">No se encontraron cuotas pendientes para este alumno.</p>';
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Curso','Profesor','Acciones');
	$i = 0;
	foreach ($cursos as $curso)
	{
                 /*$dia = $curso->Dia;
                if ($dia==1) {$Dias = 'Lunes';}
                else {if ($dia==2) {$Dias = 'Martes';}
                      else {if ($dia==3) {$Dias = 'Miercoles';}
                            else {if ($dia==4) {$Dias = 'Jueves';} 
                                  else {$Dias = 'Viernes';}
                                 }
                           } 
                     }*/
		$this->table->add_row($curso->Cur_Nombre,
                              
                              $curso->Pro_ApeNom,
                              anchor('cursos/alumnos/'.$curso->Dic_Id,'Ver Alumnos',array('class'=>'view'))
								
			);
	}
	$data['table'] = $this->table->generate();
        } 
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/cuotas', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
	
    
	public function alumnos($Dic_Id){
        if($this->caja_abierta()){};
     
	 $Alum = $this->Consultas_model->buscar_alumnos($Dic_Id);
	if(! $Alum){
            $data['table']='<p style="text-align: left">No se encontraron Alumnos del Curso.</p>';
			$cursonombre= $this->Consultas_model->buscar_cursonombre($Dic_Id);
				foreach ($cursonombre as $curnom)
                    {   $data['curso']=$curnom->Cur_Nombre;}
            
        }else{  			
	// generate table data
		
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Alumno','DNI','Acciones','');
	$i = 0;
	foreach ($Alum as $Alu)
	{
		$this->table->add_row($Alu->Alu_ApeNom,
				      $Alu->Alu_DNI,
                                      
							  anchor('cursos/cobrar/'.$Alu->Ins_ID,'Cobrar',array('class'=>'money')),
							  anchor('cursos/estadocuenta2/'.$Alu->Ins_ID,'Estado de Cuenta',array('class'=>'view'))
           );
	}
	$data['table'] = $this->table->generate();
	$data['curso'] = $Alu->Cur_Nombre;
        } 
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/cuotas2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
   
	
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('alumno');
	$query2 = $this->input->post('curso');
	$data['alumno'] = $query;
	$data['curso'] = $query2;
        
	$cursos = $this->Cursos_model->buscar_cuota($data);
	if(! $cursos){
            $data['table']='<p style="text-align: left">No se encontraron el Alumno y el Curso ingresado.</p>';
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Curso','Alumno','Acciones');
						
	foreach ($cursos as $curso)
	{
		$this->table->add_row($curso->Cur_Nombre,
							  $curso->Alu_ApeNom,
							  
							
						  anchor('cursos/cobrar/'.$curso->Ins_ID,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
	
        
        }	
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/cuotas', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
public function cobrar($Ins_ID){
        	    if($this->caja_abierta()){};
                $data ['subtitulo']='INGRESO - Curso';
				$resguardo = 100000000;
                $Cuo_Numeroseleccionada = 0;
				$salir = 0;
				$diferencia = 0;
				$control = 0;
				$monto = 0;
				$data['id']=$Ins_ID;
                $this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
                $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
                $this->form_validation->set_rules('descripcion','Descripción','required|trim|max_length[50]|xss_clean');
                $this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
                
				// validacion del chequeForm
        if ($this->input->post('formaPago')=='Cheque'){
            $this->form_validation->set_rules('banco','banco','required|trim|xss_clean');
            $this->form_validation->set_rules('sucursal','sucursal','required|trim|xss_clean');
            $this->form_validation->set_rules('numero_cheque','numero_cheque','required|trim|xss_clean');
            
        }
				
				
				$inscrip2= $this->Cursos_model->buscar_inscripcion($Ins_ID);
				foreach ($inscrip2 as $inscri2)
                    {   $data['cli_id']=$inscri2->Alu_Id;
                        $data['apeNom']=$inscri2->Alu_ApeNom;
                        $data['curNombre']=$inscri2->Cur_Nombre;
                        $data['dni']=$inscri2->Alu_DNI;    
                    }
           If ($salir ==0) {     
                if (($this->form_validation->run() == FALSE)){
                 $puesto=$this->session->userdata('puesto');
			
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
     
			// generate table data
			

				$inscrip= $this->Cursos_model->buscar_cuotaMasAntigua($Ins_ID);
					//del listado de cuotas del alumno, del curso, del dictado, busco las mas anigua pendiente 
                        foreach ($inscrip as $row) 
                        { 
                         $Alu_Id = $row->Alu_Id;
                         $Cuo_Monto = $row->Cuo_Monto;
                         $Cuo_Numero = $row->Cuo_Numero;
                         $Cuo_MontoPagado = $row->Cuo_MontoPagado;
               
                        if ($Cuo_Monto>$Cuo_MontoPagado) 
                        { 
                            if ($resguardo > $Cuo_Numero)
                                {
                                $Cuo_Numeroseleccionada = $row->Cuo_Numero;
                                $resguardo = $Cuo_Numero;
                                $monto = $Cuo_Monto-$Cuo_MontoPagado;
								$salir = 0;
                                }
                        }
						else {if ($Cuo_Monto==$Cuo_MontoPagado)
								$salir = 1;
								}
                    }
             	If ($salir ==0) {
                    // Genero la tabla que muestra los datos de Alumno
                    foreach ($inscrip2 as $inscri2)
                    {   $Alu_Id = $inscri2->Alu_Id;
                        $Alu_Nombre = $inscri2->Alu_ApeNom;
                        $Cur_Nombre = $inscri2->Cur_Nombre;
						$Alu_DNI = $inscri2->Alu_DNI;
                            
                    }
				
                    
				// aca hace la seleccion de la cuota mas antigua pendiente para la presentacion en la pantalla de los datos        
               $query2 = $this->db->query("SELECT * FROM Cuotas, Alumnos WHERE Cuotas.Cuo_Numero='$Cuo_Numeroseleccionada' AND Cuotas.Alu_Id='$Alu_Id'");     
                        
               foreach ($query2->result_array() as $row) 
                        { 
                         
                         $Cuo_Numero = $row['Cuo_Numero'];
                         $Cuo_FechaVto = $row['Cuo_FechaVto'];
                         $Cuo_Monto = $row['Cuo_Monto'];
                         $Cuo_MontoPagado = $row['Cuo_MontoPagado'];
                         
                         }
               		
                    $this->table->set_empty("&nbsp;");
                    
                    foreach ($inscrip2 as $inscri2)
                    {   $data['inscri2']=$inscri2;
                        $this->table->add_row(
                                            "<b>Alumno: </b>$inscri2->Alu_ApeNom",
                                            "<b>DNI: </b>$inscri2->Alu_DNI",
                                            "<b>Curso: </b>$inscri2->Cur_Nombre",
                                            "<b>Numero de Cuota: </b>$Cuo_Numero"
											

                                    );
                    }
					$Cuo_FechaVto = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
					$this->table->add_row(
                                            "<b>Importe: </b>$Cuo_Monto",
                                            "<b>Fecha de Vencimiento: </b>$Cuo_FechaVto",
					    "<b>Importe Abonado:</b>$Cuo_MontoPagado"

                                    );
					$rows= $this->Anticipos_model->buscarbancos();
        foreach ($rows as $row) {
			$bancos[$row->Banco_Nombre] = $row->Banco_Nombre;
		}
		$data['bancos']=$bancos;
					$data['monto']=$monto;
					$data['Alu_Id']=$Alu_Id;
                    $data['Alu_Nombre']=$Alu_Nombre;
					$data['Ins_ID']=$Ins_ID;
					$data['Alu_DNI']=$Alu_DNI;
                    $data['Cur_Nombre']=$Cur_Nombre;
					$data['Cuo_Numero'] = $Cuo_Numero;
					$data['Cuo_Monto'] = $Cuo_Monto;
					$data['Cuo_MontoPagado'] = $Cuo_MontoPagado;
					$data['Cuo_FechaVto'] = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
					$fecha = date('d-m-Y');
					$data['fecha'] = $fecha;
					$data['table'] = $this->table->generate();
					$data['mensaje'] = '';
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/cobro_cuotas', $data, TRUE);
                 }
	}else{
			
				$resguardo = 100000000;
                $Cuo_Numeroseleccionada = 0;
				$salir = 0;
				$diferencia = 0;
				$control = 0;
				$montoacalcular = 0;
				$data['id']=$Ins_ID;				
				//vuelvo  a hacer el calculo para sacar el nro de cuota
				$inscrip= $this->Cursos_model->buscar_cuotaMasAntigua($Ins_ID);
					//del listado de cuotas del alumno, del curso, del dictado, busco las mas anigua pendiente 
                        foreach ($inscrip as $row) 
                        { 
                         $Alu_Id = $row->Alu_Id;
                         $Cuo_Monto = $row->Cuo_Monto;
                         $Cuo_Numero = $row->Cuo_Numero;
                         $Cuo_MontoPagado = $row->Cuo_MontoPagado;
               
                        if ($Cuo_Monto>$Cuo_MontoPagado) 
                        { 
                            if ($resguardo > $Cuo_Numero)
                                {
                                $Cuo_Numeroseleccionada = $row->Cuo_Numero;
                                $resguardo = $Cuo_Numero;
                                $montoacalcular = $Cuo_Monto-$Cuo_MontoPagado;
								$salir = 0;
                                }
                        }
						else {if ($Cuo_Monto==$Cuo_MontoPagado)
								$salir = 1;
								}
                    	}
             	 //fin del calculo para sacar el nro de cuota
				
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
					$Cuo_Numero = $Cuo_Numeroseleccionada;
					//$Cuo_Numero = $this->input->post('Cuo_Numero');
					$Cuo_Monto = $this->input->post('Cuo_Monto');
					$Cuo_MontoPagado = $this->input->post('Cuo_MontoPagado');
					$Cuo_FechaVto = $this->input->post('Cuo_FechaVto');
                    $fecha = $this->input->post('fecha');
					$monto = $this->input->post('monto');
					$Alu_ApeNom = $this->input->post('Alu_Nombre');
					$Cur_Nombre = $this->input->post('Cur_Nombre');
					$Alu_DNI = $this->input->post('Alu_DNI');
					
	                // Genero la tabla que muestra los datos de cliente
   					$data['Alu_Nombre']=$Alu_ApeNom;
					$data['Cur_Nombre']=$Cur_Nombre;
					$data['Alu_DNI']=$Alu_DNI;
					$data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
					$data['Cuo_Numero'] = $Cuo_Numero;
					$data['Cuo_Monto'] = $Cuo_Monto;
					$data['Cuo_MontoPagado'] = $Cuo_MontoPagado;
					$data['Cuo_FechaVto'] = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
					$data['fecha'] = $fecha;
//DESDE ACA HAGO EL RECORRIDO PARA SABER SI EL TOTAL DE LO PENDIENTE ES MAYOR AL INGRESADO					
					$sumacuo = 0;
					$sumapagado = 0;
					$deuda = 0;

					$sumacuotas= $this->Cursos_model->buscar_cuotaMasAntigua($Ins_ID);
					 foreach ($sumacuotas as $suma)
						{$Cuo_Monto = $suma->Cuo_Monto;
						 $Cuo_MontoPagado = $suma->Cuo_MontoPagado;
						 $sumacuo = $sumacuo + $Cuo_Monto;
						 $sumapagado = $sumapagado + $Cuo_MontoPagado;
						}
						
						$deuda = $sumacuo - $sumapagado;
					
//ACA TERMINA LA SUMA DE LAS FACTURAS PARA CONTROLAR EL TOTAL DE LO ADEUDADO CON 					
					//aca controlo el valor ingresado 
					if ($monto>$deuda){
					
//DESDE ACA VUELVE A CONTROLAR Y CARGAR LOS DATOS PARA LA CARGA
					$montoacalcular=0;
					$resguardo=0;					
					$resguardo = 100000000;
                	$Cuo_Numeroseleccionada = 0;
					$salir = 0;
					$diferencia = 0;
					$control = 0;
					

					$inscrip= $this->Cursos_model->buscar_cuotaMasAntigua($Ins_ID);
					//del listado de cuotas del alumno, del curso, del dictado, busco las mas anigua pendiente 
                        foreach ($inscrip as $row) 
                        { 
                         $Alu_Id = $row->Alu_Id;
                         $Cuo_Monto = $row->Cuo_Monto;
                         $Cuo_Numero = $row->Cuo_Numero;
                         $Cuo_MontoPagado = $row->Cuo_MontoPagado;
               
                        if ($Cuo_Monto>$Cuo_MontoPagado) 
                        { 
                            if ($resguardo > $Cuo_Numero)
                                {
                                $Cuo_Numeroseleccionada = $row->Cuo_Numero;
                                $resguardo = $Cuo_Numero;
                                $montoacalcular = $Cuo_Monto-$Cuo_MontoPagado;
								$salir = 0;
                                }
                        }
						else {//if ($Cuo_Monto==$Cuo_MontoPagado)
								//$salir = 1;
								}
                       }
             	   If ($salir ==0) {
                    // Genero la tabla que muestra los datos de Alumno
                    foreach ($inscrip2 as $inscri2)
                    {   $Alu_Id = $inscri2->Alu_Id;
                        $Alu_Nombre = $inscri2->Alu_ApeNom;
                        $Cur_Nombre = $inscri2->Cur_Nombre;
						$Alu_DNI = $inscri2->Alu_DNI;
                            
                    }
				
                    
				// aca hace la seleccion de la cuota mas antigua pendiente para la presentacion en la pantalla de los datos        
               $query2 = $this->db->query("SELECT * FROM Cuotas, Alumnos WHERE Cuotas.Cuo_Numero='$Cuo_Numeroseleccionada' AND Cuotas.Alu_Id='$Alu_Id'");     
                        
               foreach ($query2->result_array() as $row) 
                        { 
                         
                         $Cuo_Numero = $row['Cuo_Numero'];
                         $Cuo_FechaVto = $row['Cuo_FechaVto'];
                         $Cuo_Monto = $row['Cuo_Monto'];
                         $Cuo_MontoPagado = $row['Cuo_MontoPagado'];
                         
                         }
               		
                    $this->table->set_empty("&nbsp;");
                    
                    foreach ($inscrip2 as $inscri2)
                    {   $data['inscri2']=$inscri2;
                        $this->table->add_row(
                                            "<b>Alumno: </b>$inscri2->Alu_ApeNom",
                                            "<b>DNI: </b>$inscri2->Alu_DNI",
                                            "<b>Curso: </b>$inscri2->Cur_Nombre",
											"<b>Numero de Cuota: </b>$Cuo_Numero"
											

                                    );
                    }
					$Cuo_FechaVto = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
					$this->table->add_row(
                                           
                                            "<b>Importe: </b>$Cuo_Monto",
                                            "<b>Fecha de Vencimiento: </b>$Cuo_FechaVto",
											"<b>Importe Abonado:</b>$Cuo_MontoPagado"

                                    );
					$puesto=$this->session->userdata('puesto');
			
			
			
			
			
			
           			 $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
					$data['monto']=$Cuo_Monto - $Cuo_MontoPagado;
					$data['Alu_Id']=$Alu_Id;
                    $data['Alu_Nombre']=$Alu_Nombre;
					$data['Ins_Id']=$Ins_ID;
					$data['Alu_DNI']=$Alu_DNI;
                    $data['Cur_Nombre']=$Cur_Nombre;
					$data['Cuo_Numero'] = $Cuo_Numero;
					$data['Cuo_Monto'] = $Cuo_Monto;
					$data['Cuo_MontoPagado'] = $Cuo_MontoPagado;
					$data['Cuo_FechaVto'] = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
					$fecha = date('d-m-Y');
					$data['fecha'] = $fecha;
					$data['table'] = $this->table->generate();
					$data['mensaje'] = "El valor ingresado es: '$monto' y supera lo adeudado - DEUDA TOTAL: '$deuda'";
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/cobro_cuotas', $data, TRUE);
                    $salir = 0;
				 }
//ACA TERMINA EL CONTROL DE LA CARGA DE LOS DATOS					
					//$data['mensaje'] = 'el valor ingresado es 100';
                    //$datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/cobro_cuotas', $data, TRUE);
					}
					else{
					//ARMO LA TABLA CON LAS FACTURAS ADEUDADAS
					$cuotassaldadas= $this->Consultas_model->ListarCuotasaCancelar($Ins_ID,$monto);
        			$parar = 0;
					foreach ($cuotassaldadas as $cuotasaldada){
							$numerocuota = $cuotasaldada->Cuo_Numero; 
							$valorcuota = $cuotasaldada->Cuo_Monto;
							$valorpagado = $cuotasaldada->Cuo_MontoPagado;
							$apagar = $valorcuota - $valorpagado;
							$verestado = $monto - $apagar;
							if (($apagar<>0)AND($parar==0)){							
								if ($verestado<0){$estado = 'Pago Parcial';
												  $parar = 1;
												  $saldo = $monto;}
							    else {if($verestado==0){$estado = 'Cancelado';
												        $parar = 1;
												        $saldo = $apagar;}
									   else {$estado = 'Cancelado';
									   		 $saldo = $apagar;
											 $monto = $monto - $apagar;}
								      }
								
								$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
                                            "<b>Importe: </b>$cuotasaldada->Cuo_Monto",
											"<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto a Aplicar: </b>$saldo",
											"<b> </b>$estado'"
											
            	                                   );
							$data['cunu'] = $cuotasaldada->Cuo_Numero;
							$data['cumo'] = $cuotasaldada->Cuo_Monto;
							$data['cumopa'] = $cuotasaldada->Cuo_MontoPagado;
							$data['cusa'] = $saldo;
							$data['cues'] = $estado;
							
							
							}
						}
					
					//TERMINA EL ARMADO DE LA TABLA CON LAS FACTURAS ADEUDADAS
					
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
					
					$data['table'] = $this->table->generate();
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/confirmacion', $data, TRUE);
                    }//aca finalizo el conrtol del valor ingresado
				}      			
	  }
	 if ($salir == 1){
	 $salir = 0;
	  $data['message']='El Alumno no tiene cuotas pendientes en el Curso seleccionado.';
	  	$datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/error',$data, TRUE);
		
	  } // termina el if de la validacion
	  $this->load->view('templates',$datoPrincipal);         
   } 
     
public function registrar($Ins_ID){
                if($this->caja_abierta()){};
				$control = 1;
				$bandera = 1;
				$control2 = 1;
				$sobrante = 0;
				$resguardo = 100000000;
                $Cuo_Numeroseleccionada = 0;
				$salir = 0;
				$monto = 0;
				
				
				$inscrip2= $this->Cursos_model->buscar_inscripcion($Ins_ID);
				foreach ($inscrip2 as $inscri2)
                    {   $data['Alu_Id']=$inscri2->Alu_Id;
                        $data['Alu_ApeNom']=$inscri2->Alu_ApeNom;
                        $data['Cur_Nombre']=$inscri2->Cur_Nombre;
						$data['Cur_Id']=$inscri2->Cur_Id;
						$data['Dic_Id']=$inscri2->Dic_Id;
                        $data['Alu_DNI']=$inscri2->Alu_DNI;    
                        $nombre = $inscri2->Alu_ApeNom;
					}
				$Cur_Id = $data['Cur_Id'];
				$Dic_Id = $data['Dic_Id'];
				$Alu_Id = $data['Alu_Id'];
				$razonsocial  = $data['Alu_ApeNom'];
                $tipo_desc='Cursos';
                $tipos= $this->Cursos_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
                $gru_desc='Cursos';
				$cur_id=$Cur_Id;
				$dic_id=$Dic_Id;
                $centros=  $this->Cursos_model->centro_costo_curso2($gru_desc,$cur_id,$dic_id);
                foreach ($centros as $centro){
                    $sec=$centro->Sec_Id;
                    $dir=$centro->Dir_Id;
                    $gru=$centro->Gru_Id;
                    $cur=$centro->Cur_Id;
                    $Dic=$centro->Dic_Id;
                }
               
			   //vuelvo  a hacer el calculo para sacar el nro de cuota
				$inscrip= $this->Cursos_model->buscar_cuotaMasAntigua($Ins_ID);
					//del listado de cuotas del alumno, del curso, del dictado, busco las mas anigua pendiente 
                        foreach ($inscrip as $row) 
                        { 
                         $Alu_Id = $row->Alu_Id;
                         $Cuo_Monto = $row->Cuo_Monto;
                         $Cuo_Numero = $row->Cuo_Numero;
                         $Cuo_MontoPagado = $row->Cuo_MontoPagado;
               
                        if ($Cuo_Monto>$Cuo_MontoPagado) 
                        { 
                            if ($resguardo > $Cuo_Numero)
                                {
                                $Cuo_Numeroseleccionada = $row->Cuo_Numero;
                                $resguardo = $Cuo_Numero;
                                $monto = $Cuo_Monto-$Cuo_MontoPagado;
								$salir = 0;
                                }
                        }
						else {if ($Cuo_Monto==$Cuo_MontoPagado)
								$salir = 1;
								}
                    	}

			   	//saco el monto y el monto Abonado
				$montos= $this->Consultas_model->sacardatos($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numeroseleccionada);
					//del listado de cuotas del alumno, del curso, del dictado, busco las mas anigua pendiente 
                        foreach ($montos as $monto) 
                        { 
                         $Cuo_Monto = $monto->Cuo_Monto;
                         $Cuo_MontoPagado = $monto->Cuo_MontoPagado;
						 }
				$comp_nro = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
				$Cuo_Numero = $this->input->post('Cuo_Numero');
				$fecha = $this->input->post('fecha');
				$table = $this->input->post('table');
				
				$cunu = $this->input->post('cunu');
				$cumo = $this->input->post('cumo');
				$cumopa = $this->input->post('cumopa');
				$cusa = $this->input->post('cusa');
				$cues = $this->input->post('cues');
				
				
				//FALTA HACER EL CONTROL DEL BANCO QUE ELIGIO
				
				$bco_id = 9;
				$suc_id = 2;
                $nro_cheque = $this->input->post('nro_cheque');
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
                $fecha=$this->Cursos_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$Dic,$nombre);
                
                $movimientos=$this->Cursos_model->get_id($caj_id,$fecha);
                foreach ($movimientos as $movimiento){
                    $mov_id=$movimiento->Mov_Id;
                }
                
                
                $tipo_comp='RECIBO';
                $this->Cursos_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
                //$this->RendicionesCaja_model->update_ingreso($caj_id,$monto);
                
               // $this->Consultas_model->insertIngreso_movAlu($caj_id,$mov_id,$razonsocial,$Alu_Id);
                
				// aca gaho e lcalculo para grabar las cuotas abonadas
				
				$diferencia = $Cuo_Monto - $Cuo_MontoPagado;
				
				if ($monto<$diferencia){
						$importe = $monto + $Cuo_MontoPagado;
						$PagoCuota = $this->Consultas_model->GrabarPagoCuotas($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero,$caj_id,$mov_id,$importe);
						
							if ($PagoCuota==TRUE){
                    		$data['message']='Error, no guardado.';
							$data['message2']='';
							$data['sobrante'] = '';
               			 	}else{
                    		$data['message'] = '<div class="success">Exito!</div>';
							$data['message2']='';
							$data['sobrante'] = '';} 
						}
				else{
					    if 	($monto==$diferencia){
						$importe = $monto + $Cuo_MontoPagado;
							$PagoCuota = $this->Consultas_model->GrabarPagoCuotas($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero,$caj_id,$mov_id,$importe);
							
								if ($PagoCuota==TRUE){
                    			$data['message']='Error, no guardado.';
								$data['message2']='';
								$data['sobrante'] = '';
               					 }else{
                    			$data['message'] = '<div class="success">Exito!</div>';
								$data['message2']='';
								$data['sobrante'] = '';}
							}
						else{
							$i = 0;
							if ($monto>$diferencia){
						//Controlo si no es la ultima cuota antes de entrar all while
							$Cuo_Numero = $Cuo_Numero + 1;
							$verificarexistencia = 1;
							 $montocontrolar= $this->Consultas_model->sacardatos($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero);
					//del listado de cuotas del alumno, del curso, del dictado, busco las mas anigua pendiente 
                        	foreach ($montocontrolar as $montocon) 
                        	{ 
                        	 $verificarexistencia = $montocon->Cuo_Monto;
                         	}	
								if ($verificarexistencia==1) {
									$bandera = 0;}
								else {
									$control2 = 1;}
							$Cuo_Numero = $Cuo_Numero - 1;
							//si bandera es cero es porque al valor que pago es mas de lo que debo pero no tengo mas ctas ptes	
							if ($bandera == 0)	
							{
								$PagoCuota = $this->Consultas_model->GrabarPagoCuotas($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero,$caj_id,$mov_id,$Cuo_Monto);
								$sobrante = $monto - $diferencia;
								$data['message2']='Se CANCELARON todas las facturas del Alumno...Hay un sobrante de dinero de:  $';
								$data['sobrante'] = $sobrante;
								$data['message'] = '<div class="success">Exito!</div>';
								
							}else{	
							while (($control==1) AND ($control2==1)) {
								$verificarexistencia = 1;
								$Cuo_MontoPagado = $Cuo_Monto;
								$PagoCuota = $this->Consultas_model->GrabarPagoCuotas($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero,$caj_id,$mov_id,$Cuo_MontoPagado);
								
								$monto = $monto - $diferencia;
								$diferencia = $Cuo_Monto;
								$Cuo_Numero = $Cuo_Numero + 1;
																
							//controlo que es la utima cuota y salta del bucle	
								$montocontrolar= $this->Consultas_model->sacardatos($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero);
					       		foreach ($montocontrolar as $montocon) 
                        		{ 
                        		 $verificarexistencia = $montocon->Cuo_Monto;}	
								if ($verificarexistencia==1) {
									$control2 = 0;}
								else {									
									$control2 = 1;}
								
								
								if($diferencia>$monto){
									$control = 0;}
							}	//fin de wile
							if ($control2 == 1)	{			
								$Cuo_MontoPagado = $monto;	
								$PagoCuota = $this->Consultas_model->GrabarPagoCuotas($Cur_Id,$Dic_Id,$Alu_Id,$Cuo_Numero,$caj_id,$mov_id,$Cuo_MontoPagado);
								$data['message'] = '<div class="success">Exito!</div>';
								$data['message2']='';
								$data['sobrante'] = '';}
										
				    			}
							
							if 	($control2==0){
								$data['message'] = '<div class="success">Exito!</div>';
               			 		$data['message2']='Se CANCELARON todas las facturas del Alumno...Hay un sobrante de dinero de:  $';
								$data['sobrante'] = $monto;
							}
						}
					}
		}
		
		//armo la tabla con las cuotas abonadas
		$datossacar= $this->Consultas_model->ListarCuotasSaldadas($caj_id,$mov_id);
        foreach ($datossacar as $datossaco){
				$data['id']=$datossaco->Pag_Id;
		}
		//termino el armado de las cuotas abonadas
       
	  //ARMO LA TABLA CON LAS FACTURAS ADEUDADAS
				    $bumo= $this->Consultas_model->BuscarMonto($caj_id,$mov_id);
        			foreach ($bumo as $bumo2){
								$monto = $bumo2->Mov_Mono;
					}
//	echo $monto;
				
					$cuotassaldadas= $this->Consultas_model->ListarCuotasSaldadasParaImprimir($caj_id,$mov_id);
        			$parar = 0;
					$contar = 0;
					$conteo = 0;
					$dife = 0;
					$dife1 = 0;
					$dife2 = 0;
					foreach ($cuotassaldadas as $cuotasaldada){
								$impcuo = $cuotasaldada->Cuo_Monto;
								$imppag = $cuotasaldada->Cuo_MontoPagado;
								$BA = $impcuo;
								$contar = $contar + $imppag;
								$conteo = $conteo + 1;
								If ($impcuo>$imppag){$estado = 'Pago Parcial';}
								else {$estado = 'Cancelado';}
						}
						if ($conteo>1){$dife1 = $contar - $monto;
										$dife2 = $BA - $dife1;}
						else {$dife = $monto;}
						$parar = 0;
					//echo $BA;
					//echo 'contar';
					//echo $contar ;
					//echo 'diferencia';
					//echo $dife;
						//echo 'diferencia2';
					//echo $dife2;
					//echo 'monto';
					//echo $monto; 
					//echo 'conteo';
					//echo $conteo;
					$contarcuo = 0;
					foreach ($cuotassaldadas as $cuotasaldada){
								$impcuo = $cuotasaldada->Cuo_Monto;
								$imppag = $cuotasaldada->Cuo_MontoPagado;
								$contar = $contar + $imppag;
								If ($impcuo>$imppag){$estado = 'Pago Parcial';}
								else {$estado = 'Cancelado	';}
							if (($conteo == 1) AND ($contarcuo == 0)){ //echo 'entro a este primer if';
							$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
											"<b>Importe: </b>$cuotasaldada->Cuo_Monto",
                                            "<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto Aplicado: </b>$dife",
            	                            "<b>Estado: </b>$estado");	
							}						
							else {if (($contarcuo == 0) AND ($conteo > 1)){ // echo 'entro a este primer if de avajo';
								$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
											"<b>Importe: </b>$cuotasaldada->Cuo_Monto",
                                            "<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto Aplicado: </b>$dife2",
            	                            "<b>Estado: </b>$estado");							
										$contarcuo = 1;
									}else{//echo 'entro a este primer if DE ABAJO';
								$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
											"<b>Importe: </b>$cuotasaldada->Cuo_Monto",
                                            "<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto Aplicado: </b>$cuotasaldada->Cuo_MontoPagado",
            	                            "<b>Estado: </b>$estado");
							}
							}
						
						}
						
						
						
						
						
						
					//TERMINA EL ARMADO DE LA TABLA CON LAS FACTURAS ADEUDADAS
				$data['table'] = $this->table->generate();
				
				//$data['table'] = $table;
		        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
	
    
    function imprimir($Pag_Id){
        //Datos de cliente
		
        $DatosAlumnos= $this->Consultas_model->buscarparaimprimir($Pag_Id);
        foreach ($DatosAlumnos as $DatoAlumno)
        {   
            $cli_nom=$DatoAlumno->Alu_ApeNom;
            $cli_dir=$DatoAlumno->Alu_Direccion;
            $cli_iva= 'Consumidor Final';
            $cli_cuil=$DatoAlumno->Alu_DNI;   
            $caj_id= $DatoAlumno->MovimientoCaja_Caj_Id;
            $mov_id= $DatoAlumno->MovimientoCaja_Mov_Id;
        }
 	//ARMO LA TABLA CON LAS FACTURAS ADEUDADAS
				    $bumo= $this->Consultas_model->BuscarMonto($caj_id,$mov_id);
        			foreach ($bumo as $bumo2){
								$monto = $bumo2->Mov_Mono;
					}
				
					$cuotassaldadas= $this->Consultas_model->ListarCuotasSaldadasParaImprimir($caj_id,$mov_id);
        			$parar = 0;
					$contar = 0;
					$conteo = 0;
					$dife = 0;
					$dife1 = 0;
					$dife2 = 0;
					foreach ($cuotassaldadas as $cuotasaldada){
								$impcuo = $cuotasaldada->Cuo_Monto;
								$imppag = $cuotasaldada->Cuo_MontoPagado;
								$BA = $impcuo;
								$contar = $contar + $imppag;
								$conteo = $conteo + 1;
								If ($impcuo>$imppag){$estado = 'Pago Parcial';}
								else {$estado = 'Cancelado';}
						}
						if ($conteo>1){$dife1 = $contar - $monto;
										$dife2 = $BA - $dife1;}
						else {$dife = $monto;}
						$parar = 0;
					
					$contarcuo = 0;
					foreach ($cuotassaldadas as $cuotasaldada){
								$impcuo = $cuotasaldada->Cuo_Monto;
								$imppag = $cuotasaldada->Cuo_MontoPagado;
								$contar = $contar + $imppag;
								If ($impcuo>$imppag){$estado = 'Pago Parcial';}
								else {$estado = 'Cancelado	';}
							if (($conteo == 1) AND ($contarcuo == 0)){ //echo 'entro a este primer if';
							$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
											"<b>Importe: </b>$cuotasaldada->Cuo_Monto",
                                            "<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto Aplicado: </b>$dife",
            	                            "<b>Estado: </b>$estado");	
											$apli = $dife;
							}						
							else {if (($contarcuo == 0) AND ($conteo > 1)){ echo 'entro a este primer if de avajo';
								$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
											"<b>Importe: </b>$cuotasaldada->Cuo_Monto",
                                            "<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto Aplicado: </b>$dife2",
            	                            "<b>Estado: </b>$estado");							
										$contarcuo = 1;
										$apli = $dife2;
									}else{//echo 'entro a este primer if DE ABAJO';
								$this->table->add_row(
                                            "<b>Numero de Cuota: </b>$cuotasaldada->Cuo_Numero",
											"<b>Importe: </b>$cuotasaldada->Cuo_Monto",
                                            "<b>Monto Pagado: </b>$cuotasaldada->Cuo_MontoPagado",
											"<b>Monto Aplicado: </b>$cuotasaldada->Cuo_MontoPagado",
            	                            "<b>Estado: </b>$estado");
											$apli = $cuotasaldada->Cuo_MontoPagado;
							}
							}
						   		 $cuo =  $cuotasaldada->Cuo_Numero;
								 $val =  $cuotasaldada->Cuo_MontoPagado;
								 $es = $estado;
								 $impor = $cuotasaldada->Cuo_Monto;
						    	 $db_data[] = array(
                                   'numero' => $cuo, 
                                   'valor' => $val,
								   'estado' => $es,
								   'valora' => $apli,
								   'imp' => $impor);
						}
						
						
						
						
						
						
					//TERMINA EL ARMADO DE LA TABLA CON LAS FACTURAS ADEUDADAS
	
		
		
		
		
		
		
		
		
		
				
        $movs= $this->Cursos_model->get_mov($caj_id,$mov_id);
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
"RECIBI de: $cli_nom
DOMICILIO: $cli_dir
CUIT: $cli_cuil                                                                I.V.A.: $cli_iva

FORMA DE PAGO: $formaPago

LA SUMA DE PESOS $numeroTexto.--
----------------------------------------------------------------------------------------------------------------------------------------------------------------
EN CONCEPTO DE $desc.----------------------------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------

CHEQUE Bco.:-------------------------------------------------
CHEQUE N*.:-------------------------------------------------- 

LISTADO DE CUOTAS ABONADAS";
		$this->cezpdf->ezText($content, 10, array('justification' => 'left'));
                $this->cezpdf->ezSetDy(-10);
$content = 


// aca graba los encabezados de la tabla
$col_names = array(
     
			'numero' => 'Numero Cuota',
			'imp' => 'Importe',
			'valor' => 'Monto Pagado',
			'valora' => 'Monto Aplicado',
			'estado' => 'Estado',
			
		);
//con esta sentencia manda la tabla a impresion
$this->cezpdf->ezTable($db_data, $col_names, ' ', array('width'=>500));
		$this->cezpdf->ezStream();
                

	                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = '';                
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
"RECIBI de: $cli_nom
DOMICILIO: $cli_dir
CUIT: $cli_cuil                                                                I.V.A.: $cli_iva";

		$this->cezpdf->ezText($content, 10, array('justification' => 'left'));
                $this->cezpdf->ezSetDy(-10);

$content = 
"
FORMA DE PAGO: $formaPago

LA SUMA DE PESOS $numeroTexto.------------------------------------------------------------------
------------------
EN CONCEPTO DE $desc.-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

CHEQUE Bco.:-------------------------------------------------
CHEQUE N*.:-------------------------------------------------- 

LISTADO DE CUOTAS ABONADAS";
$content =   

				
		
                
              

	                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = 
$this->cezpdf->ezTable($db_data, $col_names, ' ', array('width'=>500));
"";                
                $this->cezpdf->ezText($content, 8, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = "TOTAL $         $monto";
                $this->cezpdf->ezText($content, 15, array('justification' => 'right'));
                $this->cezpdf->ezSetDy(-60);
                
        $this->cezpdf->ezStream();
    }



 function estadocuenta($Ins_ID)
	{
			if($this->caja_abierta()){};
			$query = $this->db->query("SELECT Alumnos.Alu_Id, Inscripciones.DictadoDic_Id FROM Inscripciones, Alumnos  WHERE Inscripciones.Alu_Id=Alumnos.Alu_Id AND Inscripciones.Ins_ID='$Ins_ID'"); 
                        foreach ($query->result_array() as $row) 
                        { 
                        
                         $Alu_Id = $row['Alu_Id'];
						 $Dic_Id = $row['DictadoDic_Id'];
                         
                         }
          $data['Dic_Id']=$Dic_Id;      
                $persons = $this->Consultas_model->get_by_id2($Ins_ID);// ->result();
               
		// generate paginationos
		$this->load->library('pagination');
		$config['base_url'] = site_url('cursos/index/');
 		$config['total_rows'] = $this->Consultas_model->count_all();
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso', 'Alumno', 'Inscripcion', 'Forma Pago', 'Valor Cuota', 'Nro Cuotas', 'Accion');
   
		foreach ($persons as $person)
		{
			$this->table->add_row( $person->Cur_Nombre, $person->Alu_ApeNom, date('d-m-Y',strtotime($person->Ins_Fecha)), $person->For_Descripcion, $person->For_MontoCuota, $person->ForCuotas,
               anchor('cursos/estadocuenta2/'.$person->Ins_ID,'Ver',array('class'=>'view'))//.' '.
			  );
                     
		}
                $data['table'] = $this->table->generate();
                 // load view
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/EstadodeCuenta', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
   }
        
 
  function estadocuenta2($Ins_ID)
	{
		if($this->caja_abierta()){};
 		$data['id']=$Ins_ID;
 		$persons = $this->Consultas_model->get_by_id2($Ins_ID);// ->result();
             
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('cursos/index/');
 		$config['total_rows'] = $this->Consultas_model->count_all(); 		
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso', 'Alumno', 'Inscripcion', 'Forma Pago', 'Valor Cuota', 'Nro Cuotas', 'Acciones');
   
		foreach ($persons as $person)
		{
			 $this->table->add_row($person->Cur_Nombre,
							  $person->Alu_ApeNom,
							  date('d-m-Y',strtotime($person->Ins_Fecha)),
							  $person->For_Descripcion,
							  $person->For_MontoCuota,
							  $person->ForCuotas,
						  anchor('cursos/cobrar/'.$person->Ins_ID,'Cobrar',array('class'=>'money'))
			 );
						 $Alu_Id = $person->Alu_Id;
						 
						 $Cur_Id = $person->DictadoCur_Id;
						 
                         
	     }
					 
			
         $data['table'] = $this->table->generate();
		 $query = $this->db->query("SELECT * FROM Cuotas 	WHERE  Cuotas.Cur_Id='$Cur_Id' AND Cuotas.Alu_Id='$Alu_Id'"); 
                        foreach ($query->result_array() as $row) 
                        { 
                        
                         $Alu_Id = $row['Alu_Id'];
						 $Cuo_Numero = $row['Cuo_Numero'];
						 $Valor = $row['Cuo_Monto'];
						 $Vencimiento = $row['Cuo_FechaVto'];
						 $Cuo_MontoPagado = $row['Cuo_MontoPagado'];
						 $this->table->add_row($Cuo_Numero, $Valor, date('d-m-Y',strtotime($Vencimiento)), $Cuo_MontoPagado);
                         
                         }
		$this->table->set_heading('Nro Cuota', 'Valor', 'Fecha de Vencimiento', 'MontoPagado');
                
     	foreach ($persons as $person)
		{
			//$this->table->add_row($Cuo_Numero, $Valor, date('d-m-Y',strtotime($Vencimiento)), $Cuo_MontoPagado);
        }
       
		$data['table1'] = $this->table->generate();
		$data['dato'] = $Alu_Id;
 		
		// load view
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cursos/EstadodeCuenta2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
 
 }
}

?>