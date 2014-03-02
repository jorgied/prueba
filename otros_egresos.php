<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Otros_Egresos extends MY_Controller {
    public function __construct() {
        parent::__construct();
    	// load library
		$this->load->library(array('table','form_validation'));
		
		// load helper
		$this->load->helper('url');
		
		// load model
		$this->load->model('OtrosEgresos_model','',TRUE);
	}
   /* public function index(){
        $data ['titulo']= 'SysCoop';
        $data ['subtitulo']='Otros Egresos';
        $data['table'] = ' ';
        $data['nuevo_proveedor']= ' ';
        $this->load->model('MovimientosCaja_model');
        
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_egresos/otros_egresos', $data, TRUE);
  
        $this->load->view('templates',$datoPrincipal);
        
    } */
    
   /* public function buscar(){
       
        $data ['titulo']= 'SysCoop';
        $data['subtitulo']='Otros Egresos';
	$query = $this->input->post('proveedor');
        $this->load->model('Proveedores_model');
	$proveedores = $this->Proveedores_model->buscar_proveedor($query);
	if(! $proveedores){
            $data['table']='<p>No encontrado.</p>';
            
        }else{		
	// generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Proveedor','CUIL/CUIT','Dirección','Acciones');
	$i = 0;
	foreach ($proveedores as $proveedor)
	{
		$this->table->add_row(++$i, $proveedor->Prov_RazonSocial,
                              $proveedor->Prov_CUIT,
                              $proveedor->Prov_Direccion,
                		anchor('proveedores/ver/'.$proveedor->Prov_Id,'Ver',array('class'=>'view')).' '.
				anchor('proveedores/editar/'.$proveedor->Prov_Id,'Editar',array('class'=>'update')).' '.
				anchor('otros_egresos/pagar/'.$proveedor->Prov_Id,'Pagar',array('class'=>'money'))
			);
	}
	$data['table'] = $this->table->generate();
        }
        //$this->form_data->dob = date('d-m-Y',strtotime($person->dob));
        $data['nuevo_proveedor']= anchor('proveedor/nuevo/','Nuevo Proveedor',array('class'=>'add'));
		
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_egresos/otros_egresos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
    } */
    
    function index(){
	if($this->caja_abierta()){};
        $data ['titulo']= 'SysCoop';
        $data ['subtitulo']='EGRESOS - Otro Egreso';
        //$data['id']=$prov_id;
        
        
        //$proveedores= $this->Proveedores_model->get_by_id($prov_id);
        
        // generate table data
       /* $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading('Razón Social','CUIL/CUIT','Domicilio');
		
        $i = 0;
	foreach ($proveedores as $proveedor)
	{
            $this->table->add_row(
                                $proveedor->Prov_RazonSocial,
                                $proveedor->Prov_CUIT,
                                $proveedor->Prov_Direccion
                                
			);
	} */
       //$data['table'] = $this->table->generate();
	            $this->form_validation->set_rules('comp_nro','Recibo Nº','required|trim|exact_length[13]|xss_clean');
                $this->form_validation->set_rules('formaPago','Forma de Pago','required|trim|xss_clean');
                $this->form_validation->set_rules('descripcion','Descripción','required|trim|max_length[500]|xss_clean');
                $this->form_validation->set_rules('monto','Importe','required|trim|xss_clean');
				$this->form_validation->set_rules('nombre','Responsable','required|trim|max_length[50]|xss_clean');
	   
	    if ($this->form_validation->run() == FALSE){
        $puesto=$this->session->userdata('puesto');
			
            $data['rbo_nro']=$this->Anticipos_model->recibo_nro($puesto);
  
        //$this->load->model('MovimientosCaja_model');
        $rows= $this->OtrosEgresos_model->centro_costo_todas_sec();
        foreach ($rows as $row) {
			$centros[$row->Gru_Descripcion] = $row->Gru_Descripcion;
		}
        $data['centros']=$centros;
		$fecha = date('d-m-Y');
		$data['fecha'] = $fecha;
	$datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_egresos/Pagar_Otros_Egresos',$data, TRUE);																																																																																																																																																																																																																																																																																																																																																																																																																																																																																							
    }else{
                    $comp_nro = $this->input->post('comp_nro');
                    $formaPago = $this->input->post('formaPago');
                    $desc = $this->input->post('descripcion');
                    $monto = $this->input->post('monto');
					$fecha = $this->input->post('fecha');
					$centros = $this->input->post('centros');
					$nombre = $this->input->post('nombre');

                   
                    
                    // Genero la tabla que muestra el detalle del movimiento
                    //$data['comp_nro']=$comp_nro;
                    $data['formaPago']=$formaPago;
                    $data['desc']=$desc;
                    $data['monto']=$monto;
					$data['fecha'] = $fecha;
					$data['centros'] = $centros;
					$data['comp_nro'] = $comp_nro;
					$data['nombre'] = $nombre;
                    //$data['link_back'] = anchor('alquileres/cobrar/'.$id,'Volver',array('class'=>'back'));
                    $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_egresos/confirmacion', $data, TRUE);
                    }
                
	
	
	    $this->load->view('templates',$datoPrincipal);      
    }
    
    public function registrar(){
	if($this->caja_abierta()){};
                $data ['titulo']= 'SysCoop';
                $data['subtitulo']='EGRESOS - Otro Egreso';
		
                $comp_tipo = $this->input->post('comp_tipo');
                $comp_nro = $this->input->post('comp_nro');
                $cc = $this->input->post('centros');
                $formaPago = $this->input->post('formaPago');
                $desc = $this->input->post('desc');
                $monto = $this->input->post('monto');
                $fecha = $this->input->post('fecha');
				$nombre = $this->input->post('nombre');
				$fecha = date('Y-m-d',strtotime($fecha));
				
                $this->load->model('OtrosEgresos_model');
                $this->load->model('MovimientosCaja_model');
                //$this->load->model('RendicionesCaja_model');
                
                //dar a elegrir TIPO DE MOVIMIENTO DE CAJA
                $tipo_desc='Gastos Varios';
                $tipos= $this->OtrosEgresos_model->tipo_movimiento($tipo_desc);
                foreach ($tipos as $tipo){
                    $tm=$tipo->TipMov_Id;
                }
				
                //Completar con Centros de Costos Completos.
                //$centros=  $this->OtrosEgresos_model->centro_costo_grupo($cc);
                //foreach ($centros as $centro){
                   // $sec=$centro->Sec_Id;
                   // $dir=$centro->Dir_Id;
                   // $gru=$centro->Gru_Id;
                    //$cur=$centro->Cur_Id;
                    //$dic=$centro->Dic_Id;
               // }
                	$sec=0;
                    $dir=0;
                    $gru=0;
                    $cur=0;
                    $dic=0;
                $caj_id=$this->session->userdata('caja_id');
                //Egreso >> FALSE
                $fecha=$this->OtrosEgresos_model->insertMovimientoCaja($caj_id,$tm,$monto,$desc,$fecha,$formaPago,'FALSE',$sec,$dir,$gru,$cur,$dic,$nombre);
                
                $movimientos=$this->OtrosEgresos_model->get_id($caj_id,$fecha);
                
                foreach ($movimientos as $movimiento){
                    $mov_id=$movimiento->Mov_Id;
                }
                $this->OtrosEgresos_model->insert_comprobante($comp_tipo,$comp_nro,$caj_id,$mov_id);
				$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                
                //$this->RendicionesCaja_model->update_egreso($caj_id,$monto);
                
                //$this->MovimientosCaja_model->insertIngreso_movCli($caj_id,$mov_id,$cli_id,$razonsocial);
                $ids = $this->OtrosEgresos_model->max_id();
                foreach ($ids as $id){
                    $egreso_id=$id->id;
                }
                /*$egreso = $this->OtrosEgresos_model->insert($egreso_id,$caj_id,$mov_id);
                
                if ($egreso ==1){
                    $data['message']='<div class="success">Exito!</div>';
                }else{
                    $data['message']='Error, no guardado.';
                }*/
				 if ($fecha ==TRUE){
                    $data['message']='<div class="success">Exito!</div>';
                }else{
                    $data['message']='Error, no guardado.';
                }
		                
				$data['id'] = $mov_id;
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('otros_egresos/imprimir', $data, TRUE);
                $this->load->view('templates',$datoPrincipal);        
    }

function imprimir($mov_id){
        //Datos de cliente
        $movim= $this->OtrosEgresos_model->buscarmov($mov_id);
        foreach ($movim as $mo)
        {   $caj_id= $mo->Caj_Id;
            $mov_id= $mo->Mov_Id;
        }
        
        $movs= $this->OtrosEgresos_model->get_mov($caj_id,$mov_id);
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
"DESTINATARIO: 
DNI: 
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
"DESTINATARIO: 
DNI: 
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
