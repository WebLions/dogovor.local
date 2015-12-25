<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Document_model extends CI_Model
{
    //------------------------------------------------------------------------------------------------------------------
    // Тестовые переменные
       public $city_contract = "Одесса";
       public $day = "28";
       public $month = "ноября";
       public $year = "2028";
    //------------------------------------------------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();//Работа с бд
        $this->load->library('word');
    }
    //------------------------------------------------------------------------------------------------------------------
    //Функция вывода заголовка документа
    /*Анализирует лица, между которыми заключается договор и возвращает переменную, в которой содержиться правильный вариант текста*/
    public function set_header_doc()
    {

        return $header;
    }
    //------------------------------------------------------------------------------------------------------------------
    //договор купли-продажи транспортного средства
    public function get_doc_buy_sale()
    {
        // Подготовка
        $phpword = new $this->word->PHPWord();
        $document = $phpword->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/buy_sale_deal.docx');
        //$document = $PHPWord->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/buy_sale_deal.docx');

        // Задание значений
        $document->setValue('city_contract', $this->city_contract);
        $document->setValue('day', $this->day);
        $document->setValue('month', $this->month);
       /* $document->setValue('vendor_fio', $vendor_fio);
        $document->setValue('buyer_fio', $buyer_fio);
        $document->setValue('mark', $mark);
        $document->setValue('vin', $vin);
        $document->setValue('reg_number', $reg_number);
        $document->setValue('mark', $mark);
        $document->setValue('mark', $mark);
        $document->setValue('mark', $mark);
        $document->setValue('mark', $mark);
        $document->setValue('mark', $mark);
        $document->setValue('mark', $mark);
        $document->setValue('mark', $mark);*/

        // Сохранение результатов
        $name_of_file = '/documents/buy_sale/'. time() .'buy_sale_deal.docx';//Имя файла и путь к нему
        //setcookie('name_of_doc',$name_of_file);
        $document->save($name_of_file); // Сохранение документа
        echo 'File created.';
        //Запрос предмусмотрен на договор между двумя физическими лицами. Появится ещё несколько новых полей
       /* $this->db->query('SELECT city, day, month, year, vendor_fio, buyer_fio, mark, vin, reg_number, nametype, category, year_of_product, model, chassis, bodycar, color_bodycar, other_parametrs, additional_equip, serial_chars, serial_numbers, serial_bywho, serial_day, serial_month, serial_year, status_of_car, ts_day, ts_month, ts_year, ts_bywho, defects_all, defects_rightnow, price, price_str, day_of_pay, month_of_pay, year_of_pay, serial_car_chars, serial_car_numbers, day_car, month_car, year_car, other_documents_car, equipment_for_car, marriage_info, marriage_number, penalty_for_buyer, penalty_for_vendor, penalty_for_garanty, vendor_b_day, vendor_b_month, vendor_b_year, vendor_serial_ch, vendor_number_ser, vendor_ser_bywho, vendor_bywho_d, vendor_bywho_m, vendor_bywho_y, vendor_city, vendor_house, vendor_flat, vendor_phone, buyer_b_day, buyer_b_month, buyer_b_year, buyer_serial_ch, buyer_number_ser, buyer_ser_bywho, buyer_bywho_d, buyer_bywho_m, buyer_bywho_y, buyer_city, buyer_house, buyer_flat, buyer_phone
FROM buy_deal');*/



    }
    //------------------------------------------------------------------------------------------------------------------
    //договор дарения
    public function get_doc_gift()
    {

    }
    //------------------------------------------------------------------------------------------------------------------
    //акт приема-передачи автомобиля
    public function get_doc_act_of_reception()
    {

    }
    //------------------------------------------------------------------------------------------------------------------
    //расписка в получении денежных средств
    public function get_doc_receipt_of_money()
    {

    }
    //------------------------------------------------------------------------------------------------------------------
    //заявление в ГИБДД для смены собственника
    public function get_doc_statement_gibdd()
    {

    }
    //------------------------------------------------------------------------------------------------------------------
    //заявление продавца о согласии супруга
    public function get_doc_statement_vendor_marriage()
    {

    }
    //------------------------------------------------------------------------------------------------------------------
    //договор аренды
   /* public function get_doc_rent()
    {

    }*/
    //------------------------------------------------------------------------------------------------------------------

}

//----------------------------------------------------------------------------------------------------------------------
//Заготовка для обработки документа купли-продажи в случае брака продавца
// Если продавец в браке то
$marriage_info ="4.4. Продавец довел до Покупателя сведения о том, что транспортное средство приобретено им в период брака на совместные денежные средства с супругой(ом) __ ФИО __ и является совместным имуществом супругов. По заявлению Продавца договор заключается по обоюдному согласию супругов, Покупатель ознакомлен с содержанием указанного заявления. ";
$marriage_number = 5; //номер следующего пункта
//Если не в браке
$marriage_info = "";//пропускаем этот пункт
$marriage_number = 4; //номер следующего пункта
//----------------------------------------------------------------------------------------------------------------------

