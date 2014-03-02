<?php

class otros_Ingresos extends MY_Controller {
     
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        if($this->caja_abierta()){};
        
	$clientes = $this->Clientes_model->search_cliente('%');
        if(! $clientes){
            $data['table']='<p style="text-align: left">No se encontraron clientes.</p>';
            
        }else{		

	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Razón Social','DNI CUIL/CUIT','Acciones');
	$i = 0;
	foreach ($clientes as $cliente)
	{
		$this->table->add_row(++$i, $cliente->Cli_RazonSocial,
                              $cliente->Cli_Documento,
                              
                              anchor('otros_ingresos/cobrar/'.$cliente->Cli_Id,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }    
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_ingresos/otros_ingresos', $data, TRUE);
  
        $this->load->view('templates',$datoPrincipal);
    } 
    
    public function buscar(){
        if($this->caja_abierta()){};
	$query = $this->input->post('cliente');
        $clientes = $this->Clientes_model->search_cliente($query);
	if(! $clientes){
            $data['table']="<p style='text-align: left'>No se encontró cliente $query.</p>";
            
        }else{			
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Apellido y Nombre','DNI CUIL/CUIT','Acciones');
	$i = 0;
	$this->table->set_heading(' ','Razón Social','DNI CUIL/CUIT','Acciones');
	$i = 0;
	foreach ($clientes as $cliente)
	{
		$this->table->add_row(++$i, $cliente->Cli_RazonSocial,
                              $cliente->Cli_Documento,
                              
                              anchor('otros_ingresos/cobrar/'.$cliente->Cli_Id,'Cobrar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }      		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_ingresos/otros_ingresos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    
    public function cobrar($cli_id){
    
        if($this->caja_abierta()){}
        $data['id']=$cli_id;  
        $now= now();
        $fecha=  unix_to_human($now);
        $hoy=date('d-m-Y',strtotime($fecha));
        $data['hoy']= $hoy;
        $this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
        $this->form_validation->set_rules('fecha','Fecha','required|trim|xss_clean');
        $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
        $this->form_validation->set_rules('descripcion','Descripción','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
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
            $data['rbo_nro']=$this->MovimientosCaja_model->recibo_nro($puesto);
            
            $clientes= $this->Clientes_model->get_by_id($cli_id);

            // generate table data
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");

            foreach ($clientes as $cliente)
            {   
                $data['titular']=$cliente->Cli_RazonSocial;
                $this->table->add_row(
                                    "<b>Nombre: </b>$cliente->Cli_RazonSocial",
                                    "<b>CUIL: </b>$cliente->Cli_Documento"
                            );
            }
            $data['table'] = $this->table->generate();
            $this->load->model('MovimientosCaja_model');
            $rows= $this->MovimientosCaja_model->centro_costo_todas_sec();
            foreach ($rows as $row) {
                            $centros[$row->Sec_Descripcion] = $row->Sec_Descripcion;
                    }
            $data['centros']=$centros;

            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_ingresos/cobro_otros_ingresos', $data, TRUE);
        }else{
                $comp_nro = $this->input->post('comp_nro');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('descripcion');
                $monto = $this->input->post('monto');
                $fecha=  $this->input->post('fecha');
                $centro=  $this->input->post('centros');

                $clientes= $this->Clientes_model->get_by_id($cli_id);

                    // Genero la tabla que muestra los datos de cliente
                foreach ($clientes as $cliente)
                    {   $data['cli_id']=$cliente->Cli_Id;
                        $data['apeNom']=$cliente->Cli_RazonSocial;
                        $data['dir']=$cliente->Cli_Direccion;
                        $data['iva']=$cliente->Cli_cond_iva;
                        $data['cuil']=$cliente->Cli_Documento;    
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
                    $data['centro']=$centro;
                    //$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_ingresos/confirmacion', $data, TRUE);
        }
        $this->load->view('templates',$datoPrincipal);  
    }
    
    function registrar ($cli_id){
        if($this->caja_abierta()){}
        $data['id']=$cli_id; 
        $razonsocial = $this->input->post('apeNom');
        $comp_nro = $this->input->post('comp_nro');
        $formaPago = $this->input->post('formaPago');
        $cc = $this->input->post('centro');
        $desc = $this->input->post('desc');
        $monto = $this->input->post('monto');
        $fecha = $this->input->post('fecha');
        $tipo_desc='Servicios a Terceros';
                $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
        
        $centros=  $this->MovimientosCaja_model->centro_costo_sec($cc);
        foreach ($centros as $centro){
                    $sec=$centro->Sec_Id;
                    $dir=$centro->Dir_Id;
                    $gru=$centro->Gru_Id;
                    $cur=$centro->Cur_Id;
                    $Dic=$centro->Dic_Id;
        }
        
        $caj_id=$this->session->userdata('caja_id');
        
        $mov_id=$this->MovimientosCaja_model->insert($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'TRUE',$sec,$dir,$gru,$cur,$Dic,$razonsocial);
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
        
        $ids = $this->otrosIngresos_model->max_id();
        foreach ($ids as $id){
            $i_id=$id->id;
        }
	
        $ingreso = $this->otrosIngresos_model->insert($i_id,$caj_id,$mov_id,$cli_id);
        $data['mov_id']=$mov_id;
        if ($ingreso==TRUE){
            $data['message']='<div class="success">Exito!</div>';
        }else{
            $data['message']='Error, no guardado.';
        }
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_ingresos/imprimir', $data, TRUE);
        
        $this->load->view('templates',$datoPrincipal);        
       
    }
    public function imprimir($mov_id){
        if($this->caja_abierta()){};
        $servicios= $this->otrosIngresos_model->buscar_mov($mov_id);
        foreach ($servicios as $servicio)
        {   
            $cli_nom=$servicio->Cli_RazonSocial;
            $cli_dir=$servicio->Cli_Direccion;
            $cli_iva=$servicio->Cli_cond_iva;
            $cli_cuil=$servicio->Cli_Documento;   
            $caj_id= $servicio->Caj_Id;
            $mov_id= $servicio->Mov_Id;
            $fecha= date('d-m-Y',strtotime($servicio->Mov_FechaHora));
            $formaPago= $servicio->Mov_FormaDePago;
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
            $cheques=$this->Cheque_model->get_cheque_arreglo($mov_id);
            foreach($cheques as $cheque){
                $banco=$cheque->Banco_Nombre;
                
                $nro_cheque=$cheque->Cheq_Nro;
                $monto_cheque=$cheque->Cheq_Monto;
            }
        }
        
        $numeroTexto=  $this->MovimientosCaja_model->numerotexto($monto);

        $this->load->library('cezpdf');
		$this->load->helper('pdf');
		
                $this->cezpdf->ezText('RECIBO [C]', 12, array('justification' => 'center'));
		$this->cezpdf->ezSetDy(-10);
                
$content = 
"N* 0001-00076831
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
"N* 0001-00076831
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
