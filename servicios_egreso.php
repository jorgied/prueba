<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Servicios_egreso extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('ServiciosEgreso_model','',TRUE);
    }

    public function index(){
        if($this->caja_abierta()){}
       
        $clientes = $this->ServiciosEgreso_model->buscar_clientes2();
	if(! $clientes){
            $data['table']='<p>No encontrado.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Apellido y Nombre','CUIL/CUIT','Servicio','Fecha','Acciones');
	$i = 0;
	foreach ($clientes as $cliente)
	{
		$this->table->add_row($cliente->Cli_ApeNom,
                              $cliente->Cli_CUIL,
                              $cliente->Serv_Nombre,
                              date('d-m-Y',strtotime($cliente->Serv_Fecha)),
                              anchor('servicios_egreso/cobrar/'.$cliente->Serv_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        	
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosEgreso/servicios', $data, TRUE);
  
        $this->load->view('templates',$datoPrincipal);
    }    
    public function buscar(){
       if($this->caja_abierta()){}
        
	$query = $this->input->post('cliente');
        $clientes = $this->ServiciosEgreso_model->buscar_clientes($query);
	if(! $clientes){
            $data['table']='<p>No encontrado.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Apellido y Nombre','CUIL/CUIT','Servicio','Fecha','Acciones');
	$i = 0;
	foreach ($clientes as $cliente)
	{
		$this->table->add_row($cliente->Cli_ApeNom,
                              $cliente->Cli_CUIL,
                              $cliente->Serv_Nombre,
                              date('d-m-Y',strtotime($cliente->Serv_Fecha)),
                              anchor('servicios_egreso/cobrar/'.$cliente->Serv_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        	
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosEgreso/servicios', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    }
    public function cobrar($serv_id){
        if($this->caja_abierta()){}
        $this->form_validation->set_rules('comp_nro','Recibo N�','required|trim|exact_length[13]|xss_clean');
        $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
        $this->form_validation->set_rules('descripcion','Descripci�n','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
        if ($this->form_validation->run() == FALSE){
            $data['id']=$serv_id;
            $clientes= $this->ServiciosEgreso_model->buscar_servicio($serv_id);

            // generate table data
            $this->load->library('table');
            $this->table->set_empty("&nbsp;");

            foreach ($clientes as $cliente)
            {   $data['cliente']=$cliente;
                $this->table->add_row(
                                     "<b>Nombre: </b>$cliente->Cli_ApeNom",
                                    "<b>CUIT/CUIL: </b>$cliente->Cli_CUIL"

                            );
            }
            $data['table'] = $this->table->generate();

            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosEgreso/pago_servicio', $data, TRUE);
        }else{
                    $data['id']=$serv_id;
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $this->input->post('monto');

                    $ventas= $this->ServiciosIngreso_model->buscar_servicio($serv_id);

                    // Genero la tabla que muestra los datos de cliente
                    foreach ($ventas as $venta)
                    {   $data['cli_id']=$venta->Cli_Id;
                        $data['apeNom']=$venta->Cli_ApeNom;
                        $data['dir']=$venta->Cli_Direccion;
                        $data['iva']=$venta->Cli_cond_iva;
                        $data['cuil']=$venta->Cli_CUIL;    
                    }
                    
                    // Genero la tabla que muestra el detalle del movimiento
                    $data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
                    //$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosEgreso/confirmacion', $data, TRUE);
                    }
                    $this->load->view('templates',$datoPrincipal);  
        
    }
    
    function registrar($serv_id){
            if($this->caja_abierta()){}
            $data['id']=$serv_id;    
            $comp_nro = $this->input->post('comp_nro');
            $formaPago = $this->input->post('formaPago');
            $desc = $this->input->post('desc');
            $monto = $this->input->post('monto');
            $tipo_desc='Servicios a Terceros';
                    $tipos= $this->MovimientosCaja_model->tipo_movimiento($tipo_desc);
                    foreach ($tipos as $tipo){
                        $tm=$tipo->TipMov_Id;
                    }
            $servicios= $this->ServiciosEgreso_model->buscar_servicio($serv_id);
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

            $fecha=$this->MovimientosCaja_model->insert($caj_id,$tm,$monto,$desc,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$Dic);

            $movimientos=$this->MovimientosCaja_model->get_id($caj_id,$fecha);
            foreach ($movimientos as $movimiento){
                $mov_id=$movimiento->Mov_Id;
            }
            $tipo_comp='RECIBO';
            $this->MovimientosCaja_model->insert_comprobante($tipo_comp,$comp_nro,$caj_id,$mov_id);
            $this->RendicionesCaja_model->update_ingreso($caj_id,$monto);
            $clientes=$this->ServiciosEgreso_model->buscar_servicio($serv_id);
            foreach ($clientes as $cliente){
                $cli_id=$cliente->Cli_Id;
                $razonsocial=$cliente->Cli_ApeNom;

            }
            $this->MovimientosCaja_model->insertIngreso_movCli($caj_id,$mov_id,$cli_id,$razonsocial);

            $servicio = $this->ServiciosEgreso_model->update($serv_id,$caj_id,$mov_id);
            
            $data['mov_id']=$mov_id;
            if ($servicio==TRUE){
                $data['message']='<div class="success">Exito!</div>';
            }else{
                $data['message']='Error, no guardado.';
            }
            $datoPrincipal ['contenidoPrincipal'] = $this->load->view('serviciosEgreso/imprimir', $data, TRUE);

        
        $this->load->view('templates',$datoPrincipal);       
    }
    
    public function imprimir($mov_id){
         //Datos de cliente
        $servicios= $this->ServiciosEgreso_model->buscar_mov($mov_id);
        foreach ($servicios as $servicio)
        {   
            $cli_nom=$servicio->Cli_ApeNom;
            $cli_dir=$servicio->Cli_Direccion;
            $cli_iva=$servicio->Cli_cond_iva;
            $cli_cuil=$servicio->Cli_CUIL;   
            $caj_id= $servicio->MovimientoCaja_Caj_Id;
            $mov_id= $servicio->MovimientoCaja_Mov_Id;
            $fecha= date('d-m-Y',strtotime($servicio->Mov_FechaHora));
            $formaPago= $servicio->Mov_FormaDePago;
            $monto= $servicio->Mov_Mono;
            $desc= $servicio->Mov_Descripcion;
        }
        if($fPago==1){
            $formaPago='Contado';
        }else{
            $formaPago='Cheque';
        }
        

        //$impresion->recibo($fecha,$cli_nom,$cli_dir,$cli_cuil,$cli_iva,$formaPago,$monto,$desc);
        
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


LA SUMA DE PESOS $monto.------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------------------------

EN CONCEPTO DE $desc.--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------------------------";
                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = '
CHEQUE c/BCO.:----------------------------------------------------------------------------------------- N*------------------------------------------------- $-------------------------,------

CHEQUE c/BCO.:----------------------------------------------------------------------------------------- N*------------------------------------------------- $-------------------------,------

CHEQUE c/BCO.:----------------------------------------------------------------------------------------- N*------------------------------------------------- $-------------------------,------
';                
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


LA SUMA DE PESOS $monto.------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------------------------

EN CONCEPTO DE $desc.--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------------------------------------------------------------------";
                $this->cezpdf->ezText($content, 10, array('justification' => 'full'));
                $this->cezpdf->ezSetDy(-10);

$content = '
CHEQUE c/BCO.:----------------------------------------------------------------------------------------- N*------------------------------------------------- $-------------------------,------

CHEQUE c/BCO.:----------------------------------------------------------------------------------------- N*------------------------------------------------- $-------------------------,------

CHEQUE c/BCO.:----------------------------------------------------------------------------------------- N*------------------------------------------------- $-------------------------,------
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

