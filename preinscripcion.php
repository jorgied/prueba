<?php   
class Preinscripcion extends CI_Controller {
    public function __construct() {
        parent::__construct();
           
    }
  
function index(){ 
    $this->form_validation->set_rules('curso_elegido','required','callback_eligio_curso');
    $this->form_validation->set_rules('tipo_alumno','required','callback_eligio_tipo_alumno');
    $this->form_validation->set_rules('dictado_elegido','required');
    $this->form_validation->set_rules('dni','DNI','required|trim|exact_length[8]|xss_clean|numeric|integer');
    $this->form_validation->set_rules('clave','Clave','required|trim|max_length[50]|xss_clean');
    if ($this->input->post('no_clave')){
        $this->form_validation->set_rules('confirmar','confirmar','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('nya','Nombre y Apellido','required|trim|max_length[50]|xss_clean');    
        $this->form_validation->set_rules('direccion','Domicilio','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('telefono','Teléfono','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('email','E-Mail','required|trim|max_length[50]|xss_clean|valid_emails');
        $this->form_validation->set_rules('fuente','¿Cómo te enteraste de este curso?','trim|max_length[50]|xss_clean');
       // $this->session->set_flashdata('no_clave',TRUE);
    }

    if ($this->form_validation->run() == FALSE){
        $data['fuentes']=$this->fuentes();
        $data['tipos_alumnos']=$this->preinscripcion_model->obtener_tipos_alumnos();
        $data['query_cursos']=$this->preinscripcion_model->cursos_disponibles();
        if ( $this->input->post('no_clave')){$data['check']=TRUE;}
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('preinscripcion/seleccionar_curso',$data,TRUE);
        $this->load->view('preinscripcion/template',$datoPrincipal);
       }
    else{
       extract($_POST);
       if ($this->input->post('no_clave')){ //generar el usuario
           if ($clave===$confirmar){
            $this->preinscripcion_model->guardar_usuario($dni,$nya,$direccion,$telefono,$email,$clave);
           }else{
                 
                 $this->session->set_flashdata('clave_diferente',TRUE);
                 $this->session->set_flashdata('dni',$dni);
                 $this->session->set_flashdata('curso_elegido',$curso_elegido);
                 $this->session->set_flashdata('dictado_elegido',$dictado_elegido);
                 $this->session->set_flashdata('tipo_alumno',$tipo_alumno);
                 $this->session->set_flashdata('nya',$nya);
                 $this->session->set_flashdata('direccion',$direccion);
                 $this->session->set_flashdata('telefono',$telefono);
                 $this->session->set_flashdata('email',$email);
                 $this->session->set_flashdata('fuente',$fuente);
                 redirect('preinscripcion/index');
                }
        }else{  //check dni & clave
                $usuario=$this->preinscripcion_model->check_usuario($dni,$clave);
                if(sizeof($usuario)==0 ){
                //login failed error //dni o contraeña incorrecta
                $this->session->set_flashdata('login_error',TRUE);
                $this->session->set_flashdata('dni',$dni);
                $this->session->set_flashdata('fuente',$fuente);
                $this->session->set_flashdata('curso_elegido',$curso_elegido);
                $this->session->set_flashdata('dictado_elegido',$dictado_elegido);
                redirect('preinscripcion/index');
              }
        }
        //armar string con las fuantes (CSV)
        $fuentes='';
            for ($key=0; $key <= 4 ; $key++){
                if(isset($_POST['checkbox'.$key]))
               {
                $fuentes=$fuentes.$_POST['checkbox'.$key].',';
               }
            }
        $fuentes=$fuentes.$fuente;
        //preinscribir en la db
        $this->preinscripcion_model->preinscribir($curso_elegido,$dictado_elegido,$dni,$fuentes,$tipo_alumno);
        //armar la vista
        $nombre_curso=$this->preinscripcion_model->nombre_curso($curso_elegido);
        $data['curso']=$nombre_curso['Cur_Nombre'];
        //$dictados=  json_decode($this->dictados($curso_elegido));
        $data['dictado_elegido']= $this->obtener_dictado($curso_elegido, $dictado_elegido) ;
        $data['preinscripcion']=$this->preinscripcion_model->obtener_ultima_preinscripcion($curso_elegido,$dictado_elegido,$dni)->row_array(0);
        $datoPrincipal['contenidoPrincipal']=$this->load->view('preinscripcion/preinscripcion_exito',$data,TRUE);
        $this->load->view('preinscripcion/template',$datoPrincipal);      
      }
}
    
    function dictados($curso){
        //retorna los dictados de un cruso en JSON
        //$curso=$this->input->get('id');
        //date('d-m-Y',strtotime($row['Dic_InicioClases']))
        $consulta=$this->preinscripcion_model->dictados_disponibles($curso);
        foreach ($consulta->result_array() as $row) {
            $horario=$this->obtener_horarios($row['Dic_Id'],$curso);
            $dict[($row['Dic_Id'])]= $horario.' Profesor: '.$row['Pro_ApeNom'].' '.' Inicio: '.date('d-m-Y',strtotime($row['Dic_InicioClases'])).' Lugar: '.$row['Dic_LugarDictado'];
        } 
        $this->output->set_header("Content-Type: text/json charset=UTF-8\r\n");
        echo json_encode($dict);
    }
    
    function obtener_dictado($curso,$dictado_id){
        //iden dictados() pero retorna un arreglo
        $consulta=$this->preinscripcion_model->dictados_disponibles($curso);
        foreach ($consulta->result_array() as $row) {
            $horario=$this->obtener_horarios($row['Dic_Id'],$curso);
            $dict[($row['Dic_Id'])]= $horario.' Profesor: '.$row['Pro_ApeNom'].' '.' Inicio:'.$row['Dic_InicioClases'].' Lugar: '.$row['Dic_LugarDictado'];
        } 
        return $dict[$dictado_id];
    }
    
    function ingresar_clave($curso){
        //if($flag=='no_clave'){redirect('Preinscripcion/inscribir/'.$curso);}else{$flag='';}
        
        $this->form_validation->set_rules('clave','Clave','trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('dni','DNI','trim|exact_length[8]|xss_clean|numeric|integer');
        $this->form_validation->set_rules('fuente','¿Cómo te enteraste de este curso?','trim|max_length[50]|xss_clean');
        //redirigir atras si faltan datos
        if(!isset($curso)){ redirect('preinscripcion');}
        //armar arreglo de dictados
        $nombre_curso=$this->preinscripcion_model->nombre_curso($curso);
        $consulta=$this->preinscripcion_model->dictados_disponibles($curso);
        $dictados;
         foreach ($consulta->result_array() as $row) {
                      $horario=$this->obtener_horarios($row['Dic_Id'],$curso);
                         $dictados[($row['Dic_Id'])]= $horario.' Profesor: '.$row['Pro_ApeNom'].' '.' Inicio:'.$row['Dic_InicioClases'].' Lugar: '.$row['Dic_LugarDictado'];
                         } 
        //si falla la validacion del form                         
        if ($this->form_validation->run() == FALSE){
        // if ($this->input->post('submit')){
           $data['curso']=$curso;
           $data['nombre_curso']=$nombre_curso;
           $data['dictados']=$dictados;
           $data['fuentes']=$this->fuentes();
           $datoPrincipal ['contenidoPrincipal'] = $this->load->view('preinscripcion/preinscripcion_clave',$data,TRUE);
            $this->load->view('preinscripcion/template',$datoPrincipal);
           }// else{redirect('Preinscripcion/inscribir/'.$curso);}}
        else{
           extract($_POST);
           /*if(empty($dni) && empty($clave)){
               redirect('preinscripcion/inscribir/'.$curso);
           }*/
            $usuario=$this->preinscripcion_model->check_usuario($dni,$clave);
            //dni o contraeña incorrecta
            if(sizeof($usuario)==0 ){
                //login failed error
                $this->session->set_flashdata('login_error',TRUE);
                $this->session->set_flashdata('dni',$dni);
                redirect('preinscripcion/ingresar_clave/'.$curso);
            }
            else{
                //recorrer los checkboxs y buscar los seleccionados, en este caso 5
                $fuentes='';
                for ($key=0; $key <= 4 ; $key++){
                    
                    if(isset($_POST['checkbox'.$key]))
                   {
                    $fuentes=$fuentes.$_POST['checkbox'.$key].',';
                   }
                }
                $fuentes=$fuentes.$fuente;
         //$data['fuentes']=$fuentes;
                //dni y clave correcto
                 $data['curso']=$nombre_curso['Cur_Nombre'];
          $this->preinscripcion_model->preinscribir($curso,$dictado_elegido,$dni,$fuentes);
          $data['dictado_elegido']=$dictados[$dictado_elegido];
          $data['preinscripcion']=$this->preinscripcion_model->obtener_ultima_preinscripcion($curso,$dictado_elegido,$dni)->row_array(0);
          $datoPrincipal['contenidoPrincipal']=$this->load->view('preinscripcion/preinscripcion_exito',$data,TRUE);
          $this->load->view('preinscripcion/template',$datoPrincipal);
            }
            
          }
        
    }
    
    
       function inscribir($curso){
        $this->form_validation->set_rules('dictado_elegido','required');
        $this->form_validation->set_rules('nya','Nombre y Apellido','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('dni','DNI','required|trim|exact_length[8]|xss_clean|numeric|integer');
        $this->form_validation->set_rules('direccion','Domicilio','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('telefono','Teléfono','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('email','E-Mail','required|trim|max_length[50]|xss_clean|valid_emails');
        $this->form_validation->set_rules('fuente','¿Cómo te enteraste de este curso?','trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('clave','Clave','required|trim|max_length[50]|xss_clean');
        //si no llega parametro vuelve a la pagina anterior
        if(!isset($curso)){
            redirect('preinscripcion');
        }
        $nombre_curso=$this->preinscripcion_model->nombre_curso($curso);
        $consulta=$this->preinscripcion_model->dictados_disponibles($curso);
        $dictados;
           foreach ($consulta->result_array() as $row) {
                      $horario=$this->obtener_horarios($row['Dic_Id'],$curso);
                         $dictados[($row['Dic_Id'])]= $horario.' Profesor: '.$row['Pro_ApeNom'].' '.' Inicio:'.$row['Dic_InicioClases'].' Lugar: '.$row['Dic_LugarDictado'];
                         } 
        if ($this->form_validation->run() == FALSE){
           $data['curso']=$curso;
           $data['nombre_curso']=$nombre_curso;
           $data['dictados']=$dictados;
           $data['fuentes']=$this->fuentes();
           $datoPrincipal ['contenidoPrincipal'] = $this->load->view('preinscripcion/preinscripcion',$data,TRUE);
            $this->load->view('preinscripcion/template',$datoPrincipal);
           }
        else{/*
          extract($_POST);
          $data['curso']=$nombre_curso['Cur_Nombre'];
          $this->preinscripcion_model->preinscribir($curso,$dictado_elegido,$dni,$nya,$direccion,$telefono,$email,$fuente);
          $data['dictado_elegido']=$dictados[$dictado_elegido];
          $data['preinscripcion']=$this->preinscripcion_model->obtener_ultima_preinscripcion($curso,$dictado_elegido,$dni)->row_array(0);
          $datoPrincipal['contenidoPrincipal']=$this->load->view('preinscripcion/preinscripcion_exito',$data,TRUE);
          $this->load->view('preinscripcion/template',$datoPrincipal);*/
          extract($_POST);
          //recorrer los checkboxs y buscar los seleccionados, en este caso 5
                $fuentes='';
                for ($key=0; $key <= 4 ; $key++){
                    if(isset($_POST['checkbox'.$key]))
                   {
                    $fuentes=$fuentes.$_POST['checkbox'.$key].',';
                   }
                }
                $fuentes=$fuentes.$fuente;
         //$data['fuentes']=$fuentes;
          //crear user    
          $this->preinscripcion_model->guardar_usuario($dni,$nya,$direccion,$telefono,$email,$clave);
          //armar la vista
          $data['curso']=$nombre_curso['Cur_Nombre'];
          $this->preinscripcion_model->preinscribir($curso,$dictado_elegido,$dni,$fuentes);
          //$usuario['Pre_Usr_ApeNom'],$usuario['Pre_Usr_Direccion'],$usuario['Pre_Usr_Telefono'],$usuario['Pre_Usr_Mail']
          $data['dictado_elegido']=$dictados[$dictado_elegido];
          $data['preinscripcion']=$this->preinscripcion_model->obtener_ultima_preinscripcion($curso,$dictado_elegido,$dni)->row_array(0);
          $datoPrincipal['contenidoPrincipal']=$this->load->view('preinscripcion/preinscripcion_exito',$data,TRUE);
          $this->load->view('preinscripcion/template',$datoPrincipal);
            
            
          }
        }
    function obtener_horarios($dictado,$curso){
        $resultado=$this->preinscripcion_model->horarios($curso,$dictado);
        $horario='';
        foreach ($resultado->result_array() as $row) {
            switch ($row['Dia']) {
    case 1:
        $dia='Lunes';
        break;
    case 2:
        $dia='Martes';
        break;
    case 3:
        $dia='Miercoles';
        break;
    case 4:
        $dia='Jueves';
        break;
    case 5:
        $dia='Viernes';
        break;
    case 6:
        $dia='Sábado';
        break;
                         }
       $horario=$horario.$dia.' '.$row['HoraDesde'].'-'.$row['HoraHasta'].' hs ';
        }
        
        return $horario;
    }
    function fuentes(){
        $fuentes=array('Anuncios de radio o TV','Publicidad en Diarios o medios impresos','Publicidad en Internet','Publicidad por E-Mail');
        
        return $fuentes;
    }
    function eligio_curso($str){
            if ($str=='a')
            {
                $this->form_validation->set_message('eligio_curso','Debe elegir un curso');
                return FALSE;
            }else{
                    return TRUE;
                 }
    }
    function eligio_tipo_alumno($str){
            if ($str=='a')
            {
                $this->form_validation->set_message('eligio_tipo_alumno','Debe elegir un tipo de alumno');
                return FALSE;
            }else{
                    return TRUE;
                 }
    }
    
}
  ?>