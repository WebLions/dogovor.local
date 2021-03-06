<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Document extends CI_Controller
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('html','url'));
        $this->load->model('document_model');
        $this->load->model('user_model');
        $this->load->model('pay_model');
        $this->load->library('form_validation');
    }
    public function pay($id)
    {
        echo $this->pay_model->getPayLink($id);
    }
    public function buy_sale_buy_sale($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_doc_buy_sale( (int) $id );//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function gift_gift($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_gift_doc( (int) $id );//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    /*public function test_packset(){
        $this->document_model->testpack();
    }*/
    public function buy_sale_act_of_reception($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_doc_act_of_reception( (int) $id );//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function gift_act_of_reception($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_gift_act_of_reception( (int) $id );//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function gift_statement_gibdd($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_gift_gibbd($id);//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function buy_sale_receipt_of_money($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_doc_receipt_of_money($id);//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function buy_sale_statement_gibdd($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_doc_statement_gibdd($id);//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function buy_sale_marriage($id)  //  в ссылке выглядит так document/name
    {
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc'] = $this->document_model->get_doc_statement_vendor_marriage($id);//вызов нужно функции модели;
        if($this->data['doc'] != false){
            redirect($this->data['doc']);
        }
        redirect('/','refresh');
    }
    public function json()
    {
        $this->document_model->testjson();
    }
    public function select_from_database()
    {
        echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />';
        $this->document_model->select_from_database();
    }
    public function insert_into_database_buysale()
    {
        $this->document_model->insert_into_database_buysale();
    }
    public function check_post()
    {
        echo '<pre>';
        print_r($_POST);
        echo '</pre>';
    }

    public function save(){
        //Проверяем залогинен ли пользователь
        if( !$this->data['user_id'] ) {
            $this->form_validation->set_rules('email','E-mail','trim|required|xss_clean');
            if($this->form_validation->run() == true)
            {
                if( !$this->data['user_id'] ) {
                    $this->data['user_id'] = $this->user_model->register($this->input->post('email'));
                }
            }else{
                redirect('user/login');
            }
        }

        if($_POST['type_of_contract']=='buy_sell'){
            $doc_id = $this->document_model->insert_into_database_buysale();
            $table = "buy_sale";
        }
        if($_POST['type_of_contract']=='gift'){
            $doc_id = $this->document_model->insert_into_database_gift();
            $table = "gift";
        }

        //insert to documents return $doc_id:global
        $doc_id = $this->document_model->add_documents($doc_id,$this->data['user_id'],$table);

        if($this->user_model->checkSub( $this->data['user_id'], $doc_id )){
            redirect('user/documents');
        }else{
            $link = $this->pay_model->getPayLink($doc_id);
            redirect($link);
        }

    }
    public function check_login_user()
    {

        if( $this->data['user_id'] ) {
            // go_buy_sale()

        }else{
            //вывод формы ввода емаил
        }
    }

    public function create()
    {
        $this->load->view('user/document_header');
        $this->load->view('user/document');
        $this->load->view('user/footer');
    }
    public function data_for_canvas_buysale()
    {
        $this->document_model->get_data_for_canvas_buysale();//Возвращает json массив
    }
    public function data_for_canvas_gift()
    {
        $this->document_model->get_data_for_canvas_gift();//Возвращает json массив
    }
//    public function db_seed()
//    {
//        $result =  $this->document_model->set_pack_of_documents('law', 'law', 'gift', false);
//        var_dump ($result);
//    }
    public function edit($docum){
        if( !$this->data['user_id'] ) {
            redirect('/','refresh');
        }
        $this->data['doc_id'] = $docum;
        $type = $this->document_model->get_table_to_doc_id($this->data['doc_id']);

        $this->load->view('user/header');
        if($type=="buy_sale")
            $this->load->view('user/document_edit_bs', $this->data);
        if($type=="gift")
            $this->load->view('user/document_edit_gift', $this->data);
        $this->load->view('user/footer');
    }
    public function saveEdit(){
        $id = $_POST['doc_id'];
        $type = $this->document_model->get_table_to_doc_id($id);
        if($type=="buy_sale")
            $this->document_model->bs_save_edit($this->input->post(),$id);
        if($type=="gift")
            $this->document_model->gift_save_edit($this->input->post(),$id);
        redirect('/user/documents?save=true','refresh');
    }
}