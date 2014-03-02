<?php

class alquileres extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
    }
    
   public function index(){
        if($this->caja_abierta()){};
        $alquileres = $this->Alquileres_model->buscar_cliente('%');
	if(! $alquileres){
            $data['table']='<p style="text-align: left">No se encontraron clientes con alquiler pendiente de cobro.</p>';
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Razón Social','CUIL/CUIT','Lugar de Alquiler','Fecha Desde','Fecha Hasta','Importe','Acciones');
	$i = 0;
	foreach ($alquileres as $alquiler)
	{
		$this->table->add_row(++$i, $alquiler->Cli_RazonSocial,
                              $alquiler->Cli_Documento,
                              $alquiler->Rec_Nombre,
                              date('d-m-Y',strtotime($alquiler->Alq_FechaDesde)),
                              date('d-m-Y',strtotime($alquiler->Alq_FechaHasta)),
                              $alquiler->Alq_Monto,
                              anchor('alquileres/cobrar/'.$alquiler->Alq_Id,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        } 
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('alquileres/alquileres', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function buscar(){
       if($this->caja_abierta()){};
	$query = $this->input->post('cliente');
        
	$alquileres = $this->Alquileres_model->buscar_cliente($query);
	if(! $alquileres){
            $data['table']="<p style='text-align: left'>No se encontró cliente $query con alquiler pendiente de cobro.</p>";
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Razón Social','CUIL/CUIT','Lugar de Alquiler','Fecha Desde','Fecha Hasta','Acciones');
	$i = 0;
	foreach ($alquileres as $alquiler)
	{
		$this->table->add_row(++$i, $alquiler->Cli_RazonSocial,
                              $alquiler->Cli_Documento,
                              $alquiler->Rec_Nombre,
                              date('d-m-Y',strtotime($alquiler->Alq_FechaDesde)),
                              date('d-m-Y',strtotime($alquiler->Alq_FechaHasta)),
                              anchor('alquileres/cobrar/'.$alquiler->Alq_Id,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        
        }	
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('alquileres/alquileres', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar($alq_id){
        
                if($this->caja_abierta()){};
                
                $data['id']=$alq_id;
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
                    $puesto=$this->session->userdata('puesto');
                    $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
                    
                    $alquileres= $this->Alquileres_model->buscar($alq_id);

                    $this->table->set_empty("&nbsp;");
                    $i = 0;
                    foreach ($alquileres as $alquiler)
                    {   $data['alquiler']=$alquiler;
                        $this->table->add_row(
                                            "<b>Nombre: </b>$alquiler->Cli_RazonSocial",
                                            "<b>CUIL/CUIT: </b>$alquiler->Cli_Documento"

                                    );
                    }
                    $data['table'] = $this->table->generate();
                    
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('alquileres/cobro_alquiler', $data, TRUE);
                }else{
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $this->input->post('monto');
                    $fecha=  $this->input->post('fecha');

                    $alquileres= $this->Alquileres_model->buscar($alq_id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($alquileres as $alquiler)
                    {   $data['cli_id']=$alquiler->Cli_Id;
                        $data['apeNom']=$alquiler->Cli_RazonSocial;
                        $data['dir']=$alquiler->Cli_Direccion;
                        $data['iva']=$alquiler->Cli_cond_iva;
                        $data['cuil']=$alquiler->Cli_Documento;    
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
                    //$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('alquileres/confirmacion', $data, TRUE);
                    }
                    $this->load->view('templates',$datoPrincipal);
                
    }
    
public function registrar($alq_id){
                if($this->caja_abierta()){};
                
                $data['id']=$alq_id;
    
                //$tipo_desc='Alquiler';
               // $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                //foreach ($tipos as $tipo){
                 //   $tm=$tipo->TipMov_Id;
               // }
			$tipo_desc='Alquiler';
                $tipos= $this->Anticipos_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
				
                $gru_desc='Aula Magna';
                $centros=  $this->MovimientosCaja_model->centro_costo_grupo($gru_desc);
                foreach ($centros as $centro){
                    $sec=$centro->Sec_Id;
                    $dir=$centro->Dir_Id;
                    $gru=$centro->Gru_Id;
                    $cur=$centro->Cur_Id;
                    $Dic=$centro->Dic_Id;
                }
				
                $cli_id = $this->input->post('cli_id');
                $razonsocial = $this->input->post('apeNom');
                $comp_nro = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
                $fecha = $this->input->post('fecha');
                
                //cambiar caj_id cuando tengamos sesiones
                
                
                $caj_id=$this->session->userdata('caja_id');
				$fecha = date('Y-m-d',strtotime($fecha));
				
                $fecha=$this->MovimientosCaja_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'TRUE',$sec,$dir,$gru,$cur,$Dic,$razonsocial);
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
                
                $alquiler = $this->Alquileres_model->update($alq_id,$caj_id,$mov_id);
                //Armo la vista
                if ($alquiler==FALSE){
                    $data['message']='Error, no guardado.';
                }else{
                    $data['message'] = '<div class="success">Exito!</div>';
                
                }
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('alquileres/imprimir', $data, TRUE);
                
                $this->load->view('templates',$datoPrincipal);      
    }
    
    function imprimir($alq_id){
        if($this->caja_abierta()){};
        //Datos de cliente
        $alquileres= $this->Alquileres_model->buscar($alq_id);
        foreach ($alquileres as $alquiler)
        {   
            $cli_nom=$alquiler->Cli_RazonSocial;
            $cli_dir=$alquiler->Cli_Direccion;
            $cli_iva=$alquiler->Cli_cond_iva;
            $cli_cuil=$alquiler->Cli_Documento;   
            $caj_id= $alquiler->MovimientoCaja_Caj_Id;
            $mov_id= $alquiler->MovimientoCaja_Mov_Id;
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