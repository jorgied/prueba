<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Radios extends MY_Controller {
    
    public function index(){
        if($this->caja_abierta()){}
        $ventas = $this->Radios_model->buscar_venta('%');
	if(! $ventas){
            $data['table']='<p style="text-align: left">No se encontraron clientes con cobro de radio pendiente.</p>';
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        //Concepto es vent_radio_tipo
        $this->table->set_heading(' ','Razón Social','CUIL/CUIT','Concepto','Fecha Desde','Fecha Hasta','Importe','Monto Pendiente','Acciones');
	$i = 0;
	foreach ($ventas as $venta)
	{
            if ($venta->Vent_Rad_Tipo == 1){
                $tipo='Espacio en radio';
            }else{
                $tipo='Publicidad en radio';
            }   
            $saldo=$venta->Ven_Rad_Monto - $venta->Ven_Rad_MontoPagado;
            if ($saldo<>0){
			$this->table->add_row(++$i, $venta->Cli_RazonSocial,
                              $venta->Cli_Documento,
                              $tipo,
                              date('d-m-Y',strtotime($venta->Ven_Rad_Desde)),
                              date('d-m-Y',strtotime($venta->Ven_Rad_Hasta)),
							  $venta->Ven_Rad_Monto,
                              " $$saldo",
                              anchor('radios/cobrar/'.$venta->Ven_Rad_Id,'Cobrar',array('class'=>'money'))
                        );
			}
	}
	$data['table'] = $this->table->generate();
        }
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('radios/radios', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function buscar(){
       if($this->caja_abierta()){}
        $cli_nom = $this->input->post('cliente');
        $ventas = $this->Radios_model->buscar_venta($cli_nom);
	if(! $ventas){
            $data['table']="<p style='text-align: left'>No se encontró cliente $cli_nom con cobro de radio pendiente.</p>";
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        //Concepto es vent_radio_tipo
        $this->table->set_heading(' ','Razón Social','CUIL/CUIT','Concepto','Fecha Desde','Fecha Hasta','Acciones');
	$i = 0;
	foreach ($ventas as $venta)
	{
            if ($venta->Vent_Rad_Tipo == 1){
                $tipo='Espacio en radio';
            }else{
                $tipo='Publicidad en radio';
            }
		$this->table->add_row(++$i, $venta->Cli_RazonSocial,
                              $venta->Cli_Documento,
                              $tipo,
                              date('d-m-Y',strtotime($venta->Ven_Rad_Desde)),
                              date('d-m-Y',strtotime($venta->Ven_Rad_Hasta)),
                              anchor('radios/cobrar/'.$venta->Ven_Rad_Id,'Cobrar',array('class'=>'money'))
                        );
	}
	$data['table'] = $this->table->generate();
        //$this->form_data->dob = date('d-m-Y',strtotime($person->dob));
        }
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('radios/radios', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    public function cobrar($radio_id){
        if($this->caja_abierta()){}
        $data['id']=$radio_id; 
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
            $data['id']=$radio_id;
            $ventas= $this->Radios_model->buscar($radio_id);
            $puesto=$this->session->userdata('puesto');
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);

            // generate table data
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");

            foreach ($ventas as $venta)
            {   
                $data['venta']=$venta;
                $this->table->add_row(
                                    "<b>Nombre: </b>$venta->Cli_RazonSocial",
                                    "<b>CUIL: </b>$venta->Cli_Documento"
                            );
            }
            $data['table'] = $this->table->generate();

            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('radios/cobro_radio', $data, TRUE);
        }else{
                    $fecha = $this->input->post('fecha');
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $this->input->post('monto');

                    $ventas= $this->Radios_model->buscar($radio_id);

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
                    $data['fecha']=$fecha;
                    $data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
                    //$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('radios/confirmacion', $data, TRUE);
                    }
                    $this->load->view('templates',$datoPrincipal);    
            
        
    }
    function registrar($radio_id){
        if($this->caja_abierta()){};
        $data['id']=$radio_id;  
        
        $fecha = $this->input->post('fecha');
        $comp_nro = $this->input->post('comp_nro');
        $formaPago = $this->input->post('formaPago');
        $desc = $this->input->post('desc');
        $monto = $this->input->post('monto');
        $tipo_desc='Radio';
                $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
       
		 $query2 = $this->db->query("SELECT * FROM grupos WHERE  grupos.Gru_Descripcion = 'Radio'");     			
		 	 foreach ($query2->result_array() as $row) 
                        {		    $sec = $row['Sec_Id'];
						 			$dir = $row['Dir_Id'];
						 			$gru = $row['Gru_Id'];
					    }
			$cur = 0;
			$dic = 0;
			
        $caj_id=$this->session->userdata('caja_id');
        $clientes=$this->Radios_model->buscar_cliente($radio_id);
        foreach ($clientes as $cliente){
            $cli_id=$cliente->Cli_Id;
            $razonsocial=$cliente->Cli_RazonSocial;    
        }
        
		$fecha = date('Y-m-d',strtotime($fecha));
		
        $mov_id=$this->MovimientosCaja_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'TRUE',$sec,$dir,$gru,$cur,$dic,$razonsocial);
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
		 $query2 = $this->db->query("SELECT * FROM MovimientosCaja 
                WHERE Caj_Id='$caj_id'");     
           			 foreach ($query2->result_array() as $row) 
                        {  $mov_id = $row['Mov_Id'];}
        $tipo_comp='RECIBO';
        $this->MovimientosCaja_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
        $this->RendicionesCaja_model->update_ingreso($caj_id,$monto);
                
	$radio = $this->Radios_model->update_venta($radio_id,$monto);
        $radio = $this->Radios_model->insert_pago($radio_id,$caj_id,$mov_id);
        $data['mov_id']=$mov_id;
        if ($radio==TRUE){
            $data['message']='<div class="success">Exito!</div>';
        }else{
            $data['message']='Error, no guardado.';
        }
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('radios/imprimir', $data, TRUE);
        
        $this->load->view('templates',$datoPrincipal);        
    }
    
    function imprimir($mov_id){
        if($this->caja_abierta()){};
        //Datos de cliente
           $radios= $this->Radios_model->buscar_pago($mov_id);
        foreach ($radios as $radio)
        {   
            $cli_nom=$radio->Cli_RazonSocial;
			echo $cli_nom;
            $cli_dir=$radio->Cli_Direccion;
			echo $cli_dir;
            $cli_iva=$radio->Cli_cond_iva;
			echo $cli_iva;
            $cli_cuil=$radio->Cli_Documento;
			echo $cli_cuil;  
            $caj_id= $radio->MovimientoCaja_Caj_Id;
			echo $caj_id;
            $mov_id= $radio->Mov_Id;
			echo $mov_id;
            $fecha= date('d-m-Y',strtotime($radio->Mov_FechaHora));
			echo $fecha;
            $fPago= $radio->Mov_FormaDePago;
			echo $fPago;
            $monto= $radio->Mov_Mono;
			echo $monto;
            $desc= $radio->Mov_Descripcion;
			echo $desc;
        }
        
        $movs= $this->MovimientosCaja_model->get_mov($caj_id,$mov_id);
        foreach ($movs as $mov){
            $fecha= date('d-m-Y',strtotime($mov->Mov_FechaHora));
            $fPago= $mov->Mov_FormaDePago;
            $monto= $mov->Mov_Mono;
            $desc= $mov->Mov_Descripcion;
        }
        $banco=' ';
            $nro_cheque=' ';
            $monto_cheque=' ';
        if($fPago==1){
            $formaPago='Contado';
            
            
        }else{
            $formaPago='Cheque';
            $cheques=  $this->Cheque_model->get_cheque(TRUE,$caj_id,$mov_id);
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
                
        $this->cezpdf->ezStream();
    }
}


?>
