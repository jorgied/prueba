<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }
  
    public function index(){
      
        if($this->caja_abierta()){
        //$data ['titulo']= 'SysCoop';
       // $data ['subtitulo']='Bienvenido';
        
        $datoPrincipal ['contenidoPrincipal'] = NULL;
        $this->load->view('templates',$datoPrincipal);
       // $datoPrincipal ['contenidoPrincipal'] = $this->load->view('bienvenido', $data, TRUE);
                           }
        }  
        
    public function admin(){
        if($this->comprobar_perfil('Admin')){
            $datoPrincipal ['contenidoPrincipal'] = NULL;
            $this->load->view('templates_admin',$datoPrincipal);
        }
    }
        
    function login(){
        
        $this->form_validation->set_rules('usuario','Usuario','required|trim|max_length[50]|xss_clean');
        $this->form_validation->set_rules('contrasena','contrasena','required|trim|max_length[50]|xss_clean');
        
        if ($this->form_validation->run() == FALSE){
           //siguiendo el patron de carga:
            //$datoPrincipal ['contenidoPrincipal'] = $this->load->view('login/login', TRUE);
             //$this->load->view('templates',$datoPrincipal);
            
            //cargando directamente la vista:
            $this->load->view('login/login');
        }
        else{
           // $username=$this->input->post('usuario');
           extract($_POST);
         
            $user_id=$this->user_model->check_login($usuario,$contrasena);
            if(! $user_id){
                //login failed error
                $this->session->set_flashdata('login_error',TRUE);
                redirect('index/login');
            }
            else{
                //login in
                $nombre=$this->user_model->get_name($user_id);
                $login_data= array('logged_in'=>TRUE, 'user_id'=>$user_id, 'name'=>$nombre['Usr_Apenom'], 'perfil'=>$nombre['Usr_Perfil']);
                $this->session->set_userdata($login_data);
                 if($nombre['Usr_Perfil']=='Admin'){                       
                    //redirect('index/admin');
                    $this->admin();
                }
                elseif ($nombre['Usr_Perfil']=='Cajero') {
                    redirect('index');
                }
            }
        }
    }
    
        function login_hash($hash){
        $usuario=$this->user_model->buscar_hash($hash);
        if (sizeof($usuario) != 0){
           $login=$this->user_model->check_login($usuario['Usr_Login'],$usuario['Usr_Clave']);
           if(! $login){
                //login failed error
                $this->session->set_flashdata('login_error',TRUE);
                redirect('index/login');
            }
            else{
                $nombre=$this->user_model->get_name($login);
                $login_data= array('logged_in'=>TRUE, 'user_id'=>$login, 'name'=>$nombre['Usr_Apenom']);
                $this->session->set_userdata($login_data);
                if($usuario['Usr_Perfil']=='Admin'){                       
                    redirect('index/admin');
                }
                elseif ($usuario['Usr_Perfil']=='Cajero') {
                    redirect('index');
                }
                    
            }
            }
        }
    
    function logout(){
      //  $this->session->set_data('logged_in',FALSE);
        $this->session->sess_destroy();
        redirect('index/login');
    }
        
    function abrir_caja(){
       //chequear caja/sesion
        if ($this->is_logged_in()){
        $this->form_validation->set_rules('monto','Monto','required|trim|max_length[5]|xss_clean|greater_than[-1]');
        
         if ($this->form_validation->run() == FALSE){
        $data ['subtitulo']='Abrir Caja';
        $data ['result'] = $this->caja_model->puestos_disponibles();
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('caja/abrir_caja', $data, TRUE);
        $this->load->view('templates_caja_cerrada',$datoPrincipal);     
                }else{      /* levanta los datos de la caja que quedo abierta*/
                  extract($_POST);
                  $nombre_puesto=$this->caja_model->nombre_puesto($puesto_elegido);
                  $this->session->set_userdata('puesto',$nombre_puesto['Pue_Ubicacion']);
                  $this->caja_model->abrir($puesto_elegido,$monto);     
                  $datos_caja=$this->caja_model->recuperar_datos_caja($this->session->userdata('user_id'));
                  $this->session->set_userdata('caja_id',$datos_caja['Caj_Id']);
                  //$this->caja_model->insert_RendCaja($this->session->userdata('caja_id'),$monto);
                  redirect('index');
                    }
             }
         } 
     
    function cerrar_caja(){
        
        if ($this->caja_abierta()){}
        
        $this->form_validation->set_rules('monto','monto','required|trim|max_length[5]|xss_clean|greater_than[-1]|is_numeric');
        
        $ingresos=$this->caja_model->sumar_ingresos(($this->session->userdata('caja_id')));
        $egresos=$this->caja_model->sumar_egresos(($this->session->userdata('caja_id')));
        $monto_ap=$this->caja_model->obtener_monto_ap(($this->session->userdata('caja_id')));
        $data['ingresos']= 0+$ingresos['TotalIng'];
        $data['egresos']= 0+$egresos['TotalEg'];
        $data['monto_apertura']= $monto_ap['Caj_MontoApertura'];
        $data['total']=$data['ingresos']+$data['monto_apertura']-$data['egresos'];
        
        if ($this->form_validation->run() == FALSE){
             
        $data ['subtitulo']='Cerrar Caja';
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('caja/cerrar_caja', $data, TRUE);
        $this->load->view('templates',$datoPrincipal); 
         }else{
             extract($_POST);
             $ing = $data['ingresos'];
			 $eg =  $data['egresos'];
			 
             $this->caja_model->cerrar($monto,$ing,$eg);
             $this->session->set_userdata('caja_id','');
             $this->session->set_userdata('puesto','');
             $data ['subtitulo']='Cerrar Caja';
             $datoPrincipal ['contenidoPrincipal'] = $this->load->view('caja/cerrar_caja_exito', $data, TRUE);
             $this->load->view('templates_caja_cerrada',$datoPrincipal);     
                   }
               
       }
       function realizar_cierre(){
           if ($this->caja_abierta()){}
        $ingresos=$this->caja_model->sumar_ingresos(($this->session->userdata('caja_id')));
        $egresos=$this->caja_model->sumar_egresos(($this->session->userdata('caja_id')));
        $monto=$this->caja_model->obtener_monto_ap(($this->session->userdata('caja_id')));
        $total=$ingresos['TotalIng']+$monto['Caj_MontoApertura']-$egresos['TotalEg'];
        //$this->caja_model->cerrar($total);
        $this->session->set_userdata('caja_id','');
        $this->session->set_userdata('puesto','');
        $data ['subtitulo']='Cerrar Caja';
        $datoPrincipal ['contenidoPrincipal'] = $this->load->view('caja/cerrar_caja_exito', $data, TRUE);
        $this->load->view('templates_caja_cerrada',$datoPrincipal);     
       }
}
?>
