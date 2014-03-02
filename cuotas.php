<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuotas extends MY_Controller {
    
   	private $limit = 10;
	
	function __construct()
	{
		parent::__construct();
		
		// load library
		$this->load->library(array('table','form_validation'));
		
		// load helper
		$this->load->helper('url');
		
		// load model
		$this->load->model('Person_model','',TRUE);
                $this->load->model('Cursos_model','',TRUE);
                $this->load->model('Cuotas_model','',TRUE);
                $this->load->model('Dictados_model','',TRUE);
                $this->load->model('Consultas_model','',TRUE);
                $this->load->model('Movimiento_model','',TRUE);
                $this->form_validation->set_message('required', 'Campo Obligatorio');
	
                //$this->load->view('plantilla');
        }
  
    
    
    
    public function index(){
        if($this->caja_abierta()){};
        $data ['titulo']= 'SysCoop';
        $data ['subtitulo']='Cuotas';
                  $uri_segment = 3;
                    $offset = $this->uri->segment($uri_segment);

                    // load data
                    $persons = $this->Cursos_model->get_paged_list($this->limit, $offset)->result();

                    // generate pagination
                    $this->load->library('pagination');
                    $config['base_url'] = site_url('person/view2/');
                    $config['total_rows'] = $this->Cursos_model->count_all();
                    $config['per_page'] = $this->limit;
                    $config['uri_segment'] = $uri_segment;
                    $this->pagination->initialize($config);
                    $data['pagination'] = $this->pagination->create_links();

                    // generate table data
                    $this->load->library('table');
                    $this->table->set_empty("&nbsp;");
                    $this->table->set_heading('Codigo de Curso','Nombre del Curso', 'Duracion', 'Metodologia', 'Acciones');
                    $i = 0 + $offset;
                   foreach ($persons as $person)
		{
			$this->table->add_row(++$i, $person->Cur_Nombre, $person->Cur_Duracion, $person->Cur_Metodologia, 
				anchor('person/viewcurso/'.$person->Cur_Id,'Ver Alumnos del Curso',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
                        
                                   
		
                    $data['table'] = $this->table->generate();
		
		// load view
		 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaPrincipal2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
                    
                    
                   // $this->load->view('caja/VistaPrincipal2' , $data);

    }

     function search() { //SSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	  if($this->caja_abierta()){};	
         $data ['titulo']= 'SysCoop';
                $data['subtitulo']='Alumnos';
		$query = $this->input->post('Alu_DNI');
          
                //$this->load->model('Person_model');
		$alumnos = $this->Person_model->search_alumno($query);
			
		

                 
           

                // generate table data
                $this->load->library('table');
                $this->table->set_empty("&nbsp;");
                $this->table->set_heading(' ','Nombre','DNI','Acciones');
		$i = 0;
		foreach ($alumnos as $alumno)
		{
			$this->table->add_row(++$i, $alumno->Alu_ApeNom, $alumno->Alu_DNI,  
				anchor('person/view/'.$alumno->Alu_Id,'Ver Cursos del Alumno',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
		$data['table'] = $this->table->generate();
                
		//$datoSegundo['tabla_alu']= $this->load->view('cuotas/exito_alumno',$data,TRUE);
                //$datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/cuotas', $datoSegundo, TRUE);
                //$this->load->view('templates',$datoPrincipal);
                
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('caja/VistaPrincipal2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
                
        }
	
         function search2() {   //SSSSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIiiiiiiii
		  if($this->caja_abierta()){};
             $data ['titulo']= 'SysCoop';
                $data['subtitulo']='Alumnos';
		$query = $this->input->post('Alu_ApeNom');
              
                //$this->load->model('Person_model');
		$alumnos = $this->Person_model->search_alumno2($query);
			
		

                 
           

                // generate table data
                $this->load->library('table');
                $this->table->set_empty("&nbsp;");
                $this->table->set_heading(' ','Nombre','DNI','Acciones');
		$i = 0;
		foreach ($alumnos as $alumno)
		{
			$this->table->add_row(++$i, $alumno->Alu_ApeNom, $alumno->Alu_DNI,   
				anchor('person/view/'.$alumno->Alu_Id,'Ver Cursos del Alumno',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
		$data['table'] = $this->table->generate();
                
		//$datoSegundo['tabla_alu']= $this->load->view('cuotas/exito_alumno',$data,TRUE);
                //$datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/cuotas', $datoSegundo, TRUE);
                //$this->load->view('templates',$datoPrincipal);
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('caja/VistaPrincipal2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
                
        } 
        
       
    
    
    
    
    function listar_cuotas($idAlumno){
       if($this->caja_abierta()){}
        $data ['titulo']= 'SysCoop';
        $this->load->model('Cuotas_model');
        $this->load->model('Cursos_model','',TRUE);
        $cuotas= $this->Cuotas_model->get_by_id($idAlumno);
        
        // generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Curso','Dictado','Nro. Cuota','Importe','Acciones');
		
        $i = 0;
		foreach ($cuotas as $cuota)
		{
                 $this->table->add_row(++$i,
                                $curso=$this->Cursos_model->get_name($cuota->idCursos),
                                $cuota->idDictados,  
                                $cuota->idCuotas,
                                $cuota->cuotaImporte,
				anchor('cuotas/pagar_cuota/'.$cuota->idAlumnos,'Pagar',array('class'=>'update'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
		$data['table'] = $this->table->generate();
                
		$datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/lista_cuotas', $data, TRUE);
                $this->load->view('templates',$datoPrincipal);
    }
    
    function listar_inscriptos($Cur_Id){
        if($this->caja_abierta()){}
        $data ['titulo']= 'SysCoop';
        $this->load->model('Inscripciones_model','im');
        
        $inscriptos= $this->im->list_by_curso($Cur_Id);
        
        // generate table data
        $this->load->library('table');
        $this->table->set_empty("&nbsp;");
        $this->table->set_heading(' ','Nombre','Dictado','Acciones');
		
        $i = 0;
		foreach ($inscriptos as $inscripto)
		{
                 $this->table->add_row(++$i,
                                $inscripto->Alu_Id,
                                $inscripto->DictadoDic_Id,  
                                anchor('cuotas/cobrar_cuota/'.$inscripto->Alu_Id,'Ver Cuotas',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
		$data['table'] = $this->table->generate();
                
		$datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/lista_cuotas', $data, TRUE);
                $this->load->view('templates',$datoPrincipal);
    }
    
    
}

?>
