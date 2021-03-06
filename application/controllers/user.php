<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    /**
     * User constructor.
     */
    public function __construct()
    {

        parent::__construct();
        $this->load->helper(array('html','url'));
        $this->load->library('form_validation');
        $this->load->model('user_model');
    }
    public function test(){
        $this->user_model->send_email_to_db($_GET['email']);
    }

    public function login()
    {
        if( $this->data['user_id'] ) {
            redirect('/user/profile','refresh');
        }
        $this->form_validation->set_rules('login','Login','trim|required|xss_clean');
        $this->form_validation->set_rules('password','Password','trim|required|xss_clean');
        if( $this->form_validation->run() == TRUE )
        {
            $result = $this->user_model->login( $this->input->post('login'), $this->input->post('password') );
            if($result){
                redirect('user/profile');
            }else{
                redirect('user/login');
            }
        }else {
            $this->load->view('user/login_header');
            $this->load->view('user/login');
            $this->load->view('user/footer');
        }
    }
    public function reset()
    {
        if( $this->data['user_id'] ) {
            redirect('/user/profile','refresh');
        }
        $this->form_validation->set_rules('login','Login','trim|required|xss_clean');
        if( $this->form_validation->run() == TRUE )
        {
            $result = $this->user_model->reset_pass( $this->input->post('login') );
            if($result){
                redirect('user/login?m=true');
            }else{
                redirect('user/login');
            }
        }else {
            $this->load->view('user/login_header');
            $this->load->view('user/reset');
            $this->load->view('user/footer');
        }
    }

    public function documents()
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }

        $page = 0;
        if(!empty($_GET['page'])){
            $page = (int) $_GET['page'];
        }

        $this->data = $this->user_model->getListDocuments($page);
        $this->data['alert_save'] = empty($_GET['save'])?'false':'true';
        $this->data['page'] = $page;

        $this->load->view('user/header');
        $this->load->view('user/documents', $this->data);
        $this->load->view('user/footer');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        session_destroy();
        redirect('/user/login','refresh');
    }

    public function register()
    {

        $this->load->view('user/login_header');
        $this->load->view('user/register');
        $this->load->view('user/footer');

    }

    public function profile()
    {
        if( !$this->data['user_id'] ) {
            redirect('/user/login','refresh');
        }
        $this->data['user'] = $this->user_model->userInfo($this->data['user_id']);
        $this->data['info'] = $this->user_model->userGlobalInfo($this->data['user_id']);
        $this->load->view('user/header');
        $this->load->view('user/profile',$this->data);
        $this->load->view('user/footer');
    }
    public function subscription()
    {
        if( !$this->data['user_id'] ) {
            redirect('/user/login','refresh');
        }
        $this->load->view('user/header');
        $this->load->view('user/subscription');
        $this->load->view('user/footer');
    }

    public function payment_history()
    {
        if( !$this->data['user_id'] ) {
            redirect('/user/login','refresh');
        }
        $this->data['payments'] = $this->user_model->getUserPayments($this->data['user_id']);

        $this->load->view('user/header');
        $this->load->view('user/payment_history', $this->data);
        $this->load->view('user/footer');
    }
    public function save_profile()
    {
        if( !$this->data['user_id'] ) {
            redirect('/user/login','refresh');
        }
        $this->form_validation->set_rules('fio','FIO','trim|required|xss_clean');
        $this->form_validation->set_rules('about','About','trim|required|xss_clean');
        $this->form_validation->set_rules('email','email','trim|required|xss_clean');
        if( $this->form_validation->run() == TRUE )
        {
            $this->user_model->saveProfile( $this->input->post(), $this->data['user_id'] );
        }
        redirect('user/profile');
    }
}
