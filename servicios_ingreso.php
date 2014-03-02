<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Servicios_ingreso extends MY_Cajero {
    
    public function index(){
        $clientes = $this->ServiciosIngreso_model->buscar_clientes('%');
	if(! $clientes){
            $data['table']='<p style="text-align: left">No se encontraron clientes con servicio pendiente de cobro.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Apellido y Nombre','CUIL/CUIT','Servicio','Fecha','Monto','Acciones');
	$i = 0;
	foreach ($clientes as $cliente)
	{
		$this->table->add_row(++$i, $cliente->Cli_RazonSocial,
                              $cliente->Cli_Documento,
                              $cliente->Serv_Nombre,
                              date('d-m-Y',strtotime($cliente->Serv_Fecha)),
							  $cliente->Ser_Monto,
                              anchor('servicios_ingreso/cobrar/'.$cliente->Serv_Id,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        	
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosIngreso/servicios', $data, TRUE);
  
        $this->load->view('templates',$datoPrincipal);
    }    
    public function buscar(){
       
    $query = $this->input->post('cliente');
        $clientes = $this->ServiciosIngreso_model->buscar_clientes($query);
	if(! $clientes){
            $data['table']="<p style='text-align: left'>No se encontró cliente $query con servicio pendiente de cobro.</p>";
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Apellido y Nombre','CUIL/CUIT','Servicio','Fecha','Acciones');
	$i = 0;
	foreach ($clientes as $cliente)
	{
		$this->table->add_row(++$i, $cliente->Cli_RazonSocial,
                              $cliente->Cli_Documento,
                              $cliente->Serv_Nombre,
                              date('d-m-Y',strtotime($cliente->Serv_Fecha)),
                              anchor('servicios_ingreso/cobrar/'.$cliente->Serv_Id,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        	
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosIngreso/servicios', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    public function cobrar($serv_id){
        
        $now= now();
        $fecha=  unix_to_human($now);
        $hoy=date('d-m-Y',strtotime($fecha));
        $data['hoy']= $hoy;
        $this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
        $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
        $this->form_validation->set_rules('descripcion','Descripción','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
        $this->form_validation->set_rules('fecha','Fecha','required|trim|xss_clean');
         // validacion del chequeForm
        if ($this->input->post('formaPago')=='Cheque'){
                    $this->form_validation->set_rules('banco','banco','required|trim|xss_clean');
                    $this->form_validation->set_rules('sucursal','sucursal','required|trim|xss_clean');
                    $this->form_validation->set_rules('titular','titular','required|trim|xss_clean');
                    $this->form_validation->set_rules('numero_cheque','numero_cheque','required|trim|xss_clean');
                    $this->form_validation->set_rules('monto_cheque','monto_cheque','required|trim|xss_clean');

        }
        if ($this->form_validation->run() == FALSE){
            $data['id']=$serv_id;
            $clientes= $this->ServiciosIngreso_model->buscar_servicio($serv_id);
            $puesto=$this->session->userdata('puesto');
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);

            // generate table data
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");

            foreach ($clientes as $cliente)
            {   $data['cliente']=$cliente;
                $this->table->add_row(
                                    "<b>Nombre: </b>$cliente->Cli_RazonSocial",
                                    "<b>CUIL/CUIT: </b>$cliente->Cli_Documento"
                        );
            }
            $data['table'] = $this->table->generate();

            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosIngreso/cobro_servicio', $data, TRUE);
        }else{
                    $data['id']=$serv_id;
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $this->input->post('monto');
                    $fecha=  $this->input->post('fecha');

                    $ventas= $this->ServiciosIngreso_model->buscar_servicio($serv_id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($ventas as $venta)
                    {   $data['cli_id']=$venta->Cli_Id;
                        $data['apeNom']=$venta->Cli_RazonSocial;
                        $data['dir']=$venta->Cli_Direccion;
                        $data['iva']=$venta->Cli_cond_iva;
                        $data['cuil']=$venta->Cli_Documento;    
                    }
                    if($formaPago=='Cheque'){
                        
                         $bco = $this->input->post('banco');
                         $suc = $this->input->post('sucursal');
                         $titular = $this->input->post('titular');
                         $fecha_emitido = $this->input->post('emitido');
                         $fecha_acobrar = $this->input->post('acobrar');
                         $nro_cheque = $this->input->post('numero_cheque');
                         $monto_cheque = $this->input->post('monto_cheque');
                         
                         $nombres=  $this->Cheque_model->nombres_bancosuc($bco,$suc);
                         foreach ($nombres as $nombre){
                             $banco=$nombre->Banco_Nombre;
                             $sucursal=$nombre->Suc_Nombre;
                         }
                         
                         $data['banco'] = $banco;
                         $data['sucursal'] = $sucursal;
                         $data['bco_id'] = $bco;
                         $data['suc_id'] = $suc;
                         $data['titular'] = $titular;
                         $data['emitido']=$fecha_emitido;
                         $data['acobrar']=$fecha_acobrar;
                         $data['numero_cheque']=$nro_cheque;
                         $data['monto_cheque'] = $monto_cheque;
                        
                    }
                    // Genero la tabla que muestra el detalle del movimiento
                    $data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
                    $data['fecha']=$fecha;
                    
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosIngreso/confirmacion', $data, TRUE);
                    }
                    $this->load->view('templates',$datoPrincipal);  
        
    }
    
    function registrar($serv_id){
            
            $data['id']=$serv_id;    
            $comp_nro = $this->input->post('comp_nro');
            $formaPago = $this->input->post('formaPago');
            $desc = $this->input->post('desc');
            $monto = $this->input->post('monto');
            $fecha = $this->input->post('fecha');
            $tipo_desc='Servicios a Terceros';
                    $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                    foreach ($tipos as $tipo){
                        $tm=$tipo->TipMov_Id;
                    }
            $servicios= $this->ServiciosIngreso_model->buscar_servicio($serv_id);
                foreach ($servicios as $servicio){
                    $gru_desc=$servicio->Serv_Nombre;
                }
            
            $centros=  $this->MovimientosCaja_model->centro_costo_grupo($gru_desc);
            foreach ($centros as $centro){
                        $sec=$centro->Sec_Id;
                        $dir=$centro->Dir_Id;
                        $gru=$centro->Gru_Id;
                        $cur=$centro->Cur_Id;
                        $Dic=$centro->Dic_Id;
            }

            $caj_id=$this->session->userdata('caja_id');
            $clientes=$this->ServiciosIngreso_model->buscar_servicio($serv_id);
            foreach ($clientes as $cliente){
                $cli_id=$cliente->Cli_Id;
                $razonsocial=$cliente->Cli_RazonSocial;

            }
            $mov_id=$this->MovimientosCaja_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'TRUE',$sec,$dir,$gru,$cur,$Dic,$razonsocial);
			$query2 = $this->db->query("SELECT * FROM MovimientosCaja 
                WHERE Caj_Id='$caj_id'");     
           			 foreach ($query2->result_array() as $row) 
                        {  $mov_id = $row['Mov_Id'];}
            if($formaPago=='Cheque'){
                    $banco = $this->input->post('bco_id');
                    $sucursal = $this->input->post('suc_id');
                    $titular = $this->input->post('titular');
                    $fecha_emitido = $this->input->post('emitido');
                    $fecha_acobrar = $this->input->post('acobrar');
                    $nro_cheque = $this->input->post('numero_cheque');
                    $monto_cheque = $this->input->post('monto_cheque');

                    $this->Cheque_model->alta_cheque_tercero($nro_cheque,$titular,$monto_cheque,$fecha_emitido,$fecha_acobrar,$banco,$sucursal,$caj_id,$mov_id);
                }
            $tipo_comp='RECIBO';
            $this->MovimientosCaja_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
            $this->RendicionesCaja_model->update_ingreso($caj_id,$monto);
            
            $servicio = $this->ServiciosIngreso_model->update($serv_id,$caj_id,$mov_id);
            
            $data['mov_id']=$mov_id;
            if ($servicio==TRUE){
                $data['message']='<div class="success">Exito!</div>';
            }else{
                $data['message']='Error, no guardado.';
            }
            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosIngreso/imprimir', $data, TRUE);

        
        $this->load->view('templates',$datoPrincipal);       
    }
    
    public function imprimir($mov_id){
         //Datos de cliente
        $servicios= $this->ServiciosIngreso_model->buscar_mov($mov_id);
        foreach ($servicios as $servicio)
        {   
            $cli_nom=$servicio->Cli_RazonSocial;
            $cli_dir=$servicio->Cli_Direccion;
            $cli_iva=$servicio->Cli_cond_iva;
            $cli_cuil=$servicio->Cli_Documento;   
            $caj_id= $servicio->Caj_Id;
            $mov_id= $servicio->Mov_Id;
            $fecha= date('d-m-Y',strtotime($servicio->Mov_FechaHora));
            $fPago= $servicio->Mov_FormaDePago;
            $monto= $servicio->Mov_Mono;
            $desc= $servicio->Mov_Descripcion;
        }
        $banco=' ';
            $nro_cheque=' ';
            $monto_cheque=' ';
        if($fPago==1){
            $formaPago='Contado';
            
            
        }else{
            $formaPago='Cheque';
            $cheques= $this->Cheque_model->get_cheque(TRUE,$caj_id,$mov_id);
            foreach($cheques as $cheque){
                $banco=$cheque->Banco_Nombre;
                $nro_cheque=$cheque->Cheq_Nro;
                $monto_cheque=$cheque->Cheq_Monto;
            }
        }
        
        $numeroTexto=  $this->MovimientosCaja_model->numerotexto($monto);
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


LA SUMA DE PESOS $numeroTexto.--
----------------------------------------------------------------------------------------------------------------------------------------------------------------

EN CONCEPTO DE $desc --
----------------------------------------------------------------------------------------------------------------------------------------------------------------
";
                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);


$content = 
"
CHEQUE c/BCO.: $banco.--
Nro.: $nro_cheque.--
$ $monto_cheque.--";                
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


LA SUMA DE PESOS $numeroTexto.--
----------------------------------------------------------------------------------------------------------------------------------------------------------------

EN CONCEPTO DE $desc --
----------------------------------------------------------------------------------------------------------------------------------------------------------------
";          $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);


$content = 
"
CHEQUE c/BCO.: $banco.--
Nro.: $nro_cheque.--
$ $monto_cheque.--";               
                $this->cezpdf->ezText($content, 8, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = "TOTAL $         $monto";
                $this->cezpdf->ezText($content, 15, array('justification' => 'right'));
                $this->cezpdf->ezSetDy(-60);
                
        $this->cezpdf->ezStream();
    }

    
}

?>
