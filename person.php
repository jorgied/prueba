<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Person extends MY_Controller {

	// num of records per page
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
                 $this->load->model('Pagocuota_model','',TRUE);
                $this->form_validation->set_message('required', 'Campo Obligatorio');
	
                //$this->load->view('plantilla');
        }
    
        
        function index ($offset = 0)    //SSSSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
       // if($this->caja_abierta()){}
        if($this->caja_abierta()){};
            $data ['titulo']= 'SysCoop';
        $data ['subtitulo']='Cuotas';		

// offset
		$uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// load data
		$persons = $this->Person_model->get_paged_list($this->limit, $offset)->result();
		
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('person/index/');
 		$config['total_rows'] = $this->Person_model->count_all();
 		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Codigo', 'Nombre', 'DNI', 'Direccion', 'Acciones');
		$i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row(++$i, $person->Alu_ApeNom, $person->Alu_DNI, $person->Alu_Direccion, 
				anchor('person/view/'.$person->Alu_Id,'Ver Datos',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
		$data['table'] = $this->table->generate();
		
		// load view
		
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/personlist', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
              
	}
	
              function view($Alu_Id)  //SSSSSSSSSSSSSSSSSSSSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
{
		// offset
		 if($this->caja_abierta()){};
                  $uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// load data
		//$persons = $this->Person_model->get_paged_list($this->limit, $offset)->result();
		$persons = $this->Person_model->get_by_id($Alu_Id);// ->result();
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('person/index/');
 		$config['total_rows'] = $this->Person_model->count_all();
 		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Nombre', 'DNI', 'Direccion', 'Telefono', 'Correo Electronico', 'Acciones');
	
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row($person->Alu_ApeNom, $person->Alu_DNI, $person->Alu_Direccion, $person->Alu_Telefono, $person->Alu_Email, 
                              
				anchor('person/viewcuotasdictados/'.$person->Alu_Id,'Cursos',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                     
		}
		$data['table'] = $this->table->generate();
		
                  
		// load view
		 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/personlist', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
               
     
	}
        
        function principalinicio ($offset = 0)    //SIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
		// offset
           // if($this->caja_abierta()){}
            if($this->caja_abierta()){};
            $data ['titulo']= 'SysCoop';
             $data ['subtitulo']='Estampillas';
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
		//$data['cuotas'] = $this->Estampillas_model->listar_por_valores();
			
	
         $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/PantallaInicio', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
        
        }
	
        
        function principal ($offset = 0)    //SIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
		// offset
	 if($this->caja_abierta()){};	
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
                
         $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaPrincipal', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
	}
	
        function principal2 ($offset = 0)    //SIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
		// offset
	 if($this->caja_abierta()){};	
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
                    $this->table->set_heading('Nombre del Curso', 'Duracion', 'Metodologia', 'Acciones');
                    $i = 0 + $offset;
                   foreach ($persons as $person)
		{
			$this->table->add_row($person->Cur_Nombre, $person->Cur_Duracion, $person->Cur_Metodologia, 
				anchor('person/viewcurso/'.$person->Cur_Id,'Ver Alumnos del Curso',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			);
		}
                        
                                   
		
                    $data['table'] = $this->table->generate();
		
		// load view
		
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaCursos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
	}
	 
        
        
        
        
        function search() { //SSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	 if($this->caja_abierta()){};	
            $data ['titulo']= 'SysCoop';
                $data['subtitulo']='Alumnos';
		$query = $this->input->post('Alu_DNI');
          
                $this->load->model('Person_model');
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
                
                
                $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaPrincipal2', $data, TRUE);
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
                
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaPrincipal2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
                
        } 
        
        
        
        
        
          function viewcurso($Cur_Id) ///SSSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
		// offset
		 if($this->caja_abierta()){};
              $uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// load data
		//$persons = $this->Person_model->get_paged_list($this->limit, $offset)->result();
		$persons = $this->Cursos_model->get_by_id($Cur_Id);// ->result(); trae a persons todo curso inscripciones y alumnos
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('person/index/');
 		$config['total_rows'] = $this->Cursos_model->count_all();
 		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso', 'metodologia', 'Alumno', 'Enterado por', 'Acciones');
	
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row($person->Cur_Nombre, $person->Cur_Metodologia, $person->Alu_ApeNom, $person->Ins_Fuente, 
                              
				anchor('person/viewcuotasdictados/'.$person->Alu_Id, 'Cuotas del Dictado',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                     
		}
		$data['table'] = $this->table->generate();
		
                 // load view
		
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/personList', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
              
                
        }	

             function viewcuotasdictados($Alu_Id)   //SSSSSSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
		// offset
		 if($this->caja_abierta()){};
                 $uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// load data
		//$persons = $this->Person_model->get_paged_list($this->limit, $offset)->result();
		
                
                $query = $this->db->query("SELECT DictadoDic_Id FROM Inscripciones, Alumnos  WHERE Inscripciones.Alu_Id=Alumnos.Alu_Id AND Inscripciones.Alu_Id='$Alu_Id'"); 
                        foreach ($query->result_array() as $row) 
                        { 
                         $DictadoDic_Id = $row['DictadoDic_Id'];                    
                         }
             
     
                
                $persons = $this->Consultas_model->get_by_id($Alu_Id);// ->result();
               
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('person/viewcuotasdictados/');
 		$config['total_rows'] = $this->Consultas_model->count_all();
 		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso', 'Alumno', 'Inscripcion', 'Forma Pago', 'Valor Cuota', 'Valor Inscripcion', 'Nro Cuotas');
   
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row( $person->Cur_Nombre, $person->Alu_ApeNom, date('d-m-Y',strtotime($person->Ins_Fecha)), $person->For_Descripcion, $person->For_MontoCuota, $person->For_MontoInscripcion, $person->ForCuotas
                              
				//anchor('person/viewcuotasdictados/'.$person->Dic_Id,'Cuotas del Dictado',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                     
		}
                $data['table'] = $this->table->generate();
                
                $persons = $this->Consultas_model->consultacuotas($person->Alu_Id);
		$this->table->set_heading('Nro Cuota', 'Valor', 'Fecha de Vencimiento', 'MontoPagado', 'Acciones');
                
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row($person->Cuo_Numero, $person->Cuo_Monto, date('d-m-Y',strtotime($person->Cuo_FechaVto)), $person->Cuo_MontoPagado,
                              
				anchor('person/cobrar/'.$person->Cuo_Numero,'Cobrar',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                     
		}
                
                
                
                
                $Alu_Id = $person->Alu_Id;
                
		$data['table1'] = $this->table->generate();
		$data['dato'] = $Alu_Id;
                
                 // load view
		
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaListadoCursos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
       
        }
        
        
           function estadocuenta($dato) //SUUUUUUUUUUUUUIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
		// offset
		 if($this->caja_abierta()){};
               $uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// load data
		//$persons = $this->Person_model->get_paged_list($this->limit, $offset)->result();
	   
                $Alu_Id = $dato;
               
                $query = $this->db->query("SELECT DictadoDic_Id, Alumnos.Alu_Id FROM Inscripciones, Alumnos  WHERE Inscripciones.Alu_Id=Alumnos.Alu_Id AND Inscripciones.Alu_Id='$Alu_Id'"); 
                        foreach ($query->result_array() as $row) 
                        { 
                        
                         $Alu_Id = $row['Alu_Id'];
                         
                         }
             
     
                
                $persons = $this->Consultas_model->get_by_id($Alu_Id);// ->result();
               
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('person/index/');
 		$config['total_rows'] = $this->Consultas_model->count_all();
 		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso', 'Alumno', 'Inscripcion', 'Forma Pago', 'Valor Cuota', 'Valor Inscripcion', 'Nro Cuotas');
   
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row( $person->Cur_Nombre, $person->Alu_ApeNom, date('d-m-Y',strtotime($person->Ins_Fecha)), $person->For_Descripcion, $person->For_MontoCuota, $person->For_MontoInscripcion, $person->ForCuotas
                              
				//anchor('person/viewcuotasdictados/'.$person->Dic_Id,'Cuotas del Dictado',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                     
		}
                $data['table'] = $this->table->generate();
                
                $persons = $this->Consultas_model->consultacuotas2($person->Alu_Id);
		$this->table->set_heading('Nro Cuota', 'Valor', 'Fecha de Vencimiento', 'MontoPagado');
                
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row($person->Cuo_Numero, $person->Cuo_Monto, date('d-m-Y',strtotime($person->Cuo_FechaVto)), $person->Cuo_MontoPagado
                              
				//anchor('person/cobrar/'.$person->Cuo_Numero,'Cobrar',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                     
		}
                
                
                
                
                $Alu_Id = $person->Alu_Id;
                
		$data['table1'] = $this->table->generate();
		$data['dato'] = $Alu_Id;
                 // load view
		
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/EstadoDeCuenta', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
              }
        
       
        
        function add()
	{
 if($this->caja_abierta()){};		
// set empty default form field values
		$this->_set_fields();
		// set validation properties
		$this->_set_rules();
		
		// set common properties
		$data['title'] = 'Agregar nuevo Alumno';
		$data['message'] = '';
		$data['action'] = site_url('person/addPerson');
		$data['link_back'] = anchor('person/index/','Volver al Listado de Alumnos',array('class'=>'back'));
	
		// load view
		$this->load->view('personEdit', $data);
	}
	
               
      
        
        
        function addPerson()
	{
            
                 if($this->caja_abierta()){};
		// set common properties
		$data['title'] = 'Agregar nuevo Alumno';
		$data['action'] = site_url('person/addPerson');
		$data['link_back'] = anchor('person/index/','Volver al Listado de Alumnos',array('class'=>'back'));
		
		// set empty default form field values
		$this->_set_fields();
		// set validation properties
		$this->_set_rules();
		
		// run validation
		if ($this->form_validation->run() == FALSE)
		{
			$data['message'] = '';
		}
		else
		{
			// save data
			$person = array('name' => $this->input->post('name'),
							'gender' => $this->input->post('gender'),
							'dob' => date('Y-m-d', strtotime($this->input->post('dob'))));
			$id = $this->Person_model->save($person);
			
			// set user message
			$data['message'] = '<div class="success">Alumno agregado correctamente</div>';
		}
		
		// load view
		$this->load->view('personEdit', $data);
	}
	
	
       
        
        
       
        
        
        function view2($idAlumnos)
	{ if($this->caja_abierta()){};
		// set common properties
		$data['title'] = 'Alumno';
		$data['link_back'] = anchor('person/index/','Volver al Listado de Alumnos',array('class'=>'back'));
		
		// get person details
		$data['person'] = $this->Person_model->get_by_id($idAlumnos)->row();
	
             // aca copie casi todo el index completo que tiene la paginacion
                
                    $offset = 0;
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
                    $this->table->set_heading('Nombre del Curso', 'Duracion', 'Dias de Dictado', 'Horario de Dictado', 'Importe de Cuota', 'Acciones');
                    $i = 0 + $offset;
                    foreach ($persons as $person)
                    {
                        //asi es una manera de ahcer concultas orietado a objeto en codeigniter   
                        //$this->db->select ('CursoNombre', 'insEnteradoPor');
                        //$this->db->from ('cursos');
                        //$this->db->join ('inscripciones', 'inscripciones.idCursos = cursos.idCursos');
                        //$this->db->get ();$query = $this->db->get();

                        //asi es otra manera de hacer la consulta colocando la sentencia sql dentro del query
                        
                        $query = $this->db->query("SELECT idCuotas, alumnos.idAlumnos, cursoNombre, cursoDuracion, dictaDias, dictaHorarios, cuotaImporte FROM cursos, inscripciones, alumnos, dictados, cuotas "); //WHERE (cuotas.idDictados = dictados.idDictados AND inscripciones.idDictados = dictados.idDictados AND inscripciones.idAlumnos = alumnos.idAlumnos AND dictados.idCursos = cursos.idCursos"); 
                        foreach ($query->result_array() as $row) 
                        { 
                       //echo $row['idCursos']; 
                         $cursoNombre = $row['idCuotas'];
                         $cursoNombre = $row['cursoNombre'];
                         $cursoDuracion = $row['cursoDuracion'];
                         $dictaDias = $row['dictaDias'];
                         $dictaHorarios = $row['dictaHorarios'];                       
                         $cuotaImporte = $row['cuotaImporte'];
                         }

                        		
			$this->table->add_row($person->cursoNombre, $person->cursoDuracion=$row['cursoDuracion'], $person->dictaDias=$row['dictaDias'], $person->dictaHorarios=$row['dictaHorarios'], $person->cuotaImporte=$row['cuotaImporte'],  // $person->dictaDias, $person->dictaHorarios, $person->cuotaImporte,
				anchor('person/view3/'.$person->idAlumnos=$row['idAlumnos'],'Pagar',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->id,'update',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->id,'delete',array('class'=>'delete','onclick'=>"return confirm('Are you sure want to delete this person?')"))
			);
                        
                                   
		}
                    $data['table'] = $this->table->generate();
                     // load view
                    
                     $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/VistaListadoCursos', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);

 }

  function cobrar($Cuo_Numero)//SSSSSSSSSSSSSSSSSSSSSSSIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII
	{
            
             if($this->caja_abierta()){};
            // set empty default form field values
		//  $this->_set_fields();
		// set validation properties
		//$this->_set_rules();
		// set common properties
		$data['title'] = 'Cobrar';
		$data['message'] = '';
		$data['action'] = site_url('person/cobrarcuotas');
		$data['link_back'] = anchor('person/index/','Back to list of persons',array('class'=>'back'));
            $resguardo = 100.000;
            $Cuo_Numero2 = $Cuo_Numero;
            $Cuo_Numeroseleccionada = 0;
            $query2 = 0;
	    $this->_set_rules();
            
            // aca hae la consulta para filtrar la cuota mas antigua pendiente
            $query = $this->db->query("SELECT * FROM Cuotas, Alumnos WHERE  Alumnos.Alu_Id=Cuotas.Alu_Id"); 
                        foreach ($query->result_array() as $row) 
                        { 
                         $Alu_Id = $row['Alu_Id'];
                         $Cuo_Monto = $row['Cuo_Monto'];
                         $Cuo_Numero = $row['Cuo_Numero'];
                         $Cuo_MontoPagado = $row['Cuo_MontoPagado'];
               
                        if ($Cuo_Monto>$Cuo_MontoPagado) 
                        { 
                            if ($resguardo > $Cuo_Numero)
                                {
                                $Cuo_Numeroseleccionada = $row['Cuo_Numero'];  
                                $resguardo = $Cuo_Numero;
                                $monto = $Cuo_Monto-$Cuo_MontoPagado;
                                }
                        }
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
                // con los form_data se envia los datos de la factura mas antigua pendiente al formulario
                //$this->form_data->Cuo_Numero = $Cuo_Numero; 
				$data['Cuo_Numero'] = $Cuo_Numero;
                //$this->form_data->Cuo_FechaVto = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
                $data['Cuo_FechaVto'] = date('d-m-Y',strtotime($Cuo_FechaVto)); array('class'=>'view');
				//$this->form_data->Cuo_Monto = $Cuo_Monto;
                
                $data['Cuo_Monto'] = $Cuo_Monto;
				//$this->form_data->Cuo_MontoPagado = $Cuo_MontoPagado;
                $data['Cuo_MontoPagado'] = $Cuo_MontoPagado;
				//$this->form_data->monto = $monto;
                $data['monto'] = $monto;
				//aca levanta la fecha del sistema para tomar como fecha de pago
                $fecha = date('d-m-Y');
                //$this->form_data->fecha = $fecha;
              	$data['fecha'] = $fecha;
		// set common properties
		$data['title'] = 'Ingreso del monto a Cobrar';
		$data['message2'] = 'Solo se permitira el cobro de la Cuota mas antigua pendiente.   '; 
		$data['message3'] ='Si el monto a abonar es mayor al saldo de la misma la diferencia se aplicara a la Cuota siguiente ';
                
		$data['link_back'] = anchor('person/principal','Volver al Listado de Cursos',array('class'=>'add'));
                
                $uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// load data	--- saca el Alu_id a una vairable para volver a listar los datos del alumno elegido	
		 $query = $this->db->query("SELECT DictadoDic_Id, Alumnos.Alu_Id, Alumnos.Alu_ApeNom, Cursos.Cur_Nombre FROM Inscripciones, Alumnos, Cursos  WHERE Inscripciones.DictadoCur_Id=Cursos.Cur_Id AND Inscripciones.Alu_Id=Alumnos.Alu_Id AND Inscripciones.Alu_Id='$Alu_Id'"); 
                        foreach ($query->result_array() as $row) 
                        { 
                        
                         $Alu_Id = $row['Alu_Id'];
                         $Alu_ApeNom = $row['Alu_ApeNom'];
                         $Cur_Nombre = $row['Cur_Nombre'];
                         }
                // aca se hace la consulta para la impresion de la tabla con los datos del curso y del alumno
                $persons = $this->Consultas_model->get_by_id($Alu_Id);// ->result();
               
		// generate pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url('person/index/');
 		$config['total_rows'] = $this->Consultas_model->count_all();
 		$config['per_page'] = $this->limit;
		$config['uri_segment'] = $uri_segment;
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		
		// generate table data
		$this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso', 'Alumno', 'Inscripcion', 'Forma Pago', 'Valor Cuota', 'Valor Inscripcion', 'Nro Cuotas');
   
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row( $person->Cur_Nombre, $person->Alu_ApeNom, date('d-m-Y',strtotime($person->Ins_Fecha)), $person->For_Descripcion, $person->For_MontoCuota, $person->For_MontoInscripcion, $person->ForCuotas
                         );
                     
		}
                $data['table'] = $this->table->generate();
                
                //aca hace la consulta para la impresion de los datos de la cuota elegida
                $persons = $this->Cuotas_model->get_by_id($Cuo_Numero2);
		$this->table->set_heading('Nro Cuota', 'Valor', 'Fecha de Vencimiento', 'MontoPagado');
                
                $i = 0 + $offset;
		foreach ($persons as $person)
		{
			$this->table->add_row($person->Cuo_Numero, $person->Cuo_Monto, date('d-m-Y',strtotime($person->Cuo_FechaVto)), $person->Cuo_MontoPagado
                               );
                     
		}

                $Alu_Id = $person->Alu_Id;
                
		$data['table1'] = $this->table->generate();
		$data['dato'] = $Alu_Id;
                $data['Cuo_Monto'] = $Cuo_Monto;
                $data['Alu_ApeNom'] = $Alu_ApeNom;
                $data['Cur_Nombre'] = $Cur_Nombre;
              
		// load view
		
	
                 $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/cuotasEdit', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);

        }
	
    
        function cobrarcuotas()
	{
 if($this->caja_abierta()){};	
$caja_id=$this->session->userdata('caja_id');

// set common properties
$this->_set_rules();
$this->form_validation->set_rules('Cuo_Numero', 'Cuo_Numero', 'trim|required');
		$this->form_validation->set_rules('Cuo_Monto', 'Cuo_Monto', 'trim|required');
                $this->form_validation->set_rules('Cuo_MontoPagado', 'Cuo_MontoPagado', 'trim|required');
            $this->form_validation->set_rules('monto', 'monto', 'trim|required');
               $this->form_validation->set_rules('fecha', 'fecha', 'trim|required');
		$this->form_validation->set_rules('Cuo_FechaVto', 'Cuo_FechaVto', 'trim|required|callback_valid_date');
		
		$this->form_validation->set_message('required', '* Campo Obligatorio');
		$this->form_validation->set_message('isset', '* Campo Obligatorio');
		$this->form_validation->set_message('valid_date', 'Formato no Valido. dd-mm-aaaa');
		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');

	if ($this->form_validation->run() == TRUE)
		{
			$data['message'] = 'no se ingresaron datos obligatorios';
		}
		else
		{
                 $numerocuota = array ('Cuo_Numero' => $this->input->post('Cuo_Numero'),);
                $Cuo_Numero = $numerocuota['Cuo_Numero'];
		$data['title'] = 'Registro de Cobro Ingresado';
		$data['action'] = site_url('person/cobrarcuotas');
		$data['link_back'] = anchor('person/principal','Volver al Listado de Cursos',array('class'=>'add'));
                $valor = 0;
                $valor2 = 0;
                $saldo = 0;
                $aplicar = 0;
                
       $numerocuota = array ('Cuo_Numero' => $this->input->post('Cuo_Numero'),);
       $Cuo_Numero = $numerocuota['Cuo_Numero'];
  
       $query = $this->db->query("SELECT * FROM Cuotas, Alumnos, Cursos  WHERE Cuotas.Cuo_Numero='$Cuo_Numero' AND Cuotas.Cur_Id=Cursos.Cur_Id AND Alumnos.Alu_Id=Cuotas.Alu_Id"); 
                        foreach ($query->result_array() as $row) 
                        { 
                        
                         $Cuo_Monto = $row['Cuo_Monto'];
                         $Cuo_MontoPagado = $row['Cuo_MontoPagado'];
                         $Alu_ApeNom = $row['Alu_ApeNom'];
                         $Cur_Nombre = $row['Cur_Nombre'];
                         $Alu_Id = $row['Alu_Id'];
                         
                         }
       
                         $person = array('Caj_Id' => $caja_id,
                                        'Cuo_Numero' => $this->input->post('Cuo_Numero'),
					'TipMov_Id' => 1,
                                        'Mov_Mono' => $this->input->post('monto'),                                       
					'Mov_FechaHora' => date('Y-m-d', strtotime($this->input->post('fecha'))),
                                        'Mov_IngresoEgreso' =>  TRUE,
                                        'Mov_Descripcion' => 'pago cuota',
                                        'Mov_Anulado' => TRUE,
                                        'Mov_FormadePago' => 1,
                                        'Sec_Id' => 1,
                                        'Dir_Id' => 1,
                                        'Gru_Id' => 1,
                                        'Cur_Id' => 4,
                                        'Dic_Id' => 1,
                            
                            
                            );
                    $Mov_Id = $this->Movimiento_model->save($person);
        $valor2 = $person['Mov_Mono'];
        $valor = $Cuo_Monto - $Cuo_MontoPagado;
        $saldo = $valor - $valor2;
    
	If ($saldo < 0)	
        { 
                      $aplicar =  $saldo * (-1);
                      $saldo = 0;
        }
        else{ 
                      $aplicar =  0;
        }                   
// set user message			
                $this->load->library('table');
		$this->table->set_empty("&nbsp;");
		$this->table->set_heading('Curso','Alumno','Numero cuota', 'Importe Abonado', 'Saldo', 'Valor aplicado a cuota siguiente');	
		$this->table->add_row( $Cur_Nombre, $Alu_ApeNom, $Cuo_Numero, $valor2, $saldo, $aplicar
                              
				//anchor('person/viewcuotasdictados/'.$person->Dic_Id,'Cuotas del Dictado',array('class'=>'view'))//.' '.
				//anchor('person/update/'.$person->idAlumnos,'Actualizar',array('class'=>'update')).' '.
				//anchor('person/delete/'.$person->idAlumnos,'Borrar',array('class'=>'delete','onclick'=>"return confirm('Esta seguro que desea borrar el Alumno?')"))
			  );
                $data['table'] = $this->table->generate();
//hago    la carga de pago cuota
                    $person = array('Cur_Id' => 4,
                            'Dic_Id' => 1,
                        'Alu_Id' => 1,
                        'Cuo_Numero' => $this->input->post('Cuo_Numero'),
                        'LiquidacionCur_Id' => NULL,
                        'LiquidacionDic_Id' => NULL,
                        'LiquidacionLic_Id' => NULL,
                        'MovimientoCaja_Caj_Id' => 5,
                        'MovimientoCaja_Mov_Id' => 135,
                       
                            
                        
                        );

                   

                    $Mov_Id = $this->Pagocuota_model->save($person);
                    
                    
                    
                    
                        
                        
                        
        
        
        
        
        
        
        
        
        $data['message'] = '<div class="success">add new person success</div>';
                        
		
         
           $datoPrincipal ['contenidoPrincipal'] = $this->load->view('cuotas/PantallaInicio2', $data, TRUE);
        $this->load->view('templates',$datoPrincipal);
                        
                       } 
                        
                        
             }           
                        
                        
		
                
                
	
		
	
	
        
         
        
	function updatePerson()
	{
		// set common properties
		$data['title'] = 'Alumno Actualizado';
		$data['action'] = site_url('person/updatePerson');
		$data['link_back'] = anchor('person/index/','Volver al listado de Alumnos',array('class'=>'back'));
		
		// set empty default form field values
		$this->_set_fields();
		// set validation properties
		$this->_set_rules();
		
		// run validation
		if ($this->form_validation->run() == FALSE)
		{
			$data['message'] = '';
		}
		else
		{
			// save data
			$id = $this->input->post('id');
			$person = array('name' => $this->input->post('name'),
							'gender' => $this->input->post('gender'),
							'dob' => date('Y-m-d', strtotime($this->input->post('dob'))));
			$this->Person_model->update($id,$person);
			
			// set user message
			$data['message'] = '<div class="success">Datos del alumno Modificado</div>';
		}
		
		// load view
		$this->load->view('personEdit', $data);
	}
	
	function delete($id)
	{
		// delete person
		$this->Person_model->delete($id);
		
		// redirect to person list page
		redirect('person/index/','refresh');
	}
	
	// set empty default form field values
	//function _set_fields()
	//{
		//$this->form_data->Cuo_Numero = '';
		//$this->form_data->Cuo_Monto = '';
		//$this->form_data->Cuo_MontoPagado = '';
		//$this->form_data->monto = '';
          //     $this->form_data->fecha = '';
            //    $this->form_data->Cuo_FechaVto = '';
	//}
	
	// validation rules
	function _set_rules()
	{
		$this->form_validation->set_rules('Cuo_Numero', 'Cuo_Numero', 'trim|required');
		$this->form_validation->set_rules('Cuo_Monto', 'Cuo_Monto', 'trim|required');
                $this->form_validation->set_rules('Cuo_MontoPagado', 'Cuo_MontoPagado', 'trim|required');
            $this->form_validation->set_rules('monto', 'monto', 'trim|required');
               $this->form_validation->set_rules('fecha', 'fecha', 'trim|required');
		$this->form_validation->set_rules('Cuo_FechaVto', 'Cuo_FechaVto', 'trim|required|callback_valid_date');
		
		$this->form_validation->set_message('required', '* Campo Obligatorio');
		$this->form_validation->set_message('isset', '* Campo Obligatorio');
		$this->form_validation->set_message('valid_date', 'Formato no Valido. dd-mm-aaaa');
		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');
	}
	
	// date_validation callback
	function valid_date($str)
	{
		//match the format of the date
		if (preg_match ("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $str, $parts))
		{
			//check weather the date is valid of not
			if(checkdate($parts[2],$parts[1],$parts[3]))
				return true;
			else
				return false;
		}
		else
			return false;
	}
}
?>