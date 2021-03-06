<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);

class Document_model extends CI_Model
{
   //------------------------------------------------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();//Работа с бд
        $this->load->library('word');
    }
    //------------------------------------------------------------------------------------------------------------------
    // Запрос и присвание переменных с базы
    public function select_from_database()
    {
        $this->db->select();
        $query = $this->db->get('buy_sale');
        $result = $query->row();
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
               $result->$key = 'не указано';
            }
        }

        //Тестовый вывод содержимого результата
        echo '<pre>';
        print_r($result);
        //echo $result->place_o*f_contract;
        echo '</pre>';
    }
    //------------------------------------------------------------------------------------------------------------------
    public function num2str($num)
    {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('' ,'' ,'',	 1),
            array(''   ,''   ,''    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
       // $out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }
    public function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function get_month_from_number($number)
    {
        switch ($number)
        {
            case '01':
                $result = 'января';
                break;

            case '02':
                $result = 'февраля';
                break;

            case '03':
                $result = 'марта';
                break;

            case '04':
                $result = 'апреля';
                break;

            case '05':
                $result = 'мая';
                break;

            case '06':
                $result = 'июня';
                break;

            case '07':
                $result = 'июля';
                break;

            case '08':
                $result = 'августа';
                break;

            case '09':
                $result = 'сентября';
                break;

            case '10':
                $result = 'октября';
                break;

            case '11':
                $result = 'ноября';
                break;

            case '12':
                $result = 'декабря';
                break;

            default:
                $result = 'Ошибка. Введенно неверное число месяца';
                break;
        }
        return $result;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function get_sign($type_of_owner)
    {
        if ($type_of_owner == 'own_car') {
            $sign = '';
        }
        elseif ($type_of_owner == 'not_own_car'){
            $sign = 'по доверенности';
        }
        else {
            $sign = false;
        }

        return $sign;
    }
    //------------------------------------------------------------------------------------------------------------------
    protected function get_side_name($type_of_side, $owner_car, $namedata)
    {
        switch ($type_of_side)
        {
            case 'physical':
                $name = $namedata['phys_name'];
                break;
            case 'law':
                $name = $namedata['law_name'];
                break;
            case 'individual':
                $name = $namedata['ind_name'];
                break;
        }
        if ($owner_car == 'not_own_car')
            $name = $namedata['agent_name'];
        return $name;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function format_date($date, $brd = false)
    {
        if(empty($date) || $date == 'не указано'){
            return false;
        };

        if ($brd == true) {$brd = ' года';}
        else {$brd = 'г.';}

        $date = DateTime::createFromFormat('Y-m-d', $date);
        $day = $date->format('d');
        $month = $date->format('m');
        $month = $this->get_month_from_number($month);
        $year = $date->format('Y');
        $date = '"'. $day . '" ' . $month . ' ' . $year.$brd;
        return $date;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function format_adress($city, $street, $house, $flat)
    {
        $adress =   "г.$city, ул.$street, д.$house, кв.$flat";
        return $adress;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function format_fio($surname, $name, $patronymic)
    {
        $fio = $surname .' '. $name . ' '. $patronymic;
        return $fio;
    }
    //------------------------------------------------------------------------------------------------------------------
    public function format_shortfio($surname=string, $name=string, $patronymic=string)
    {
        $fio = $surname .' '. mb_substr($name, 0,1) . '. '. mb_substr($patronymic,0,1).'.';
        return $fio;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function json_to_string($target)
    {
        $target = json_decode($target);
        if (empty($target) == true) {return'не указано';}
        ///
//        $quantity = count($target);
//        $last_element = $quantity-1;//Ибо счет с нуля
//        if ($quantity == 1)
//        {
//            $string = $target[0];
//        }
//        elseif ($quantity > 1)
//        {
//            $string = $target[0];
//            for ($i = 1; $i<$quantity-1; $i++)
//            {
//                $string .= "; " . $target[$i];
//            }
//            $string .= "; " . $target[$last_element] . ".";
//        }
        ///
        $target = join("; ", $target);
        return $target;
    }
    //------------------------------------------------------------------------------------------------------------------
    public function json_to_string_accessories($target)
    {
        if (empty($target)) return 'не указано';
        $string = "";
        $target = json_decode($target);
//        $last_element = array_pop($target);
        foreach ($target as $value)
        {
            if (is_array($value) && !empty($value[0]))
                {
//                    foreach ($value as $skey => $svalue)
//                    {
//                        if (!next($value))
//                        {
//                            $string .= " в количестве $svalue шт.";
//                        }
//                        else
//                        {
//                            $string .= $svalue;
//                        }
//                    }
                    $middle_element = array_pop($value);
                    foreach ($value as $mvalue)
                    {
                        $string .= $mvalue;
                    }
                    $string .= ' в количестве '.$middle_element.'('.$this->num2str($middle_element).') '.'шт.; ';
                }
            elseif (is_object($value))
            {
                continue;
            }
            else
            {
                $string .= $value .'; ';
            }
        }
//        // Последний элемент
//        if (is_array($last_element))
//        {
//            $end_element = array_pop($last_element);
//            foreach ($last_element as $lvalue)
//            {
//                $string .= $lvalue;
//            }
//            $string .= ' в количестве '.$end_element.'шт.';
//        }
//        else
//        {
//            $string .= $last_element . '.';
//        }
        return $string;
    }
    //------------------------------------------------------------------------------------------------------------------
    public function set_pack_of_documents($giver, $taker, $type_of_document, $gibdd, $marriage=false )
    {
//        /**
//         * КП
//         * 1 - кп, акт, расписка, гибдд, супруга (ф-ф /ф-и /ф-ю /и-и /и-ф /и-ю)
//         * 2 - кп, акт, гибдд (ю-ю)
//         * 3 - кп, акт, расписка, гибдд (ю-ф /ю-и) или (когда нет супруги)
//         * 4 - кп, акт, расписка, супруга (когда нет гибдд)
//         * 5 - кп, акт, расписка (когда нет ни супруги, ни гибдд)
//         * 6 - кп, акт (для юр лиц когда нет гибдд)
//         * _____________________________________________________________________
//         *
//         * Дарение
//         * 11 - дарение, гибдд (когда ф/и-ф/и)
//         * 12 - дарение, акт, гибдд (когда ф/и-ю)
//         * 13 - дарение (когда нет гибдд)
//         * 14 - дарение, акт (когда нет гибдд у юр лица)
//         *________________________________________________________________________
//         * Миграция:
//         *
//         *  TRUNCATE TABLE  `types`
//         *  ALTER TABLE  `types` ADD  `index` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
//         *  INSERT INTO `project_dogovor`.`types` (`index`, `id`, `document_name`, `url`) VALUES (NULL, '1', 'Договор купли-продажи', 'buy_sale'), (NULL, '1', 'Акт приема-передачи', 'act_of_reception'), (NULL, '1', 'Расписка в получении денег', 'receipt_of_money'), (NULL, '1', 'Заявление в ГИБДД', 'statement_gibdd'), (NULL, '1', 'Заявление продавца о согласии супруга', 'marriage'), (NULL, '2', 'Договор купли-продажи', 'buy_sale'), (NULL, '2', 'Акт приема-передачи', 'act_of_reception'), (NULL, '2', 'Заявление в ГИБДД', 'statement_gibdd');
//         *  INSERT INTO `project_dogovor`.`types` (`index`, `id`, `document_name`, `url`) VALUES (NULL, '3', 'Договор купли-продажи', 'buy_sale'), (NULL, '3', 'Акт приема-передачи', 'act_of_reception'), (NULL, '3', 'Расписка в получении денег', 'receipt_of_money'), (NULL, '3', 'Заявление в ГИБДД', 'statement_gibdd'), (NULL, '4', 'Договор купли-продажи', 'buy_sale'), (NULL, '4', 'Акт приема-передачи', 'act_of_reception'), (NULL, '4', 'Расписка в получении денег', 'receipt_of_money'), (NULL, '4', 'Заявление продавца о согласии супруга', 'marriage');
//         *  INSERT INTO `project_dogovor`.`types` (`index`, `id`, `document_name`, `url`) VALUES (NULL, '5', 'Договор купли-продажи', 'buy_sale'), (NULL, '5', 'Акт приема-передачи', 'act_of_reception'), (NULL, '5', 'Расписка в получении денег', 'receipt_of_money'), (NULL, '6', 'Договор купли-продажи', 'buy_sale'), (NULL, '6', 'Акт приема-передачи', 'act_of_reception'), (NULL, '11', 'Договор дарения', 'gift'), (NULL, '11', 'Заявление в ГИБДД', 'statement_gibdd'), (NULL, '12', 'Договор дарения', 'gift'), (NULL, '12', 'Акт приема-передачи', 'act_of_reception'), (NULL, '12', 'Заявление в ГИБДД', 'statement_gibdd');
//         *  INSERT INTO `project_dogovor`.`types` (`index`, `id`, `document_name`, `url`) VALUES (NULL, '13', 'Договор дарения', 'gift'), (NULL, '14', 'Договор дарения', 'gift'), (NULL, '14', 'Акт приема-передачи', 'act_of_reception');
//         *
//         */
//        if ($type_of_document == 'buy_sell')
//        {
//
//            if ($giver == 'physical' && $taker == 'physical') {
//                $id_type = 1;
//                if ($gibdd == 'false') $id_type = 7;
//                if ($marriage == 'false' || $marriage = null) $id_type = 9;
//                if ($gibdd == 'false' && $marriage == 'false' || $marriage = null) $id_type = 8;
//            } elseif ($giver == 'individual' && $taker == 'individual') {
//                $id_type = 1;
//                if ($gibdd == 'false') $id_type = 7;
//                if ($marriage == 'false' || $marriage = null) $id_type = 9;
//                if ($gibdd == 'false' && $marriage == 'false' || $marriage = null) $id_type = 8;
//            } elseif ($giver == 'law' && $taker == 'law') {
//                $id_type = 2;
//                if ($gibdd == 'false') $id_type = 15;
//            } elseif ($giver == 'law' && $taker == 'individual') {
//                $id_type = 4;
//                if ($gibdd == 'false') $id_type = 13;
//            } elseif ($giver == 'physical' && $taker == 'law') {
//                $id_type = 3;
//                if ($gibdd == 'false') $id_type = 7;
//                if ($marriage == 'false' || $marriage = null) $id_type = 9;
//                if ($gibdd == 'false' && $marriage == 'false' || $marriage = null) $id_type = 8;
//            } elseif ($giver == 'physical' && $taker == 'individual') {
//                $id_type = 3;
//                if ($gibdd == 'false') $id_type = 7;
//                if ($marriage == 'false' || $marriage = null) $id_type = 9;
//                if ($gibdd == 'false' && $marriage == 'false' || $marriage = null) $id_type = 8;
//            } elseif ($giver == 'individual' && $taker == 'physical') {
//                $id_type = 4;
//                if ($gibdd == 'false') $id_type = 13;
//            } elseif ($giver == 'law' && $taker == 'physical') {
//                $id_type = 4;
//                if ($gibdd == 'false') $id_type = 13;
//            } elseif ($giver == 'individual' && $taker == 'law') {
//                $id_type = 1;
//                if ($gibdd == 'false') $id_type = 7;
//                if ($marriage == 'false' || $marriage = null) $id_type = 9;
//                if ($gibdd == 'false' && $marriage == 'false' || $marriage = null) $id_type = 8;
//            } elseif ($giver == 'physical' && $taker == 'law') {
//                $id_type = 1;
//                if ($gibdd == 'false') $id_type = 7;
//                if ($marriage == 'false' || $marriage = null) $id_type = 9;
//                if ($gibdd == 'false' && $marriage == 'false' || $marriage = null) $id_type = 8;
//            } else $id_type = false;
//        }
//        if ($type_of_document == 'gift')
//        {
//            if ($giver == 'physical' && $taker == 'physical')
//            {
//                $id_type = 5;
//                if ($gibdd == 'false') $id_type = 14;
//            }
//            elseif ($giver == 'physical' && $taker == 'individual')
//            {
//                $id_type = 5;
//                if ($gibdd == 'false') $id_type = 14;
//            }
//            elseif ($giver == 'individual' && $taker == 'physical')
//            {
//                $id_type = 5;
//                if ($gibdd == 'false') $id_type = 14;
//            }
//            elseif ($giver == 'individual' && $taker == 'individual')
//            {
//                $id_type = 5;
//                if ($gibdd == 'false') $id_type = 14;
//            }
//            elseif ($giver == 'physical' && $taker == 'law')
//            {
//                $id_type = 6;
//                if ($gibdd == 'false') $id_type = 15;
//            }
//            elseif ($giver == 'individual' && $taker == 'law')
//            {
//                $id_type = 6;
//                if ($gibdd == 'false') $id_type = 15;
//            }
//            elseif ($giver == 'law' && $taker == 'physical')
//            {
//                $id_type = 6;
//                if ($gibdd == 'false') $id_type = 15;
//            }
//            elseif ($giver == 'law' && $taker == 'individual')
//            {
//                $id_type = 6;
//                if ($gibdd == 'false') $id_type = 15;
//            }
//            elseif ($giver == 'law' && $taker == 'law')
//            {
//                $id_type = 6;
//                if ($gibdd == 'false') $id_type = 15;
//            }
//            else $id_type = false;
//        }

        if ($gibdd == null) $gibdd = false;
        if ($marriage == null) $marriage = false;

        $this->db->select('type_id');
        $this->db->where('type_of_document', $type_of_document);
        $this->db->where('giver', $giver);
        $this->db->where('taker', $taker);
        $this->db->where('gibdd', filter_var($gibdd, FILTER_VALIDATE_BOOLEAN));
        $this->db->where('marriage', filter_var($marriage, FILTER_VALIDATE_BOOLEAN));
        $query = $this->db->get('types_options');
        $result = $query->row();
        $id_type = 1;
        if (isset($result->type_id)) {
            $id_type = $result->type_id;
        }
        return $id_type;
    }
    //------------------------------------------------------------------------------------------------------------------
    public function db_seed()
    {
        $data = array();
        $a = array('physical', 'law', 'individual');
        foreach ($a as $first){
            foreach ($a as $second){
                for ($i = 0; $i<2; $i++){
                    for ($j = 0; $j<2; $j++){
                        $buffer = array(
                        'giver' => $first,
                            'taker' => $second,
                            'gibdd'=> $i,
                            'type_of_document' => 'gift'
                        );
                        $data[] = $buffer;
                    }
                }
            }
        }

        $this->db->insert_batch('types_options', $data);

//        print_r($data);
    }
    //------------------------------------------------------------------------------------------------------------------
    //Функция вывода заголовка документа
    /*Анализирует лица, между которыми заключается договор и возвращает переменную, в которой содержиться правильный вариант текста*/
    private function set_header_doc($type_of_contract, $type_of_vendor, $type_of_buyer,$data_for_header, $canvas = false) //law //physical //individual
    {
        $bold_start = '';
        $bold_end = '';
//        if(!$canvas) {
//            $bold_start = '<w:rPr><w:b/></w:rPr>';
//            $bold_end = '</w:t></w:r><w:r><w:t>';
//        }

        switch ($type_of_contract)
        {
            case 'buy_sell':
                $first_person = 'Продавец';
                $second_person = 'Покупатель';
                break;
            case 'gift':
                $first_person = 'Даритель';
                $second_person = 'Одаряемый';

        }
        //Физ.лицо шаблоны
        $phys = array(
            'vendor' => array(
                'own' => $bold_start. 'Гражданин ' . $data_for_header['vendor_fio']. $bold_end .', далее именуемый "'.$bold_start.$first_person.$bold_end.'", с одной стороны и ',

                'not_own' => $bold_start.'Гражданин '.$data_for_header['vendor_agent_fio'].$bold_end.', '.$data_for_header['agent_vendor_birthday'].' рождения, паспорт серии '.$data_for_header['agent_vendor_pass_serial'].' №'.$data_for_header['agent_vendor_pass_number'].' выдан '.$data_for_header['agent_vendor_pass_date'].' '.$data_for_header['agent_vendor_pass_bywho'].', зарегистирован по адресу: '.$data_for_header['agent_vendor_adress'].', действующий от имени гражданина '.$data_for_header['vendor_fio_parent'].' на основании доверенности от '.$data_for_header['for_agent_vendor_proxy_date'].' №'.$data_for_header['for_agent_vendor_proxy_number'].', выданной '.$data_for_header['for_agent_vendor_proxy_notary'].', далее именуемый "'.$bold_start.$first_person.$bold_end.'", с одной стороны и ',
            ),
            'buyer' => array(
                'own' => $bold_start. 'гражданин ' . $data_for_header['buyer_fio']. $bold_end . ', далее именуемый "'.$bold_start.$second_person.$bold_end.'", с другой стороны, совместно в дальнейшем именуемые "Стороны", заключили настоящий договор (далее - Договор) о нижеследующем:',

                'not_own' => $bold_start.'гражданин '.$data_for_header['buyer_agent_fio'].$bold_end.', '.$data_for_header['for_agent_proxy_birthday'].' рождения, паспорт серии '.$data_for_header['for_agent_proxy_pass_serial'].' №'.$data_for_header['for_agent_proxy_pass_number'].' выдан '.$data_for_header['for_agent_proxy_pass_date'].' '.$data_for_header['for_agent_proxy_pass_bywho'].', зарегистирован по адресу: '.$data_for_header['for_agent_proxy_adress'].', действующий от имени гражданина '.$data_for_header['buyer_fio_parent'].' на основании доверенности от '.$data_for_header['for_agent_buyer_proxy_date'].' №'.$data_for_header['for_agent_buyer_proxy_number'].', выданной '.$data_for_header['for_agent_buyer_proxy_notary'].', далее именуемый "'.$bold_start.$second_person.$bold_end.'", с другой стороны, совместно в дальнейшем именуемые "Стороны", заключили настоящий договор (далее - Договор) о нижеследующем:',
            ),
        );

        //Юр лицо шаблоны
        $law = array(
            'vendor' => array(
                'own' => $bold_start.$data_for_header['vendor_law_company_name'].$bold_end.', далее именуемое "'.$bold_start.$first_person.$bold_end.'", в лице '. $data_for_header['vendor_law_actor_position'].' '. $data_for_header['vendor_law_fio'].', действующего на основании '. $data_for_header['vendor_law_document_osn'].' , с одной стороны, и ',

                'not_own' => $bold_start.$data_for_header['vendor_law_company_name'].$bold_end.', далее именуемое "'.$bold_start.$second_person.$bold_end.'", в лице '. $data_for_header['vendor_agent_fio_parent'].', действующего на основании доверености №'. $data_for_header['for_agent_vendor_proxy_number'].' от '.$data_for_header['for_agent_vendor_proxy_date'].', с одной стороны, и ',
            ),
            'buyer' => array(
                'own' => $bold_start.$data_for_header['buyer_law_company_name'].$bold_end.', далее именуемое "'.$bold_start.$second_person.$bold_end.'", в лице ' . $data_for_header['buyer_law_actor_position'].' '. $data_for_header['buyer_law_fio'].', действующего на основании '. $data_for_header['buyer_law_document_osn'].', с другой стороны, совместно в дальнейшем именуемые "Стороны", заключили настоящий договор (далее - Договор) о нижеследующем:',

                'not_own' => $bold_start.$data_for_header['buyer_law_company_name'].$bold_end.', далее именуемое "'.$bold_start.$second_person.$bold_end.'", в лице '. $data_for_header['buyer_agent_fio_parent'].', действующего на основании доверености №'. $data_for_header['for_agent_buyer_proxy_number'].' от '.$data_for_header['for_agent_buyer_proxy_date'].', с другой стороны, совместно в дальнейшем именуемые "Стороны", заключили настоящий договор (далее - Договор) о нижеследующем:',
            ),
        );
        //Инд лицо шаблоны
        $ind= array(
            'vendor' => array(
                'own' => 'Индивидуальный предприниматель '.$bold_start.$data_for_header['vendor_ind_fio'].$bold_end.', действующий на основании свидетельства от '.$data_for_header['vendor_date_of_certificate'].' № '.$data_for_header['vendor_number_of_certificate'].', далее именуемый "'.$bold_start.$first_person.$bold_end.', с одной стороны и ',
                'not_own' => $bold_start.'Гражданин '.$data_for_header['vendor_agent_fio'].$bold_end.', '.$data_for_header['agent_vendor_birthday'].' рождения, паспорт серии '.$data_for_header['agent_vendor_pass_serial'].' №'.$data_for_header['agent_vendor_pass_number'].' выдан '.$data_for_header['agent_vendor_pass_date'].' '.$data_for_header['agent_vendor_pass_bywho'].', зарегистирован по адресу: '.$data_for_header['agent_vendor_adress'].', действующий от имени индивидуального предпринимателя '.$data_for_header['vendor_ind_fio_parent'].' на основании доверенности от '.$data_for_header['for_agent_vendor_proxy_date'].' №'.$data_for_header['for_agent_vendor_proxy_number'].', выданной '.$data_for_header['for_agent_vendor_proxy_notary'].', далее именуемый "'.$bold_start.$first_person.$bold_end.'", с одной стороны и ',
            ),
            'buyer' => array(
                'own' => 'Индивидуальный предприниматель '.$bold_start.$data_for_header['buyer_ind_fio'].$bold_end.', действующий на основании свидетельства от '.$data_for_header['buyer_date_of_certificate'].' №'.$data_for_header['buyer_number_of_certificate'].', далее именуемый "'.$bold_start.$second_person.$bold_end.', с другой стороны, совместно в дальнейшем именуемые "Стороны", заключили настоящий договор (далее - Договор) о нижеследующем:',

                'not_own' => $bold_start.'гражданин '.$data_for_header['buyer_agent_fio'].$bold_end.', '.$data_for_header['for_agent_proxy_birthday'].' рождения, паспорт серии '.$data_for_header['for_agent_proxy_pass_serial'].' №'.$data_for_header['for_agent_proxy_pass_number'].' выдан '.$data_for_header['for_agent_proxy_pass_date'].' '.$data_for_header['for_agent_proxy_pass_bywho'].', зарегистирован по адресу: '.$data_for_header['for_agent_proxy_adress'].', действующий от имени индивидуального предпринимателя '.$data_for_header['buyer_ind_fio_parent'].' на основании доверенности от '.$data_for_header['for_agent_buyer_proxy_date'].' №'.$data_for_header['for_agent_buyer_proxy_number'].', выданной '.$data_for_header['for_agent_buyer_proxy_notary'].', далее именуемый "'.$bold_start.$second_person.$bold_end.'", с другой стороны, совместно в дальнейшем именуемые "Стороны", заключили настоящий договор (далее - Договор) о нижеследующем:',
            ),
        );
        if ($type_of_vendor == 'physical' && $type_of_buyer == 'physical')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $phys['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $phys['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $phys['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $phys['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'law' && $type_of_buyer == 'law')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $law['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $law['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $law['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $law['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'physical' && $type_of_buyer == 'law')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $phys['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $phys['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $law['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $law['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'law' && $type_of_buyer == 'physical')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $law['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $law['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $phys['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $phys['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'physical' && $type_of_buyer == 'individual')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $phys['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $phys['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $ind['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $ind['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'individual' && $type_of_buyer == 'physical')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $ind['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $ind['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $phys['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $phys['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'law' && $type_of_buyer == 'individual')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $law['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $law['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $ind['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $ind['buyer']['not_own'];
                    break;
            }
        }
        elseif ($type_of_vendor == 'individual' && $type_of_buyer == 'law')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $ind['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $ind['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $law['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $law['buyer']['not_own'];
                    break;
            };
        }
        elseif ($type_of_vendor == 'individual' && $type_of_buyer == 'individual')
        {
            switch ($data_for_header['vendor_is_owner_car'])
            {
                case 'own_car':
                    $header = $ind['vendor']['own'];
                    break;
                case 'not_own_car':
                    $header = $ind['vendor']['not_own'];
                    break;
            };
            switch ($data_for_header['buyer_is_owner_car'])
            {
                case 'own_car':
                    $header .= $ind['buyer']['own'];
                    break;
                case 'not_own_car':
                    $header .= $ind['buyer']['not_own'];
                    break;
            }
        };
        return $header;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function get_marriage_info($car_in_marriage, $spouse_fio, $canvas=false)
    {
        $marriage = array();
        $enter = "</w:t></w:r></w:p><w:p><w:r><w:t>";
        if ($canvas  == true){
            $enter = "^+";
        };
        if ($car_in_marriage == 'true')
        {
            // Если продавец в браке то
            $marriage['info'] ="$enter"."    4.4. Продавец довел до Покупателя сведения о том, что транспортное средство приобретено им в период брака на совместные денежные средства принадлежащие ему(ей) и супруге(у) ".$spouse_fio." и является совместным имуществом супругов. По заявлению Продавца договор заключается по обоюдному согласию супругов, Покупатель ознакомлен с содержанием указанного заявления. ";
            $marriage['number'] = 5; //номер следующего пункта
        }
//        elseif ($car_in_marriage == 'false')
        else
        {
            //Если не в браке
            $marriage['info'] = "";//пропускаем этот пункт
            $marriage['number'] = 4; //номер следующего пункта
        }

        return $marriage;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function get_requisites($data, $canvans = false)
    {

        $enter = "</w:t><w:br/><w:t>";
        if($canvans == true){$enter = '^+';}
        $output = ""; //Строка вывода
        switch ($data['type_of_side'])
        {
            case 'physical':
                $output =  "{$data['fio']} $enter";
                $output .= "{$data['date']} рождения $enter";
                $output .= "паспорт серии {$data['document_serial']} №{$data['document_number']} выдан {$data['document_date']} {$data['document_bywho']}  $enter";
                $output .= "Адрес: {$data['adress']}";
                if (($data['phone'] != null) || ($data['phone'] !='не указано' ))
                {
                    $output .= "$enter"."Телефон: {$data['phone']}";
                }
                break;

            case 'law':
                $output = "{$data['name']} $enter";
                $output .= "{$data['adress']} $enter";
                $output .= "ИНН {$data['inn']}, ОГРН {$data['ogrn']}  $enter";
                $output .= "р/счёт {$data['acc']} в банке {$data['bank_name']}$enter";
                $output .= "кор. счет {$data['korr_acc']} $enter";
                $output .= "БИК {$data['bik']}";
                if (($data['phone'] != null) || ($data['phone'] !='не указано' ))
                {
                    $output .= "$enter"."Телефон: {$data['phone']}";

                }
                break;

            case 'individual':
                $output = "ИП {$data['fio']} $enter";
                $output .="{$data['date']} рождения $enter";
                $output .= "Свидетельство №{$data['number_of_certificate']} от {$data['date_of_certificate']} $enter";
                $output .="паспорт серии {$data['document_serial']} №{$data['document_number']} выдан {$data['document_date']} {$data['document_bywho']}  $enter";
                $output .="Адрес: {$data['adress']} $enter";
                $output .= "р/счёт {$data['acc']} в банке {$data['bank_name']}$enter";
                $output .= "кор. счет: {$data['korr_acc']} $enter";
                $output .= "БИК {$data['bik']}";
                if (($data['phone'] != null) || ($data['phone'] !='не указано' ))
                {
                    $output .= "$enter"."Телефон: {$data['phone']}";
                }
                break;
        }
        return $output;
    }
    //------------------------------------------------------------------------------------------------------------------
    //договор дарения
    public function get_gift_doc($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='gift'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=gift.id");
        $query = $this->db->get('gift');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        //Подготовка
        //Фио
        $vendor_fio = $this->format_fio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
//        $spouse_fio = $this->format_fio($result->spouse_surname,$result->spouse_name,$result->spouse_patronymic);
        $vendor_law_fio = $this->format_fio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $vendor_ind_fio = $this->format_fio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $vendor_agent_fio = $this->format_fio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Короткое фио
        $short_vendor_fio = $this->format_shortfio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $short_buyer_fio = $this->format_shortfio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $short_vendor_law_fio = $this->format_shortfio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $short_buyer_law_fio = $this->format_shortfio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $short_vendor_ind_fio = $this->format_shortfio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $short_buyer_ind_fio = $this->format_shortfio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $short_vendor_agent_fio = $this->format_shortfio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $short_buyer_agent_fio = $this->format_shortfio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Родительское фио
        $vendor_law_fio_parent = $this->format_fio($result->vendor_law_actor_surname_parent,$result->vendor_law_actor_name_parent,$result->vendor_law_actor_patronymic_parent);
        $buyer_law_fio_parent = $this->format_fio($result->buyer_law_actor_surname_parent,$result->buyer_law_actor_name_parent,$result->buyer_law_actor_patronymic_parent);
        $vendor_fio_parent = $this->format_fio($result->vendor_surname_parent, $result->vendor_name_parent, $result->vendor_patronymic_parent);
        $buyer_fio_parent = $this->format_fio($result->buyer_surname_parent,$result->buyer_name_parent,$result->buyer_patronymic_parent);
        $vendor_ind_fio_parent = $this->format_fio($result->vendor_ind_surname_parent,$result->vendor_ind_name_parent,$result->vendor_ind_patronymic_parent);
        $buyer_ind_fio_parent = $this->format_fio($result->buyer_ind_surname_parent,$result->buyer_ind_name_parent,$result->buyer_ind_patronymic_parent);
        $vendor_agent_fio_parent = $this->format_fio($result->for_agent_vendor_surname_parent,$result->for_agent_vendor_name_parent,$result->for_agent_vendor_patronymic_parent);
        $buyer_agent_fio_parent = $this->format_fio($result->for_agent_buyer_surname_parent,$result->for_agent_buyer_name_parent,$result->for_agent_buyer_patronymic_parent);

        //Дата
        $date_of_contract = $this->format_date($result->date_of_contract);
        $date_of_serial_car = $this->format_date($result->date_of_serial_car);
        $vendor_birthday = $this->format_date($result->vendor_birthday, true);
        $buyer_birthday = $this->format_date($result->buyer_birthday, true);
        $vendor_ind_birthday = $this->format_date($result->vendor_ind_birthday, true);
        $buyer_ind_birthday = $this->format_date($result->buyer_ind_birthday, true);
        $vendor_ind_date_of_certificate = $this->format_date($result->vendor_ind_date_of_certificate);
        $buyer_ind_date_of_certificate = $this->format_date($result->buyer_ind_date_of_certificate);
        $vendor_passport_date  = $this->format_date($result->vendor_passport_date);
        $for_agent_vendor_proxy_date = $this->format_date($result->for_agent_vendor_proxy_date);
        $for_agent_buyer_proxy_date = $this->format_date($result->for_agent_buyer_proxy_date);
        $vendor_ind_passport_date = $this->format_date($result->vendor_ind_passport_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        $buyer_law_proxy_date = $this->format_date($result->buyer_law_proxy_date);
        $vendor_law_proxy_date = $this->format_date($result->vendor_law_proxy_date);
        //Новая дата
        $agent_vendor_birthday = $this->format_date($result->agent_vendor_birthday, true);
        $agent_vendor_pass_date = $this->format_date($result->agent_vendor_pass_date);
        $for_agent_proxy_birthday = $this->format_date($result->for_agent_proxy_birthday, true);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);
        //Правки даты
        $vendor_law_date_of_create = $this->format_date($result->vendor_law_date_of_create);
        $buyer_law_date_of_create = $this->format_date($result->buyer_law_date_of_create);
        //Адрес
        $vendor_adress = $this->format_adress($result->vendor_city,$result->vendor_street,$result->vendor_house,$result->vendor_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        //$vendor_law_adress = $this->format_adress($result->vendor_law_city,$result->vendor_law_street,$result->vendor_law_house,$result->vendor_law_flat);
        //$buyer_law_adress = $this->format_adress($result->buyer_law_city,$result->buyer_law_street,$result->buyer_law_house,$result->buyer_law_flat);
        $vendor_ind_adress = $this->format_adress($result->vendor_ind_city,$result->vendor_ind_street,$result->vendor_ind_house,$result->vendor_ind_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
        //Новые адресса
        $agent_vendor_adress = $this->format_adress($result->agent_vendor_city,$result->agent_vendor_street,$result->agent_vendor_house,$result->agent_vendor_flat);
        $for_agent_proxy_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        //Иное
        $data_for_header = array(
            'vendor_fio' =>$vendor_fio,
            'buyer_fio' =>$buyer_fio,
            'vendor_law_company_name' => $result->vendor_law_company_name,
            'vendor_law_actor_position' => $result->vendor_law_actor_position,
//            'vendor_law_fio' =>$vendor_law_fio,
            'vendor_law_fio' =>$vendor_law_fio_parent,
            'vendor_law_document_osn' => $result->vendor_law_document_osn,
            'vendor_law_proxy_number' => $result->vendor_law_proxy_number,
            'vendor_law_proxy_date' => $vendor_law_proxy_date,
            'buyer_law_company_name' => $result->buyer_law_company_name,
            'buyer_law_actor_position' => $result->buyer_law_actor_position,
//            'buyer_law_fio' =>$buyer_law_fio,
            'buyer_law_fio' =>$buyer_law_fio_parent,
            'buyer_law_document_osn' => $result->buyer_law_document_osn,
            'buyer_law_proxy_number' => $result->buyer_law_proxy_number,
            'buyer_law_proxy_date' => $buyer_law_proxy_date,
            'vendor_ind_fio' =>$vendor_ind_fio,
            'vendor_number_of_certificate' => $result->vendor_ind_number_of_certificate,
            'vendor_date_of_certificate' => $vendor_ind_date_of_certificate,
            'buyer_ind_fio' =>$buyer_ind_fio,
            'buyer_number_of_certificate' => $result->buyer_ind_number_of_certificate,
            'buyer_date_of_certificate' => $buyer_ind_date_of_certificate,
            'vendor_is_owner_car' => $result->vendor_is_owner_car,
            'buyer_is_owner_car' => $result->buyer_is_owner_car,
            'vendor_agent_fio' =>$vendor_agent_fio,
            'for_agent_vendor_proxy_number' => $result->for_agent_vendor_proxy_number,
            'for_agent_vendor_proxy_date' => $for_agent_vendor_proxy_date,
            'for_agent_vendor_proxy_notary' => $result->for_agent_vendor_proxy_notary,
            'buyer_agent_fio' =>$buyer_agent_fio,
            'for_agent_buyer_proxy_number' => $result->for_agent_buyer_proxy_number,
            'for_agent_buyer_proxy_date' => $for_agent_buyer_proxy_date,
            'for_agent_buyer_proxy_notary' => $result->for_agent_buyer_proxy_notary,
            //Новые данные для реквизитов
            'agent_vendor_birthday' => $agent_vendor_birthday,
            'agent_vendor_pass_serial' => $result->agent_vendor_pass_serial,
            'agent_vendor_pass_number' => $result->agent_vendor_pass_number,
            'agent_vendor_pass_date' => $agent_vendor_pass_date,
            'agent_vendor_pass_bywho' => $result->agent_vendor_pass_bywho,
            'agent_vendor_adress' => $agent_vendor_adress,
            'for_agent_proxy_birthday' => $for_agent_proxy_birthday,
            'for_agent_proxy_pass_serial' => $result->for_agent_proxy_pass_serial,
            'for_agent_proxy_pass_number' => $result->for_agent_proxy_pass_number,
            'for_agent_proxy_pass_bywho' => $result->for_agent_proxy_pass_bywho,
            'for_agent_proxy_pass_date' => $for_agent_proxy_pass_date,
            'for_agent_proxy_adress' => $for_agent_proxy_adress,
            //Ещё правки
            'vendor_birthday' => $vendor_birthday,
            'buyer_birthday' => $buyer_birthday,
            'vendor_ind_birthday' => $vendor_ind_birthday,
            'buyer_ind_birthday' => $buyer_ind_birthday,
            'vendor_passport_serial' => $result->vendor_passport_serial,
            'vendor_passport_number' => $result->vendor_passport_number,
            'vendor_passport_bywho' => $result->vendor_passport_bywho,
            'vendor_passport_date' => $vendor_passport_date,
            'vendor_adress' => $vendor_adress,
            'vendor_ind_passport_serial' => $result->vendor_ind_passport_serial,
            'vendor_ind_passport_number' => $result->vendor_ind_passport_number,
            'vendor_ind_passport_bywho' => $result->vendor_ind_passport_bywho,
            'vendor_ind_passport_date' => $vendor_ind_passport_date,
            'vendor_ind_adress'=> $vendor_ind_adress,
            'buyer_passport_serial'=> $result->buyer_passport_serial,
            'buyer_passport_number'=> $result->buyer_passport_number,
            'buyer_passport_bywho'=> $result->buyer_passport_bywho,
            'buyer_passport_date'=> $buyer_passport_date,
            'buyer_adress'=> $buyer_adress,
            'buyer_ind_passport_serial' => $result->buyer_ind_passport_serial,
            'buyer_ind_passport_number' => $result->buyer_ind_passport_number,
            'buyer_ind_passport_bywho' => $result->buyer_ind_passport_bywho,
            'buyer_ind_passport_date' => $buyer_ind_passport_date,
            'buyer_ind_adress'=> $buyer_ind_adress,
            'vendor_fio_parent'=> $vendor_fio_parent,
            'buyer_fio_parent'=> $buyer_fio_parent,
            'vendor_ind_fio_parent'=> $vendor_ind_fio_parent,
            'buyer_ind_fio_parent'=> $buyer_ind_fio_parent,
            //
            'vendor_agent_fio_parent'=> $vendor_agent_fio_parent,
            'buyer_agent_fio_parent'=> $buyer_agent_fio_parent,
        );
        $header_doc = $this->set_header_doc($result->type_of_contract ,$result->type_of_giver, $result->type_of_taker, $data_for_header);
        unset($data_for_header);
        //Реквизиты
        //Продавец
        switch ($result->type_of_giver)
        {
            case 'physical':
                $data_for_req_giver = array
                (
                    'type_of_side' => $result->type_of_giver,
                    'fio' => $vendor_fio,
                    'date' => $vendor_birthday,
                    'document_serial' => $result->vendor_passport_serial,
                    'document_number' => $result->vendor_passport_number,
                    'document_bywho' => $result->vendor_passport_bywho,
                    'document_date' => $vendor_passport_date,
                    'adress' => $vendor_adress,
                    'phone' => $result->vendor_phone,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'name'=> $result->vendor_law_company_name,
                    'inn'=> $result->vendor_law_inn,
                    'ogrn'=> $result->vendor_law_ogrn,
                    'adress'=> $result->vendor_law_adress,
                    'phone'=> $result->vendor_law_phone,
                    'acc'=> $result->vendor_law_acc,
                    'bank_name'=> $result->vendor_law_bank_name,
                    'korr_acc'=> $result->vendor_law_korr_acc,
                    'bik'=> $result->vendor_law_bik,
                    'owner_car'=> $result->vendor_is_owner_car,
                    'agent_fio'=> $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'fio'=> $vendor_ind_fio,
                    'date'=> $vendor_ind_birthday,
                    'document_serial' => $result->vendor_ind_passport_serial,
                    'document_number' => $result->vendor_ind_passport_number,
                    'document_bywho' => $result->vendor_ind_passport_bywho,
                    'document_date' => $vendor_ind_passport_date,
                    'adress'=> $vendor_ind_adress,
                    'phone'=> $result->vendor_ind_phone,
                    'acc'=> $result->vendor_ind_bank_acc,
                    'bank_name'=> $result->vendor_ind_bank_name,
                    'korr_acc'=> $result->vendor_ind_korr_acc,
                    'bik'=> $result->vendor_ind_bik,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary,
                    'number_of_certificate' => $result->vendor_ind_number_of_certificate,
                    'date_of_certificate' => $vendor_ind_date_of_certificate,
                );
                break;
        }
        //Покупатель
        switch ($result->type_of_taker)
        {
            case 'physical':
                $data_for_req_taker = array
                (
                    'type_of_side' => $result->type_of_taker,
                    'fio' => $buyer_fio,
                    'date' => $buyer_birthday,
                    'document_serial' => $result->buyer_passport_serial,
                    'document_number' => $result->buyer_passport_number,
                    'document_bywho' => $result->buyer_passport_bywho,
                    'document_date' => $buyer_passport_date,
                    'adress' => $buyer_adress,
                    'phone' => $result->buyer_phone,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'name'=> $result->buyer_law_company_name,
                    'inn'=> $result->buyer_law_inn,
                    'ogrn'=> $result->buyer_law_ogrn,
                    'adress'=> $result->buyer_law_adress,
                    'phone'=> $result->buyer_law_phone,
                    'acc'=> $result->buyer_law_acc,
                    'bank_name'=> $result->buyer_law_bank_name,
                    'korr_acc'=> $result->buyer_law_korr_acc,
                    'bik'=> $result->buyer_law_bik,
                    'owner_car'=> $result->buyer_is_owner_car,
                    'agent_fio'=> $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'fio'=> $buyer_ind_fio,
                    'date'=> $buyer_ind_birthday,
                    'document_serial' => $result->buyer_ind_passport_serial,
                    'document_number' => $result->buyer_ind_passport_number,
                    'document_bywho' => $result->buyer_ind_passport_bywho,
                    'document_date' => $buyer_ind_passport_date,
                    'adress'=> $buyer_ind_adress,
                    'phone'=> $result->buyer_ind_phone,
                    'acc'=> $result->buyer_ind_bank_acc,
                    'bank_name'=> $result->buyer_ind_bank_name,
                    'korr_acc'=> $result->buyer_ind_korr_acc,
                    'bik'=> $result->buyer_ind_bik,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary,
                    'number_of_certificate' => $result->buyer_ind_number_of_certificate,
                    'date_of_certificate' => $buyer_ind_date_of_certificate,
                );
                break;
        }
        $firstside_requisites = $this->get_requisites($data_for_req_giver);
        $secondside_requisites = $this->get_requisites($data_for_req_taker);
        //Для подписи
        //Продавец
        $vendor_namedata = array
        (
            'phys_name' => $short_vendor_fio,
            'law_name' => $short_vendor_law_fio,
            'ind_name' => $short_vendor_ind_fio,
            'agent_name' => $short_vendor_agent_fio
        );
        $vendor_name = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_namedata);
        //Покупатель
        $buyer_namedata = array
        (
            'phys_name' => $short_buyer_fio,
            'law_name' => $short_buyer_law_fio,
            'ind_name' => $short_buyer_ind_fio,
            'agent_name' => $short_buyer_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $buyer_namedata);

        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/gift/patterns/gift.docx');

        //Заполнение
        $document->setValue('place_of_contract', $result->place_of_contract);
        $document->setValue('date_of_contract',  $date_of_contract);
        $document->setValue('header_doc', $header_doc);
        $document->setValue('mark', $result->mark);
        $document->setValue('vin', $result->vin);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('car_type', $result->car_type);
        $document->setValue('category', $result->category);
        $document->setValue('date_of_product', $result->date_of_product);
        $document->setValue('engine_model', $result->engine_model);
        $document->setValue('shassi', $result->shassi);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('color_carcass', $result->color_carcass);
        $document->setValue('serial_car', $result->serial_car);
        $document->setValue('number_of_serial_car', $result->number_of_serial_car);
        $document->setValue('bywho_serial_car', $result->bywho_serial_car);
        $document->setValue('date_of_serial_car', $date_of_serial_car);
        $document->setValue('firstside_requisites', $firstside_requisites);
        $document->setValue('secondside_requisites', $secondside_requisites);
        $document->setValue('vendor_name', $vendor_name);
        $document->setValue('buyer_name', $buyer_name);
        //Подпись представителя
        $vendor_agent_sign = $this->get_sign($result->vendor_is_owner_car);
        $buyer_agent_sign = $this->get_sign($result->buyer_is_owner_car);
        $document->setValue('vendor_agent_sign', $vendor_agent_sign);
        $document->setValue('buyer_agent_sign', $buyer_agent_sign);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/gift/'.$id.'gift.docx';//Имя файла и путь к нему
        $document->save($name_of_file,true); // Сохранение документа
        $name_for_server = '/documents/gift/'.$id.'gift.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/gift/'.$id.'gift.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    public function get_gift_act_of_reception($id)
        //Акт приема-передачи договора дарения
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='gift'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=gift.id");
        $query = $this->db->get('gift');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }

        //Подготовка данных
        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/gift/patterns/act_of_reception.docx');
        //Подготовка
        //Фио
        $vendor_fio = $this->format_fio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
//        $spouse_fio = $this->format_fio($result->spouse_surname,$result->spouse_name,$result->spouse_patronymic);
        $vendor_law_fio = $this->format_fio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $vendor_ind_fio = $this->format_fio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $vendor_agent_fio = $this->format_fio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);

        //Короткое фио
        $short_vendor_fio = $this->format_shortfio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $short_buyer_fio = $this->format_shortfio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $short_vendor_law_fio = $this->format_shortfio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $short_buyer_law_fio = $this->format_shortfio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $short_vendor_ind_fio = $this->format_shortfio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $short_buyer_ind_fio = $this->format_shortfio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $short_vendor_agent_fio = $this->format_shortfio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $short_buyer_agent_fio = $this->format_shortfio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Родительское фио
        $vendor_law_fio_parent = $this->format_fio($result->vendor_law_actor_surname_parent,$result->vendor_law_actor_name_parent,$result->vendor_law_actor_patronymic_parent);
        $buyer_law_fio_parent = $this->format_fio($result->buyer_law_actor_surname_parent,$result->buyer_law_actor_name_parent,$result->buyer_law_actor_patronymic_parent);
        $vendor_fio_parent = $this->format_fio($result->vendor_surname_parent, $result->vendor_name_parent, $result->vendor_patronymic_parent);
        $buyer_fio_parent = $this->format_fio($result->buyer_surname_parent,$result->buyer_name_parent,$result->buyer_patronymic_parent);
        $vendor_ind_fio_parent = $this->format_fio($result->vendor_ind_surname_parent,$result->vendor_ind_name_parent,$result->vendor_ind_patronymic_parent);
        $buyer_ind_fio_parent = $this->format_fio($result->buyer_ind_surname_parent,$result->buyer_ind_name_parent,$result->buyer_ind_patronymic_parent);
        $vendor_agent_fio_parent = $this->format_fio($result->for_agent_vendor_surname_parent,$result->for_agent_vendor_name_parent,$result->for_agent_vendor_patronymic_parent);
        $buyer_agent_fio_parent = $this->format_fio($result->for_agent_buyer_surname_parent,$result->for_agent_buyer_name_parent,$result->for_agent_buyer_patronymic_parent);
        //Дата
        $date_of_contract = $this->format_date($result->date_of_contract);
        $date_of_serial_car = $this->format_date($result->date_of_serial_car);
        $vendor_birthday = $this->format_date($result->vendor_birthday, true);
        $buyer_birthday = $this->format_date($result->buyer_birthday, true);
        $vendor_ind_birthday = $this->format_date($result->vendor_ind_birthday, true);
        $buyer_ind_birthday = $this->format_date($result->buyer_ind_birthday, true);
        $vendor_ind_date_of_certificate = $this->format_date($result->vendor_ind_date_of_certificate);
        $buyer_ind_date_of_certificate = $this->format_date($result->buyer_ind_date_of_certificate);
        $vendor_passport_date  = $this->format_date($result->vendor_passport_date);
        $for_agent_vendor_proxy_date = $this->format_date($result->for_agent_vendor_proxy_date);
        $for_agent_buyer_proxy_date = $this->format_date($result->for_agent_buyer_proxy_date);
        $vendor_ind_passport_date = $this->format_date($result->vendor_ind_passport_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        $buyer_law_proxy_date = $this->format_date($result->buyer_law_proxy_date);
        $vendor_law_proxy_date = $this->format_date($result->vendor_law_proxy_date);
        //Новая дата
        $agent_vendor_birthday = $this->format_date($result->agent_vendor_birthday, true);
        $agent_vendor_pass_date = $this->format_date($result->agent_vendor_pass_date);
        $for_agent_proxy_birthday = $this->format_date($result->for_agent_proxy_birthday, true);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);
        //Правки даты
        $vendor_law_date_of_create = $this->format_date($result->vendor_law_date_of_create);
        $buyer_law_date_of_create = $this->format_date($result->buyer_law_date_of_create);
        //Адрес
        $vendor_adress = $this->format_adress($result->vendor_city,$result->vendor_street,$result->vendor_house,$result->vendor_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        //$vendor_law_adress = $this->format_adress($result->vendor_law_city,$result->vendor_law_street,$result->vendor_law_house,$result->vendor_law_flat);
        //$buyer_law_adress = $this->format_adress($result->buyer_law_city,$result->buyer_law_street,$result->buyer_law_house,$result->buyer_law_flat);
        $vendor_ind_adress = $this->format_adress($result->vendor_ind_city,$result->vendor_ind_street,$result->vendor_ind_house,$result->vendor_ind_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
        //Новые адресса
        $agent_vendor_adress = $this->format_adress($result->agent_vendor_city,$result->agent_vendor_street,$result->agent_vendor_house,$result->agent_vendor_flat);
        $for_agent_proxy_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        //Иное
        $data_for_header = array(
            'vendor_fio' =>$vendor_fio,
            'buyer_fio' =>$buyer_fio,
            'vendor_law_company_name' => $result->vendor_law_company_name,
            'vendor_law_actor_position' => $result->vendor_law_actor_position,
//            'vendor_law_fio' =>$vendor_law_fio,
            'vendor_law_fio' =>$vendor_law_fio_parent,
            'vendor_law_document_osn' => $result->vendor_law_document_osn,
            'vendor_law_proxy_number' => $result->vendor_law_proxy_number,
            'vendor_law_proxy_date' => $vendor_law_proxy_date,
            'buyer_law_company_name' => $result->buyer_law_company_name,
            'buyer_law_actor_position' => $result->buyer_law_actor_position,
//            'buyer_law_fio' =>$buyer_law_fio,
            'buyer_law_fio' =>$buyer_law_fio_parent,
            'buyer_law_document_osn' => $result->buyer_law_document_osn,
            'buyer_law_proxy_number' => $result->buyer_law_proxy_number,
            'buyer_law_proxy_date' => $buyer_law_proxy_date,
            'vendor_ind_fio' =>$vendor_ind_fio,
            'vendor_number_of_certificate' => $result->vendor_ind_number_of_certificate,
            'vendor_date_of_certificate' => $vendor_ind_date_of_certificate,
            'buyer_ind_fio' =>$buyer_ind_fio,
            'buyer_number_of_certificate' => $result->buyer_ind_number_of_certificate,
            'buyer_date_of_certificate' => $buyer_ind_date_of_certificate,
            'vendor_is_owner_car' => $result->vendor_is_owner_car,
            'buyer_is_owner_car' => $result->buyer_is_owner_car,
            'vendor_agent_fio' =>$vendor_agent_fio,
            'for_agent_vendor_proxy_number' => $result->for_agent_vendor_proxy_number,
            'for_agent_vendor_proxy_date' => $for_agent_vendor_proxy_date,
            'for_agent_vendor_proxy_notary' => $result->for_agent_vendor_proxy_notary,
            'buyer_agent_fio' =>$buyer_agent_fio,
            'for_agent_buyer_proxy_number' => $result->for_agent_buyer_proxy_number,
            'for_agent_buyer_proxy_date' => $for_agent_buyer_proxy_date,
            'for_agent_buyer_proxy_notary' => $result->for_agent_buyer_proxy_notary,
            //Новые данные для реквизитов
            'agent_vendor_birthday' => $agent_vendor_birthday,
            'agent_vendor_pass_serial' => $result->agent_vendor_pass_serial,
            'agent_vendor_pass_number' => $result->agent_vendor_pass_number,
            'agent_vendor_pass_date' => $agent_vendor_pass_date,
            'agent_vendor_pass_bywho' => $result->agent_vendor_pass_bywho,
            'agent_vendor_adress' => $agent_vendor_adress,
            'for_agent_proxy_birthday' => $for_agent_proxy_birthday,
            'for_agent_proxy_pass_serial' => $result->for_agent_proxy_pass_serial,
            'for_agent_proxy_pass_number' => $result->for_agent_proxy_pass_number,
            'for_agent_proxy_pass_bywho' => $result->for_agent_proxy_pass_bywho,
            'for_agent_proxy_pass_date' => $for_agent_proxy_pass_date,
            'for_agent_proxy_adress' => $for_agent_proxy_adress,
            //Ещё правки
            'vendor_birthday' => $vendor_birthday,
            'buyer_birthday' => $buyer_birthday,
            'vendor_ind_birthday' => $vendor_ind_birthday,
            'buyer_ind_birthday' => $buyer_ind_birthday,
            'vendor_passport_serial' => $result->vendor_passport_serial,
            'vendor_passport_number' => $result->vendor_passport_number,
            'vendor_passport_bywho' => $result->vendor_passport_bywho,
            'vendor_passport_date' => $vendor_passport_date,
            'vendor_adress' => $vendor_adress,
            'vendor_ind_passport_serial' => $result->vendor_ind_passport_serial,
            'vendor_ind_passport_number' => $result->vendor_ind_passport_number,
            'vendor_ind_passport_bywho' => $result->vendor_ind_passport_bywho,
            'vendor_ind_passport_date' => $vendor_ind_passport_date,
            'vendor_ind_adress'=> $vendor_ind_adress,
            'buyer_passport_serial'=> $result->buyer_passport_serial,
            'buyer_passport_number'=> $result->buyer_passport_number,
            'buyer_passport_bywho'=> $result->buyer_passport_bywho,
            'buyer_passport_date'=> $buyer_passport_date,
            'buyer_adress'=> $buyer_adress,
            'buyer_ind_passport_serial' => $result->buyer_ind_passport_serial,
            'buyer_ind_passport_number' => $result->buyer_ind_passport_number,
            'buyer_ind_passport_bywho' => $result->buyer_ind_passport_bywho,
            'buyer_ind_passport_date' => $buyer_ind_passport_date,
            'buyer_ind_adress'=> $buyer_ind_adress,
            'vendor_fio_parent'=> $vendor_fio_parent,
            'buyer_fio_parent'=> $buyer_fio_parent,
            'vendor_ind_fio_parent'=> $vendor_ind_fio_parent,
            'buyer_ind_fio_parent'=> $buyer_ind_fio_parent,
            //
            'vendor_agent_fio_parent'=> $vendor_agent_fio_parent,
            'buyer_agent_fio_parent'=> $buyer_agent_fio_parent,

        );
        $header_doc = $this->set_header_doc($result->type_of_contract ,$result->type_of_giver, $result->type_of_taker, $data_for_header);
        unset($data_for_header);
        //Реквизиты
        //Продавец
        switch ($result->type_of_giver)
        {
            case 'physical':
                $data_for_req_giver = array
                (
                    'type_of_side' => $result->type_of_giver,
                    'fio' => $vendor_fio,
                    'date' => $vendor_birthday,
                    'document_serial' => $result->vendor_passport_serial,
                    'document_number' => $result->vendor_passport_number,
                    'document_bywho' => $result->vendor_passport_bywho,
                    'document_date' => $vendor_passport_date,
                    'adress' => $vendor_adress,
                    'phone' => $result->vendor_phone,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'name'=> $result->vendor_law_company_name,
                    'inn'=> $result->vendor_law_inn,
                    'ogrn'=> $result->vendor_law_ogrn,
                    'adress'=> $result->vendor_law_adress,
                    'phone'=> $result->vendor_law_phone,
                    'acc'=> $result->vendor_law_acc,
                    'bank_name'=> $result->vendor_law_bank_name,
                    'korr_acc'=> $result->vendor_law_korr_acc,
                    'bik'=> $result->vendor_law_bik,
                    'owner_car'=> $result->vendor_is_owner_car,
                    'agent_fio'=> $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'fio'=> $vendor_ind_fio,
                    'date'=> $vendor_ind_birthday,
                    'document_serial' => $result->vendor_ind_passport_serial,
                    'document_number' => $result->vendor_ind_passport_number,
                    'document_bywho' => $result->vendor_ind_passport_bywho,
                    'document_date' => $vendor_ind_passport_date,
                    'adress'=> $vendor_ind_adress,
                    'phone'=> $result->vendor_ind_phone,
                    'acc'=> $result->vendor_ind_bank_acc,
                    'bank_name'=> $result->vendor_ind_bank_name,
                    'korr_acc'=> $result->vendor_ind_korr_acc,
                    'bik'=> $result->vendor_ind_bik,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary,
                    'number_of_certificate' => $result->vendor_ind_number_of_certificate,
                    'date_of_certificate' => $vendor_ind_date_of_certificate,
                );
                break;
        }
        //Покупатель
        switch ($result->type_of_taker)
        {
            case 'physical':
                $data_for_req_taker = array
                (
                    'type_of_side' => $result->type_of_taker,
                    'fio' => $buyer_fio,
                    'date' => $buyer_birthday,
                    'document_serial' => $result->buyer_passport_serial,
                    'document_number' => $result->buyer_passport_number,
                    'document_bywho' => $result->buyer_passport_bywho,
                    'document_date' => $buyer_passport_date,
                    'adress' => $buyer_adress,
                    'phone' => $result->buyer_phone,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'name'=> $result->buyer_law_company_name,
                    'inn'=> $result->buyer_law_inn,
                    'ogrn'=> $result->buyer_law_ogrn,
                    'adress'=> $result->buyer_law_adress,
                    'phone'=> $result->buyer_law_phone,
                    'acc'=> $result->buyer_law_acc,
                    'bank_name'=> $result->buyer_law_bank_name,
                    'korr_acc'=> $result->buyer_law_korr_acc,
                    'bik'=> $result->buyer_law_bik,
                    'owner_car'=> $result->buyer_is_owner_car,
                    'agent_fio'=> $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'fio'=> $buyer_ind_fio,
                    'date'=> $buyer_ind_birthday,
                    'document_serial' => $result->buyer_ind_passport_serial,
                    'document_number' => $result->buyer_ind_passport_number,
                    'document_bywho' => $result->buyer_ind_passport_bywho,
                    'document_date' => $buyer_ind_passport_date,
                    'adress'=> $buyer_ind_adress,
                    'phone'=> $result->buyer_ind_phone,
                    'acc'=> $result->buyer_ind_bank_acc,
                    'bank_name'=> $result->buyer_ind_bank_name,
                    'korr_acc'=> $result->buyer_ind_korr_acc,
                    'bik'=> $result->buyer_ind_bik,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary,
                    'number_of_certificate' => $result->buyer_ind_number_of_certificate,
                    'date_of_certificate' => $buyer_ind_date_of_certificate,
                );
                break;
        }
        $firstside_requisites = $this->get_requisites($data_for_req_giver);
        $secondside_requisites = $this->get_requisites($data_for_req_taker);
        //Для подписи
        //Продавец
        $vendor_namedata = array
        (
            'phys_name' => $short_vendor_fio,
            'law_name' => $short_vendor_law_fio,
            'ind_name' => $short_vendor_ind_fio,
            'agent_name' => $short_vendor_agent_fio
        );
        $vendor_name = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_namedata);
        //Покупатель
        $buyer_namedata = array
        (
            'phys_name' => $short_buyer_fio,
            'law_name' => $short_buyer_law_fio,
            'ind_name' => $short_buyer_ind_fio,
            'agent_name' => $short_buyer_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $buyer_namedata);

        //Заполнение
        $document->setValue('place_of_contract', $result->place_of_contract);
        $document->setValue('date_of_contract', $date_of_contract);
        $document->setValue('header_doc', $header_doc);
        $document->setValue('mark', $result->mark);
        $document->setValue('vin', $result->vin);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('car_type', $result->car_type);
        $document->setValue('date_of_product', $result->date_of_product);
        $document->setValue('category', $result->category);
        $document->setValue('engine_model', $result->engine_model);
        $document->setValue('shassi', $result->shassi);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('color_carcass', $result->color_carcass);
        $document->setValue('serial_car', $result->serial_car);
        $document->setValue('number_of_serial_car', $result->number_of_serial_car);
        $document->setValue('bywho_serial_car', $result->bywho_serial_car);
        $document->setValue('date_of_serial_car', $date_of_serial_car);
        $document->setValue('firstside_requisites', $firstside_requisites);
        $document->setValue('secondside_requisites', $secondside_requisites);
        $document->setValue('buyer_name', $buyer_name);
        $document->setValue('vendor_name', $vendor_name);
        //Подпись представителя
        $vendor_agent_sign = $this->get_sign($result->vendor_is_owner_car);
        $buyer_agent_sign = $this->get_sign($result->buyer_is_owner_car);
        $document->setValue('vendor_agent_sign', $vendor_agent_sign);
        $document->setValue('buyer_agent_sign', $buyer_agent_sign);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/gift/'.$id.'act_of_reception.docx';//Имя файла и путь к нему
        $document->save($name_of_file); // Сохранение документа
        $name_for_server = '/documents/gift/'.$id.'act_of_reception.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/gift/'.$id.'act_of_reception.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;

    }
    //------------------------------------------------------------------------------------------------------------------
    private function get_info_for_gibbd($type_of_giver, $data)
    {
        switch ($type_of_giver)
        {
            case 'physical':
                $data_output['giver_name']= $this->format_fio($data['vendor_surname'],$data['vendor_name'],$data['vendor_patronymic']);
                $data_output['giver_date'] =$data['vendor_birthday'];
                $data_output['giver_documnet']="Серия {$data['vendor_passport_serial']} {$data['vendor_passport_number']} выданый {$data['vendor_passport_bywho']} от {$data['vendor_passport_date']}";
                $data_output['giver_adress']=$this->format_adress($data['vendor_city'],$data['vendor_street'],$data['vendor_house'],$data['vendor_flat']);
                $data_output['giver_phone'] = $data['vendor_phone'];
                break;
            case 'law':
                $data_output['giver_name']= $data['vendor_law_company_name'];
                $data_output['giver_date'] =$data['vendor_law_proxy_date'];
                $data_output['giver_documnet']="";
                $data_output['giver_adress']=$this->format_adress($data['vendor_law_city'],$data['vendor_law_street'],$data['vendor_law_house'],$data['vendor_law_flat']);
                $data_output['giver_phone'] = $data['vendor_law_phone'];
                break;
            case 'individual':
                $data_output['giver_name']= $this->format_fio($data['vendor_ind_surname'], $data['vendor_ind_name'], $data['vendor_ind_patromymic']);
                $data_output['giver_date'] =$data['vendor_ind_birthday'];
                $data_output['giver_documnet']="Серия {$data['vendor_ind_passport_serial']} {$data['vendor_ind_passport_number']} выданый {$data['vendor_ind_passport_bywho']} от {$data['vendor_ind_passport_date']}";
                $data_output['giver_adress']=$this->format_adress($data['vendor_ind_city'],$data['vendor_ind_street'],$data['vendor_ind_house'],$data['vendor_ind_flat']);
                $data_output['giver_phone'] = $data['vendor_ind_phone'];
                break;
        }
        return $data_output;
    }
    //------------------------------------------------------------------------------------------------------------------
    public function get_gift_gibbd($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='gift'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=gift.id");
        $query = $this->db->get('gift');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        //Подготовка
        //Фио
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Адрес
        $buyer_agent_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
//        $buyer_law_adress = $this->format_adress($result->buyer_law_city,$result->buyer_law_street,$result->buyer_law_house,$result->buyer_law_flat);
        //Дата
//        $date_of_product = $this->format_date($result->date_of_product);
        $buyer_date = $this->format_date($result->buyer_birthday);
        $buyer_ind_date = $this->format_date($result->buyer_ind_birthday);
        $buyer_law_proxy_date = $this->format_date($result->buyer_law_proxy_date);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        //Правки даты
        $vendor_law_date_of_create = $this->format_date($result->vendor_law_date_of_create);
        $buyer_law_date_of_create = $this->format_date($result->buyer_law_date_of_create);
        //Паспорта
        $buyer_pass = "Паспорт: серия $result->buyer_passport_serial № $result->buyer_passport_number выдан $result->buyer_passport_bywho от  $buyer_passport_date";
        $buyer_ind_pass = "Паспорт: серия $result->buyer_ind_passport_serial № $result->buyer_ind_passport_number выдан $result->buyer_ind_passport_bywho от  $buyer_ind_passport_date";
        $buyer_agent_pass = "Паспорт: серия $result->for_agent_proxy_pass_serial № $result->for_agent_proxy_pass_number выдан $result->for_agent_proxy_pass_bywho от  $for_agent_proxy_pass_date";
        //Имя заявителя
        $namedata = array
        (
            'phys_name' => $buyer_fio,
            'law_name' => $buyer_law_fio,
            'ind_name' => $buyer_ind_fio,
            'agent_name' => $buyer_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $namedata);

        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/gift/patterns/gibdd.docx');
        //Заполнение
        $document->setValue('gibdd_reg_name', $result->gibdd_reg_name);
        $document->setValue('buyer_name', $buyer_name);
        $document->setValue('mark', $result->mark);
        $document->setValue('date_of_product', $result->date_of_product);
        $document->setValue('vin', $result->vin);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        //Определяем тип заявителя
        switch ($result->type_of_taker)
        {
            case 'physical':
                $giver['name'] = $buyer_fio;
                $giver['date'] = $buyer_date;
                $giver['pass'] = $buyer_pass;
                $giver['adress'] = $buyer_adress;
                $giver['phone'] = $result->buyer_phone;
                break;
            case 'law':
                $giver['name'] = $result->buyer_law_company_name;
                $giver['date'] = $buyer_law_date_of_create ;
                $giver['pass'] = '';
                $giver['adress'] = $result->buyer_law_adress;
                $giver['phone'] = $result->buyer_law_phone;
                break;
            case 'individual':
                $giver['name'] = $buyer_ind_fio;
                $giver['date'] = $buyer_ind_date;
                $giver['pass'] = $buyer_ind_pass;
                $giver['adress'] = $buyer_ind_adress;
                $giver['phone'] = $result->buyer_ind_phone;
                break;
        }
        $document->setValue('giver_name', $giver['name']);
        $document->setValue('giver_date', $giver['date']);
        $document->setValue('giver_pass', $giver['pass']);
        $document->setValue('gibdd_inn', $result->gibdd_inn);
        $document->setValue('giver_adress',  $giver['adress']);
        $document->setValue('giver_phone', $giver['phone']);
        //
        if ($result->statement_form == 'false')
        {
            $document->setValue('buyer_agent_fio', $buyer_agent_fio);
            $document->setValue('buyer_agent_pass', $buyer_agent_pass);
            $document->setValue('buyer_agent_adress', $buyer_agent_adress);
            $document->setValue('buyer_agent_phone', $result->for_agent_proxy_phone);
        }
        else
        {
            $document->setValue('buyer_agent_fio', '');
            $document->setValue('buyer_agent_pass', '');
            $document->setValue('buyer_agent_adress', '');
            $document->setValue('buyer_agent_phone', '');
        }
        $document->setValue('mark', $result->mark);
        $document->setValue('car_type', $result->car_type);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('color_carcass', $result->color_carcass);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('vin', $result->vin);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('shassi', $result->shassi);
        $document->setValue('gibdd_power_engine', $result->gibdd_power_engine);
        $document->setValue('gibdd_eco_class', $result->gibdd_eco_class);
        $document->setValue('gibdd_max_mass', $result->gibdd_max_mass);
        $document->setValue('gibdd_min_mass', $result->gibdd_min_mass);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/gift/'.$id.'gibdd.docx';//Имя файла и путь к нему
        $document->save($name_of_file); // Сохранение документа
        $name_for_server = '/documents/gift/'.$id.'gibdd.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/gift/'.$id.'gibdd.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    private function file_force_download($file)
    {
        if (file_exists($file)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            // заставляем браузер показать окно сохранения файла
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            // читаем файл и отправляем его пользователю
            if ($fd = fopen($file, 'rb')) {
                while (!feof($fd)) {
                    print fread($fd, 1024);
                }
                fclose($fd);
            }
            exec('rm '.$file);
            exit;
        }
    }
    //------------------------------------------------------------------------------------------------------------------
    //договор купли-продажи транспортного средства
    public function get_doc_buy_sale($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='buy_sale'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=buy_sale.id");
        $query = $this->db->get('buy_sale');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        // Подготовка данных для работы с документов
        //Фио
        $vendor_fio = $this->format_fio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $vendor_law_fio = $this->format_fio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $vendor_ind_fio = $this->format_fio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $vendor_agent_fio = $this->format_fio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Родительское фио
        $spouse_parent_fio = $this->format_fio($result->spouse_surname_parent,$result->spouse_name_parent,$result->spouse_patronymic_parent);
        $vendor_law_fio_parent = $this->format_fio($result->vendor_law_actor_surname_parent,$result->vendor_law_actor_name_parent,$result->vendor_law_actor_patronymic_parent);
        $buyer_law_fio_parent = $this->format_fio($result->buyer_law_actor_surname_parent,$result->buyer_law_actor_name_parent,$result->buyer_law_actor_patronymic_parent);
        $vendor_fio_parent = $this->format_fio($result->vendor_surname_parent, $result->vendor_name_parent, $result->vendor_patronymic_parent);
        $buyer_fio_parent = $this->format_fio($result->buyer_surname_parent,$result->buyer_name_parent,$result->buyer_patronymic_parent);
        $vendor_ind_fio_parent = $this->format_fio($result->vendor_ind_surname_parent,$result->vendor_ind_name_parent,$result->vendor_ind_patronymic_parent);
        $buyer_ind_fio_parent = $this->format_fio($result->buyer_ind_surname_parent,$result->buyer_ind_name_parent,$result->buyer_ind_patronymic_parent);

        $vendor_agent_fio_parent = $this->format_fio($result->for_agent_vendor_surname_parent,$result->for_agent_vendor_name_parent,$result->for_agent_vendor_patronymic_parent);
        $buyer_agent_fio_parent = $this->format_fio($result->for_agent_buyer_surname_parent,$result->for_agent_buyer_name_parent,$result->for_agent_buyer_patronymic_parent);

        //Короткое фио
        $short_vendor_fio = $this->format_shortfio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $short_buyer_fio = $this->format_shortfio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $short_vendor_law_fio = $this->format_shortfio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $short_buyer_law_fio = $this->format_shortfio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $short_vendor_ind_fio = $this->format_shortfio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $short_buyer_ind_fio = $this->format_shortfio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $short_vendor_agent_fio = $this->format_shortfio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $short_buyer_agent_fio = $this->format_shortfio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);

        //Адрес
        $vendor_adress = $this->format_adress($result->vendor_city,$result->vendor_street,$result->vendor_house,$result->vendor_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        //$vendor_law_adress = $this->format_adress($result->vendor_law_city,$result->vendor_law_street,$result->vendor_law_house,$result->vendor_law_flat);
        //$buyer_law_adress = $this->format_adress($result->buyer_law_city,$result->buyer_law_street,$result->buyer_law_house,$result->buyer_law_flat);
        $vendor_ind_adress = $this->format_adress($result->vendor_ind_city,$result->vendor_ind_street,$result->vendor_ind_house,$result->vendor_ind_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
        //Новые адресса
        $agent_vendor_adress = $this->format_adress($result->agent_vendor_city,$result->agent_vendor_street,$result->agent_vendor_house,$result->agent_vendor_flat);
        $for_agent_proxy_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        //Дата
        $date_of_contract = $this->format_date($result->date_of_contract);
        $date_of_serial_car = $this->format_date($result->date_of_serial_car);
        $vendor_birthday = $this->format_date($result->vendor_birthday, true);
        $buyer_birthday = $this->format_date($result->buyer_birthday, true);
        $vendor_ind_birthday = $this->format_date($result->vendor_ind_birthday, true);
        $buyer_ind_birthday = $this->format_date($result->buyer_ind_birthday, true);
        $vendor_ind_date_of_certificate = $this->format_date($result->vendor_ind_date_of_certificate);
        $buyer_ind_date_of_certificate = $this->format_date($result->buyer_ind_date_of_certificate);
        $maintenance_date = $this->format_date($result->maintenance_date);
        $vendor_passport_date  = $this->format_date($result->vendor_passport_date);
        $for_agent_vendor_proxy_date = $this->format_date($result->for_agent_vendor_proxy_date);
        $for_agent_buyer_proxy_date = $this->format_date($result->for_agent_buyer_proxy_date);
        $vendor_ind_passport_date = $this->format_date($result->vendor_ind_passport_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        $buyer_law_proxy_date = $this->format_date($result->buyer_law_proxy_date);
        $vendor_law_proxy_date = $this->format_date($result->vendor_law_proxy_date);
        $credit_date = $this->format_date($result->credit_date);
        //Новая дата
        $agent_vendor_birthday = $this->format_date($result->agent_vendor_birthday, true);
        $agent_vendor_pass_date = $this->format_date($result->agent_vendor_pass_date);
        $for_agent_proxy_birthday = $this->format_date($result->for_agent_proxy_birthday, true);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);
        //Правки даты
        $vendor_law_date_of_create = $this->format_date($result->vendor_law_date_of_create);
        $buyer_law_date_of_create = $this->format_date($result->buyer_law_date_of_create);
        //Джсон
        $documents = $this->json_to_string($result->documents);
        $accessories = $this->json_to_string_accessories($result->accessories);
        $additional_devices_array = $this->json_to_string($result->additional_devices_array);
        //Иные
        $marriage = $this->get_marriage_info($result->car_in_marriage, $spouse_parent_fio);
        $price_str = $this->num2str($result->price_car);
        $data_for_header = array(
            'vendor_fio' =>$vendor_fio,
            'buyer_fio' =>$buyer_fio,
            'vendor_law_company_name' => $result->vendor_law_company_name,
            'vendor_law_actor_position' => $result->vendor_law_actor_position,
            'vendor_law_fio' =>$vendor_law_fio_parent,
            'vendor_law_document_osn' => $result->vendor_law_document_osn,
            'vendor_law_proxy_number' => $result->vendor_law_proxy_number,
            'vendor_law_proxy_date' => $vendor_law_proxy_date,
            'buyer_law_company_name' => $result->buyer_law_company_name,
            'buyer_law_actor_position' => $result->buyer_law_actor_position,
            'buyer_law_fio' =>$buyer_law_fio_parent,
            'buyer_law_document_osn' => $result->buyer_law_document_osn,
            'buyer_law_proxy_number' => $result->buyer_law_proxy_number,
            'buyer_law_proxy_date' => $buyer_law_proxy_date,
            'vendor_ind_fio' =>$vendor_ind_fio,
            'vendor_number_of_certificate' => $result->vendor_ind_number_of_certificate,
            'vendor_date_of_certificate' => $vendor_ind_date_of_certificate,
            'buyer_ind_fio' =>$buyer_ind_fio,
            'buyer_number_of_certificate' => $result->buyer_ind_number_of_certificate,
            'buyer_date_of_certificate' => $buyer_ind_date_of_certificate,
            'vendor_is_owner_car' => $result->vendor_is_owner_car,
            'buyer_is_owner_car' => $result->buyer_is_owner_car,
            'vendor_agent_fio' =>$vendor_agent_fio,
            'for_agent_vendor_proxy_number' => $result->for_agent_vendor_proxy_number,
            'for_agent_vendor_proxy_date' => $for_agent_vendor_proxy_date,
            'for_agent_vendor_proxy_notary' => $result->for_agent_vendor_proxy_notary,
            'buyer_agent_fio' =>$buyer_agent_fio,
            'for_agent_buyer_proxy_number' => $result->for_agent_buyer_proxy_number,
            'for_agent_buyer_proxy_date' => $for_agent_buyer_proxy_date,
            'for_agent_buyer_proxy_notary' => $result->for_agent_buyer_proxy_notary,
            //Новые данные для реквизитов
            'agent_vendor_birthday' => $agent_vendor_birthday,
            'agent_vendor_pass_serial' => $result->agent_vendor_pass_serial,
            'agent_vendor_pass_number' => $result->agent_vendor_pass_number,
            'agent_vendor_pass_date' => $agent_vendor_pass_date,
            'agent_vendor_pass_bywho' => $result->agent_vendor_pass_bywho,
            'agent_vendor_adress' => $agent_vendor_adress,
            'for_agent_proxy_birthday' => $for_agent_proxy_birthday,
            'for_agent_proxy_pass_serial' => $result->for_agent_proxy_pass_serial,
            'for_agent_proxy_pass_number' => $result->for_agent_proxy_pass_number,
            'for_agent_proxy_pass_bywho' => $result->for_agent_proxy_pass_bywho,
            'for_agent_proxy_pass_date' => $for_agent_proxy_pass_date,
            'for_agent_proxy_adress' => $for_agent_proxy_adress,
            //Ещё правки
            'vendor_birthday' => $vendor_birthday,
            'buyer_birthday' => $buyer_birthday,
            'vendor_ind_birthday' => $vendor_ind_birthday,
            'buyer_ind_birthday' => $buyer_ind_birthday,
            'vendor_passport_serial' => $result->vendor_passport_serial,
            'vendor_passport_number' => $result->vendor_passport_number,
            'vendor_passport_bywho' => $result->vendor_passport_bywho,
            'vendor_passport_date' => $vendor_passport_date,
            'vendor_adress' => $vendor_adress,
            'vendor_ind_passport_serial' => $result->vendor_ind_passport_serial,
            'vendor_ind_passport_number' => $result->vendor_ind_passport_number,
            'vendor_ind_passport_bywho' => $result->vendor_ind_passport_bywho,
            'vendor_ind_passport_date' => $vendor_ind_passport_date,
            'vendor_ind_adress'=> $vendor_ind_adress,
            'buyer_passport_serial'=> $result->buyer_passport_serial,
            'buyer_passport_number'=> $result->buyer_passport_number,
            'buyer_passport_bywho'=> $result->buyer_passport_bywho,
            'buyer_passport_date'=> $buyer_passport_date,
            'buyer_adress'=> $buyer_adress,
            'buyer_ind_passport_serial' => $result->buyer_ind_passport_serial,
            'buyer_ind_passport_number' => $result->buyer_ind_passport_number,
            'buyer_ind_passport_bywho' => $result->buyer_ind_passport_bywho,
            'buyer_ind_passport_date' => $buyer_ind_passport_date,
            'buyer_ind_adress'=> $buyer_ind_adress,
            'vendor_fio_parent'=> $vendor_fio_parent,
            'buyer_fio_parent'=> $buyer_fio_parent,
            'vendor_ind_fio_parent'=> $vendor_ind_fio_parent,
            'buyer_ind_fio_parent'=> $buyer_ind_fio_parent,
            //
            'vendor_agent_fio_parent'=> $vendor_agent_fio_parent,
            'buyer_agent_fio_parent'=> $buyer_agent_fio_parent,


        );
        $header_doc = $this->set_header_doc($result->type_of_contract ,$result->type_of_giver, $result->type_of_taker, $data_for_header);
        //Реквизиты
        //Продавец
        switch ($result->type_of_giver)
        {
            case 'physical':
                $data_for_req_giver = array
                (
                    'type_of_side' => $result->type_of_giver,
                    'fio' => $vendor_fio,
                    'date' => $vendor_birthday,
                    'document_serial' => $result->vendor_passport_serial,
                    'document_number' => $result->vendor_passport_number,
                    'document_bywho' => $result->vendor_passport_bywho,
                    'document_date' => $vendor_passport_date,
                    'adress' => $vendor_adress,
                    'phone' => $result->vendor_phone,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'name'=> $result->vendor_law_company_name,
                    'inn'=> $result->vendor_law_inn,
                    'ogrn'=> $result->vendor_law_ogrn,
                    'adress'=> $result->vendor_law_adress,
                    'phone'=> $result->vendor_law_phone,
                    'acc'=> $result->vendor_law_acc,
                    'bank_name'=> $result->vendor_law_bank_name,
                    'korr_acc'=> $result->vendor_law_korr_acc,
                    'bik'=> $result->vendor_law_bik,
                    'owner_car'=> $result->vendor_is_owner_car,
                    'agent_fio'=> $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'fio'=> $vendor_ind_fio,
                    'date'=> $vendor_ind_birthday,
                    'document_serial' => $result->vendor_ind_passport_serial,
                    'document_number' => $result->vendor_ind_passport_number,
                    'document_bywho' => $result->vendor_ind_passport_bywho,
                    'document_date' => $vendor_ind_passport_date,
                    'adress'=> $vendor_ind_adress,
                    'phone'=> $result->vendor_ind_phone,
                    'acc'=> $result->vendor_ind_bank_acc,
                    'bank_name'=> $result->vendor_ind_bank_name,
                    'korr_acc'=> $result->vendor_ind_korr_acc,
                    'bik'=> $result->vendor_ind_bik,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary,
                    'number_of_certificate' => $result->vendor_ind_number_of_certificate,
                    'date_of_certificate' => $vendor_ind_date_of_certificate,
                );
                break;
        }
        //Покупатель
        switch ($result->type_of_taker)
        {
            case 'physical':
                $data_for_req_taker = array
                (
                    'type_of_side' => $result->type_of_taker,
                    'fio' => $buyer_fio,
                    'date' => $buyer_birthday,
                    'document_serial' => $result->buyer_passport_serial,
                    'document_number' => $result->buyer_passport_number,
                    'document_bywho' => $result->buyer_passport_bywho,
                    'document_date' => $buyer_passport_date,
                    'adress' => $buyer_adress,
                    'phone' => $result->buyer_phone,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'name'=> $result->buyer_law_company_name,
                    'inn'=> $result->buyer_law_inn,
                    'ogrn'=> $result->buyer_law_ogrn,
                    'adress'=> $result->buyer_law_adress,
                    'phone'=> $result->buyer_law_phone,
                    'acc'=> $result->buyer_law_acc,
                    'bank_name'=> $result->buyer_law_bank_name,
                    'korr_acc'=> $result->buyer_law_korr_acc,
                    'bik'=> $result->buyer_law_bik,
                    'owner_car'=> $result->buyer_is_owner_car,
                    'agent_fio'=> $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'fio'=> $buyer_ind_fio,
                    'date'=> $buyer_ind_birthday,
                    'document_serial' => $result->buyer_ind_passport_serial,
                    'document_number' => $result->buyer_ind_passport_number,
                    'document_bywho' => $result->buyer_ind_passport_bywho,
                    'document_date' => $buyer_ind_passport_date,
                    'adress'=> $buyer_ind_adress,
                    'phone'=> $result->buyer_ind_phone,
                    'acc'=> $result->buyer_ind_bank_acc,
                    'bank_name'=> $result->buyer_ind_bank_name,
                    'korr_acc'=> $result->buyer_ind_korr_acc,
                    'bik'=> $result->buyer_ind_bik,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary,
                    'number_of_certificate' => $result->buyer_ind_number_of_certificate,
                    'date_of_certificate' => $buyer_ind_date_of_certificate,
                );
                break;
        }
        $firstside_requisites = $this->get_requisites($data_for_req_giver);
        $secondside_requisites = $this->get_requisites($data_for_req_taker);
        //Для подписи
        //Продавец
        $vendor_namedata = array
        (
            'phys_name' => $short_vendor_fio,
            'law_name' => $short_vendor_law_fio,
            'ind_name' => $short_vendor_ind_fio,
            'agent_name' => $short_vendor_agent_fio
        );
        $vendor_name = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_namedata);
        //Покупатель
        $buyer_namedata = array
        (
            'phys_name' => $short_buyer_fio,
            'law_name' => $short_buyer_law_fio,
            'ind_name' => $short_buyer_ind_fio,
            'agent_name' => $short_buyer_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $buyer_namedata);
        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/buy_sale_deal.docx');

        //Заполнение
        $document->setValue('place_of_contract', $result->place_of_contract);
        $document->setValue('date_of_contract',  $date_of_contract);
        $document->setValue('header_doc', $header_doc);
        $document->setValue('vendor_name', $vendor_name);
        $document->setValue('buyer_name', $buyer_name);
        $document->setValue('mark', $result->mark);
        $document->setValue('vin', $result->vin);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('car_type', $result->car_type);
        $document->setValue('category', $result->category);
        $document->setValue('date_of_product', $result->date_of_product);
        $document->setValue('engine_model', $result->engine_model);
        $document->setValue('shassi', $result->shassi);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('color_carcass', $result->color_carcass);
        $document->setValue('other_parameters', $result->other_parameters);
        $document->setValue('additional_devices_array', $additional_devices_array);
        $document->setValue('serial_car', $result->serial_car);
        $document->setValue('number_of_serial_car', $result->number_of_serial_car);
        $document->setValue('bywho_serial_car', $result->bywho_serial_car);
        $document->setValue('date_of_serial_car', $date_of_serial_car);
        $document->setValue('car_allstatus', $result->car_allstatus);
        $document->setValue('maintenance_date', $maintenance_date);
        $document->setValue('maintenance_bywho', $result->maintenance_bywho);
        $document->setValue('defects', $result->defects);
        $document->setValue('features', $result->features);
        $document->setValue('price', $result->price_car);
        $document->setValue('price_str', $price_str);
        $document->setValue('currency', $result->currency);

        $document->setValue('payment_date', $result->payment_date);
        if ($result->payment_date == 'в рассрочку по следующему графику')
        {
            $credit = ": аванс в сумме $result->credit (".$this->num2str($result->credit).") $result->credit_currency оплачен покупателем при подписании настоящего договора, оставшуюся часть денег покупатель обязуется оплатить до $credit_date";
        }
        else
        {
            $credit = '';
        }
        $document->setValue('credit', $credit);
        $document->setValue('documents', $documents);
        $document->setValue('accessories', $accessories);
        $document->setValue('marriage_info', $marriage['info']);
        $document->setValue('marriage_number', $marriage['number']);
        $document->setValue('penalty', $result->penalty);
        $document->setValue('firstside_requisites', $firstside_requisites);
        $document->setValue('secondside_requisites', $secondside_requisites);

        //Подпись представителя
        $vendor_agent_sign = $this->get_sign($result->vendor_is_owner_car);
        $buyer_agent_sign = $this->get_sign($result->buyer_is_owner_car);
        $document->setValue('vendor_agent_sign', $vendor_agent_sign);
        $document->setValue('buyer_agent_sign', $buyer_agent_sign);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/'.$id.'buy_sale_deal.docx';//Имя файла и путь к нему
        $document->save($name_of_file,true); // Сохранение документа
        $name_for_server = '/documents/buy_sale/'.$id.'buy_sale_deal.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/buy_sale/'.$id.'buy_sale_deal.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    //акт приема-передачи автомобиля
    public function get_doc_act_of_reception($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='buy_sale'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=buy_sale.id");
        $query = $this->db->get('buy_sale');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        //Подготовка данных
        //Фио
        $vendor_fio = $this->format_fio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $spouse_fio = $this->format_fio($result->spouse_surname,$result->spouse_name,$result->spouse_patronymic);
        $vendor_law_fio = $this->format_fio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $vendor_ind_fio = $this->format_fio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $vendor_agent_fio = $this->format_fio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);

        //Короткое фио
        $short_vendor_fio = $this->format_shortfio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $short_buyer_fio = $this->format_shortfio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $short_vendor_law_fio = $this->format_shortfio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $short_buyer_law_fio = $this->format_shortfio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $short_vendor_ind_fio = $this->format_shortfio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $short_buyer_ind_fio = $this->format_shortfio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $short_vendor_agent_fio = $this->format_shortfio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $short_buyer_agent_fio = $this->format_shortfio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Родительское фио
        $vendor_law_fio_parent = $this->format_fio($result->vendor_law_actor_surname_parent,$result->vendor_law_actor_name_parent,$result->vendor_law_actor_patronymic_parent);
        $buyer_law_fio_parent = $this->format_fio($result->buyer_law_actor_surname_parent,$result->buyer_law_actor_name_parent,$result->buyer_law_actor_patronymic_parent);
        $vendor_fio_parent = $this->format_fio($result->vendor_surname_parent, $result->vendor_name_parent, $result->vendor_patronymic_parent);
        $buyer_fio_parent = $this->format_fio($result->buyer_surname_parent,$result->buyer_name_parent,$result->buyer_patronymic_parent);
        $vendor_ind_fio_parent = $this->format_fio($result->vendor_ind_surname_parent,$result->vendor_ind_name_parent,$result->vendor_ind_patronymic_parent);
        $buyer_ind_fio_parent = $this->format_fio($result->buyer_ind_surname_parent,$result->buyer_ind_name_parent,$result->buyer_ind_patronymic_parent);
        $vendor_agent_fio_parent = $this->format_fio($result->for_agent_vendor_surname_parent,$result->for_agent_vendor_name_parent,$result->for_agent_vendor_patronymic_parent);
        $buyer_agent_fio_parent = $this->format_fio($result->for_agent_buyer_surname_parent,$result->for_agent_buyer_name_parent,$result->for_agent_buyer_patronymic_parent);

        //Адрес
        $vendor_adress = $this->format_adress($result->vendor_city,$result->vendor_street,$result->vendor_house,$result->vendor_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        //$vendor_law_adress = $this->format_adress($result->vendor_law_city,$result->vendor_law_street,$result->vendor_law_house,$result->vendor_law_flat);
        //$buyer_law_adress = $this->format_adress($result->buyer_law_city,$result->buyer_law_street,$result->buyer_law_house,$result->buyer_law_flat);
        $vendor_ind_adress = $this->format_adress($result->vendor_ind_city,$result->vendor_ind_street,$result->vendor_ind_house,$result->vendor_ind_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
        //Новые адресса
        $agent_vendor_adress = $this->format_adress($result->agent_vendor_city,$result->agent_vendor_street,$result->agent_vendor_house,$result->agent_vendor_flat);
        $for_agent_proxy_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        //Дата
        $date_of_contract = $this->format_date($result->date_of_contract);
        $date_of_serial_car = $this->format_date($result->date_of_serial_car);
        $vendor_birthday = $this->format_date($result->vendor_birthday, true);
        $buyer_birthday = $this->format_date($result->buyer_birthday, true);
        $vendor_ind_birthday = $this->format_date($result->vendor_ind_birthday, true);
        $buyer_ind_birthday = $this->format_date($result->buyer_ind_birthday, true);
        $vendor_ind_date_of_certificate = $this->format_date($result->vendor_ind_date_of_certificate);
        $buyer_ind_date_of_certificate = $this->format_date($result->buyer_ind_date_of_certificate);
        $maintenance_date = $this->format_date($result->maintenance_date);
        $vendor_passport_date  = $this->format_date($result->vendor_passport_date);
        $for_agent_vendor_proxy_date = $this->format_date($result->for_agent_vendor_proxy_date);
        $for_agent_buyer_proxy_date = $this->format_date($result->for_agent_buyer_proxy_date);
        $vendor_ind_passport_date = $this->format_date($result->vendor_ind_passport_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        $buyer_law_proxy_date = $this->format_date($result->buyer_law_proxy_date);
        $vendor_law_proxy_date = $this->format_date($result->vendor_law_proxy_date);
        $credit_date = $this->format_date($result->credit_date);
        //Новая дата
        $agent_vendor_birthday = $this->format_date($result->agent_vendor_birthday, true);
        $agent_vendor_pass_date = $this->format_date($result->agent_vendor_pass_date);
        $for_agent_proxy_birthday = $this->format_date($result->for_agent_proxy_birthday, true);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);
        //Правки даты
        $vendor_law_date_of_create = $this->format_date($result->vendor_law_date_of_create);
        $buyer_law_date_of_create = $this->format_date($result->buyer_law_date_of_create);
        //Джсон
        $documents = $this->json_to_string($result->documents);
        $accessories = $this->json_to_string($result->accessories);
        $additional_devices_array = $this->json_to_string($result->additional_devices_array);
        //Иные
        $marriage = $this->get_marriage_info($result->car_in_marriage, $spouse_fio);
        $price_str = $this->num2str($result->price_car);
        $data_for_header = array(
            'vendor_fio' =>$vendor_fio,
            'buyer_fio' =>$buyer_fio,
            'vendor_law_company_name' => $result->vendor_law_company_name,
            'vendor_law_actor_position' => $result->vendor_law_actor_position,
            'vendor_law_fio' =>$vendor_law_fio_parent,
            'vendor_law_document_osn' => $result->vendor_law_document_osn,
            'vendor_law_proxy_number' => $result->vendor_law_proxy_number,
            'vendor_law_proxy_date' => $vendor_law_proxy_date,
            'buyer_law_company_name' => $result->buyer_law_company_name,
            'buyer_law_actor_position' => $result->buyer_law_actor_position,
            'buyer_law_fio' =>$buyer_law_fio_parent,
            'buyer_law_document_osn' => $result->buyer_law_document_osn,
            'buyer_law_proxy_number' => $result->buyer_law_proxy_number,
            'buyer_law_proxy_date' => $buyer_law_proxy_date,
            'vendor_ind_fio' =>$vendor_ind_fio,
            'vendor_number_of_certificate' => $result->vendor_ind_number_of_certificate,
            'vendor_date_of_certificate' => $vendor_ind_date_of_certificate,
            'buyer_ind_fio' =>$buyer_ind_fio,
            'buyer_number_of_certificate' => $result->buyer_ind_number_of_certificate,
            'buyer_date_of_certificate' => $buyer_ind_date_of_certificate,
            'vendor_is_owner_car' => $result->vendor_is_owner_car,
            'buyer_is_owner_car' => $result->buyer_is_owner_car,
            'vendor_agent_fio' =>$vendor_agent_fio,
            'for_agent_vendor_proxy_number' => $result->for_agent_vendor_proxy_number,
            'for_agent_vendor_proxy_date' => $for_agent_vendor_proxy_date,
            'for_agent_vendor_proxy_notary' => $result->for_agent_vendor_proxy_notary,
            'buyer_agent_fio' =>$buyer_agent_fio,
            'for_agent_buyer_proxy_number' => $result->for_agent_buyer_proxy_number,
            'for_agent_buyer_proxy_date' => $for_agent_buyer_proxy_date,
            'for_agent_buyer_proxy_notary' => $result->for_agent_buyer_proxy_notary,
            //Новые данные для реквизитов
            'agent_vendor_birthday' => $agent_vendor_birthday,
            'agent_vendor_pass_serial' => $result->agent_vendor_pass_serial,
            'agent_vendor_pass_number' => $result->agent_vendor_pass_number,
            'agent_vendor_pass_date' => $agent_vendor_pass_date,
            'agent_vendor_pass_bywho' => $result->agent_vendor_pass_bywho,
            'agent_vendor_adress' => $agent_vendor_adress,
            'for_agent_proxy_birthday' => $for_agent_proxy_birthday,
            'for_agent_proxy_pass_serial' => $result->for_agent_proxy_pass_serial,
            'for_agent_proxy_pass_number' => $result->for_agent_proxy_pass_number,
            'for_agent_proxy_pass_bywho' => $result->for_agent_proxy_pass_bywho,
            'for_agent_proxy_pass_date' => $for_agent_proxy_pass_date,
            'for_agent_proxy_adress' => $for_agent_proxy_adress,
            //Ещё правки
            'vendor_birthday' => $vendor_birthday,
            'buyer_birthday' => $buyer_birthday,
            'vendor_ind_birthday' => $vendor_ind_birthday,
            'buyer_ind_birthday' => $buyer_ind_birthday,
            'vendor_passport_serial' => $result->vendor_passport_serial,
            'vendor_passport_number' => $result->vendor_passport_number,
            'vendor_passport_bywho' => $result->vendor_passport_bywho,
            'vendor_passport_date' => $vendor_passport_date,
            'vendor_adress' => $vendor_adress,
            'vendor_ind_passport_serial' => $result->vendor_ind_passport_serial,
            'vendor_ind_passport_number' => $result->vendor_ind_passport_number,
            'vendor_ind_passport_bywho' => $result->vendor_ind_passport_bywho,
            'vendor_ind_passport_date' => $vendor_ind_passport_date,
            'vendor_ind_adress'=> $vendor_ind_adress,
            'buyer_passport_serial'=> $result->buyer_passport_serial,
            'buyer_passport_number'=> $result->buyer_passport_number,
            'buyer_passport_bywho'=> $result->buyer_passport_bywho,
            'buyer_passport_date'=> $buyer_passport_date,
            'buyer_adress'=> $buyer_adress,
            'buyer_ind_passport_serial' => $result->buyer_ind_passport_serial,
            'buyer_ind_passport_number' => $result->buyer_ind_passport_number,
            'buyer_ind_passport_bywho' => $result->buyer_ind_passport_bywho,
            'buyer_ind_passport_date' => $buyer_ind_passport_date,
            'buyer_ind_adress'=> $buyer_ind_adress,
            'vendor_fio_parent'=> $vendor_fio_parent,
            'buyer_fio_parent'=> $buyer_fio_parent,
            'vendor_ind_fio_parent'=> $vendor_ind_fio_parent,
            'buyer_ind_fio_parent'=> $buyer_ind_fio_parent,
            //
            'vendor_agent_fio_parent'=> $vendor_agent_fio_parent,
            'buyer_agent_fio_parent'=> $buyer_agent_fio_parent,
        );
        $header_doc = $this->set_header_doc($result->type_of_contract ,$result->type_of_giver, $result->type_of_taker, $data_for_header);
        //Реквизиты
        //Продавец
        switch ($result->type_of_giver)
        {
            case 'physical':
                $data_for_req_giver = array
                (
                    'type_of_side' => $result->type_of_giver,
                    'fio' => $vendor_fio,
                    'date' => $vendor_birthday,
                    'document_serial' => $result->vendor_passport_serial,
                    'document_number' => $result->vendor_passport_number,
                    'document_bywho' => $result->vendor_passport_bywho,
                    'document_date' => $vendor_passport_date,
                    'adress' => $vendor_adress,
                    'phone' => $result->vendor_phone,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'name'=> $result->vendor_law_company_name,
                    'inn'=> $result->vendor_law_inn,
                    'ogrn'=> $result->vendor_law_ogrn,
                    'adress'=> $result->vendor_law_adress,
                    'phone'=> $result->vendor_law_phone,
                    'acc'=> $result->vendor_law_acc,
                    'bank_name'=> $result->vendor_law_bank_name,
                    'korr_acc'=> $result->vendor_law_korr_acc,
                    'bik'=> $result->vendor_law_bik,
                    'owner_car'=> $result->vendor_is_owner_car,
                    'agent_fio'=> $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_giver = array(
                    'type_of_side' => $result->type_of_giver,
                    'fio'=> $vendor_ind_fio,
                    'date'=> $vendor_ind_birthday,
                    'document_serial' => $result->vendor_ind_passport_serial,
                    'document_number' => $result->vendor_ind_passport_number,
                    'document_bywho' => $result->vendor_ind_passport_bywho,
                    'document_date' => $vendor_ind_passport_date,
                    'adress'=> $vendor_ind_adress,
                    'phone'=> $result->vendor_ind_phone,
                    'acc'=> $result->vendor_ind_bank_acc,
                    'bank_name'=> $result->vendor_ind_bank_name,
                    'korr_acc'=> $result->vendor_ind_korr_acc,
                    'bik'=> $result->vendor_ind_bik,
                    'owner_car' => $result->vendor_is_owner_car,
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $result->for_agent_vendor_proxy_number,
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_vendor_proxy_notary,
                    'number_of_certificate' => $result->vendor_ind_number_of_certificate,
                    'date_of_certificate' => $vendor_ind_date_of_certificate,
                );
                break;
        }
        //Покупатель
        switch ($result->type_of_taker)
        {
            case 'physical':
                $data_for_req_taker = array
                (
                    'type_of_side' => $result->type_of_taker,
                    'fio' => $buyer_fio,
                    'date' => $buyer_birthday,
                    'document_serial' => $result->buyer_passport_serial,
                    'document_number' => $result->buyer_passport_number,
                    'document_bywho' => $result->buyer_passport_bywho,
                    'document_date' => $buyer_passport_date,
                    'adress' => $buyer_adress,
                    'phone' => $result->buyer_phone,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'law':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'name'=> $result->buyer_law_company_name,
                    'inn'=> $result->buyer_law_inn,
                    'ogrn'=> $result->buyer_law_ogrn,
                    'adress'=> $result->buyer_law_adress,
                    'phone'=> $result->buyer_law_phone,
                    'acc'=> $result->buyer_law_acc,
                    'bank_name'=> $result->buyer_law_bank_name,
                    'korr_acc'=> $result->buyer_law_korr_acc,
                    'bik'=> $result->buyer_law_bik,
                    'owner_car'=> $result->buyer_is_owner_car,
                    'agent_fio'=> $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary
                );
                break;
            case 'individual':
                $data_for_req_taker = array(
                    'type_of_side' => $result->type_of_taker,
                    'fio'=> $buyer_ind_fio,
                    'date'=> $buyer_ind_birthday,
                    'document_serial' => $result->buyer_ind_passport_serial,
                    'document_number' => $result->buyer_ind_passport_number,
                    'document_bywho' => $result->buyer_ind_passport_bywho,
                    'document_date' => $buyer_ind_passport_date,
                    'adress'=> $buyer_ind_adress,
                    'phone'=> $result->buyer_ind_phone,
                    'acc'=> $result->buyer_ind_bank_acc,
                    'bank_name'=> $result->buyer_ind_bank_name,
                    'korr_acc'=> $result->buyer_ind_korr_acc,
                    'bik'=> $result->buyer_ind_bik,
                    'owner_car' => $result->buyer_is_owner_car,
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $result->for_agent_buyer_proxy_number,
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $result->for_agent_buyer_proxy_notary,
                    'number_of_certificate' => $result->buyer_ind_number_of_certificate,
                    'date_of_certificate' => $buyer_ind_date_of_certificate,
                );
                break;
        }
        $firstside_requisites = $this->get_requisites($data_for_req_giver);
        $secondside_requisites = $this->get_requisites($data_for_req_taker);
        //Для подписи
        //Продавец
        $vendor_namedata = array
        (
            'phys_name' => $short_vendor_fio,
            'law_name' => $short_vendor_law_fio,
            'ind_name' => $short_vendor_ind_fio,
            'agent_name' => $short_vendor_agent_fio
        );
        $vendor_name = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_namedata);
        //Покупатель
        $buyer_namedata = array
        (
            'phys_name' => $short_buyer_fio,
            'law_name' => $short_buyer_law_fio,
            'ind_name' => $short_buyer_ind_fio,
            'agent_name' => $short_buyer_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $buyer_namedata);

        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/act_of_reception.docx');

        //Заполнение
        $document->setValue('place_of_contract', $result->place_of_contract);
        $document->setValue('date_of_contract', $date_of_contract);
        $document->setValue('header_doc', $header_doc);
        $document->setValue('vendor_name', $vendor_name);
        $document->setValue('buyer_name', $buyer_name);
        $document->setValue('mark', $result->mark);
        $document->setValue('vin', $result->vin);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('car_type', $result->car_type);
        $document->setValue('category', $result->category);
        $document->setValue('date_of_product', $result->date_of_product);
        $document->setValue('engine_model', $result->engine_model);
        $document->setValue('shassi', $result->shassi);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('color_carcass', $result->color_carcass);
        $document->setValue('other_parameters', $result->other_parameters);
        $document->setValue('serial_car', $result->serial_car);
        $document->setValue('number_of_serial_car', $result->number_of_serial_car);
        $document->setValue('bywho_serial_car', $result->bywho_serial_car);
        $document->setValue('date_of_serial_car', $date_of_serial_car);
        $document->setValue('additional_devices_array', $additional_devices_array);
        $document->setValue('oil_in_car', $result->oil_in_car);
        $document->setValue('defects', $result->defects);
        $document->setValue('features', $result->features);
        $document->setValue('firstside_requisites', $firstside_requisites);
        $document->setValue('secondside_requisites', $secondside_requisites);
        //Подпись представителя
        $vendor_agent_sign = $this->get_sign($result->vendor_is_owner_car);
        $buyer_agent_sign = $this->get_sign($result->buyer_is_owner_car);
        $document->setValue('vendor_agent_sign', $vendor_agent_sign);
        $document->setValue('buyer_agent_sign', $buyer_agent_sign);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/'.$id.'act_of_reception.docx';//Имя файла и путь к нему
        $document->save($name_of_file); // Сохранение документа
        $name_for_server = '/documents/buy_sale/'.$id.'act_of_reception.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/buy_sale/'.$id.'act_of_reception.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    //расписка в получении денежных средств
    public function get_doc_receipt_of_money($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='buy_sale'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=buy_sale.id");
        $query = $this->db->get('buy_sale');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        //Подготовка данных

        //ФИО
        $vendor_fio = $this->format_fio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $buyer_fio = $this->format_fio($result->buyer_surname_parent,$result->buyer_name_parent,$result->buyer_patronymic_parent);
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $vendor_law_fio = $this->format_fio($result->vendor_law_actor_surname,$result->vendor_law_actor_name,$result->vendor_law_actor_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname_parent,$result->buyer_law_actor_name_parent,$result->buyer_law_actor_patronymic_parent);
        $buyer_law_fio = $this->format_fio($result->buyer_law_surname,$result->buyer_law_name,$result->buyer_law_patronymic);
        $vendor_ind_fio = $this->format_fio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname_parent,$result->buyer_ind_name_parent,$result->buyer_ind_patronymic_parent);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $vendor_agent_fio = $this->format_fio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname_parent,$result->for_agent_buyer_name_parent,$result->for_agent_buyer_patronymic_parent);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Родительские ФИО
        $vendor_fio_parent = $this->format_fio($result->vendor_surname_parent, $result->vendor_name_parent, $result->vendor_patronymic_parent);
        $buyer_fio_parent = $this->format_fio($result->buyer_surname_parent,$result->buyer_name_parent,$result->buyer_patronymic_parent);
        $buyer_law_fio_parent = $this->format_fio($result->buyer_law_actor_surname_parent,$result->buyer_law_actor_name_parent,$result->buyer_law_actor_patronymic_parent);
        $vendor_ind_fio_parent = $this->format_fio($result->vendor_ind_surname_parent,$result->vendor_ind_name_parent,$result->vendor_ind_patronymic_parent);
        $buyer_ind_fio_parent = $this->format_fio($result->buyer_ind_surname_parent,$result->buyer_ind_name_parent,$result->buyer_ind_patronymic_parent);
        $vendor_agent_fio_parent = $this->format_fio($result->for_agent_vendor_surname_parent,$result->for_agent_vendor_name_parent,$result->for_agent_vendor_patronymic_parent);
        $buyer_agent_fio_parent = $this->format_fio($result->for_agent_buyer_surname_parent_parent,$result->for_agent_buyer_name_parent_parent,$result->for_agent_buyer_patronymic_parent_parent);
        $buyer_agent_fio_parent = $this->format_fio($result->for_agent_buyer_surname_parent,$result->for_agent_buyer_name_parent,$result->for_agent_buyer_patronymic_parent);
        $vendor_law_fio_parent = $this->format_fio($result->vendor_law_actor_surname_parent,$result->vendor_law_actor_name_parent,$result->vendor_law_actor_patronymic_parent);
        //Адрес
        $vendor_adress = $this->format_adress($result->vendor_city,$result->vendor_street,$result->vendor_house,$result->vendor_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        $vendor_ind_adress = $this->format_adress($result->vendor_ind_city,$result->vendor_ind_street,$result->vendor_ind_house,$result->vendor_ind_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
        $agent_vendor_adress = $this->format_adress($result->agent_vendor_city,$result->agent_vendor_street,$result->agent_vendor_house,$result->agent_vendor_flat);
        $for_agent_proxy_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        //Дата
        $date_of_contract = $this->format_date($result->date_of_contract);
        $vendor_passport_date  = $this->format_date($result->vendor_passport_date);
        $vendor_ind_passport_date = $this->format_date($result->vendor_ind_passport_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        $agent_vendor_pass_date = $this->format_date($result->agent_vendor_pass_date);
        $agent_vendor_birthday = $this->format_date($result->agent_vendor_birthday, true);
        $for_agent_proxy_birthday = $this->format_date($result->for_agent_proxy_birthday, true);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);


//        $vendor_date_of_certificate = $result->vendor_date_of_certificate;
//        $buyer_date_of_certificate = $result->buyer_date_of_certificate;
//        $for_agent_vendor_proxy_date = $result->for_agent_vendor_proxy_date;
//        $for_agent_buyer_proxy_date = $result->for_agent_buyer_proxy_date;
//        $buyer_law_proxy_date = $result->buyer_law_proxy_date;
//        $vendor_law_proxy_date = $result->vendor_law_proxy_date;
        //Иное
        $price_str = $this->num2str($result->price_car);
        //Продавец
        switch ($result->type_of_giver)
        {
            case 'physical':
                if ($result->vendor_is_owner_car == 'own_car')
                    $vendor_data = "Я, $vendor_fio, паспорт серия $result->vendor_passport_serial № $result->vendor_passport_number, выдан $result->vendor_passport_bywho дата выдачи $vendor_passport_date, зарегистрированный(ая) по адресу: $vendor_adress (далее - Продавец),";
                elseif ($result->vendor_is_owner_car == 'not_own_car')
                    $vendor_data = "Я, $vendor_agent_fio, $agent_vendor_birthday рождения, паспорт серия $result->agent_vendor_pass_serial № $result->agent_vendor_pass_number, выдан $result->agent_vendor_pass_bywho дата выдачи $agent_vendor_pass_date, зарегистрированный(ая) по адресу: $agent_vendor_adress действующий на основании доверенности от имени $vendor_fio_parent (далее - Продавец),";
                break;
            case 'law':
                if ($result->vendor_is_owner_car == 'own_car')
                    $vendor_data = "Я, $vendor_law_fio, в лице $result->vendor_law_actor_position компании $result->vendor_law_company_name, ИНН $result->vendor_law_inn , ОГРН $result->vendor_law_ogrn зарегистрированной по адресу: $result->vendor_law_adress (далее - Продавец),";
                elseif ($result->vendor_is_owner_car == 'not_own_car')
                    $vendor_data = "Я, $vendor_agent_fio, $agent_vendor_birthday рождения, паспорт серия $result->agent_vendor_pass_serial, № $result->agent_vendor_pass_number, выдан $result->agent_vendor_pass_bywho дата выдачи $agent_vendor_pass_date, зарегистрированный(ая) по адресу: $agent_vendor_adress действующий на основании доверенности от имени $vendor_law_fio_parent (далее - Продавец),";
                break;
            case 'individual':
                if ($result->vendor_is_owner_car == 'own_car')
                    $vendor_data = "Я, $vendor_ind_fio, паспорт серия $result->vendor_ind_passport_serial № $result->vendor_ind_passport_number, выдан $result->vendor_ind_passport_bywho дата выдачи $vendor_ind_passport_date, зарегистрированный(ая) по адресу: $vendor_ind_adress (далее - Продавец),";
                elseif ($result->vendor_is_owner_car == 'not_own_car')
                    $vendor_data = "Я, $vendor_agent_fio, $agent_vendor_birthday рождения, паспорт серия $result->agent_vendor_pass_serial, № $result->agent_vendor_pass_number, выдан $result->agent_vendor_pass_bywho дата выдачи $agent_vendor_pass_date, зарегистрированный(ая) по адресу: $agent_vendor_adress действующий на основании доверенности от имени $vendor_ind_fio_parent (далее - Продавец),";
                break;
        }
        //Покупатель
        switch ($result->type_of_taker)
        {
            case 'physical':
                if ($result->buyer_is_owner_car == 'own_car')
                    $buyer_data = " получил от $buyer_fio_parent паспорт серия $result->buyer_passport_serial, № $result->buyer_passport_number, выдан $result->buyer_passport_bywho дата выдачи $buyer_passport_date, зарегистрированный(ая) по адресу: $buyer_adress (далее - Покупатель)";
                elseif ($result->buyer_is_owner_car == 'not_own_car')
                    $buyer_data = " получил от $buyer_agent_fio_parent $for_agent_proxy_birthday рождения, паспорт серия $result->for_agent_proxy_pass_serial, № $result->for_agent_proxy_pass_number, выдан $result->for_agent_proxy_pass_bywho дата выдачи $for_agent_proxy_pass_date, зарегистрированный(ая) по адресу: $for_agent_proxy_adress действующий на основании доверенности от имени $buyer_fio_parent(далее - Покупатель)";
                break;
            case 'law':
                if ($result->buyer_is_owner_car == 'own_car')
                    $buyer_data = " получил от $buyer_law_fio_parent в лице $result->buyer_law_actor_position компании $result->buyer_law_company_name, ИНН $result->buyer_law_inn , ОГРН $result->buyer_law_ogrn зарегистрированной по адресу: $result->buyer_law_adress (далее - Покупатель)";
                elseif ($result->buyer_is_owner_car == 'not_own_car')
                    $buyer_data = " получил от $buyer_agent_fio_parent $for_agent_proxy_birthday рождения, паспорт серия $result->for_agent_proxy_pass_serial, № $result->for_agent_proxy_pass_number, выдан $result->for_agent_proxy_pass_bywho дата выдачи $for_agent_proxy_pass_date, зарегистрированный(ая) по адресу: $for_agent_proxy_adress действующий на основании доверенности от имени $buyer_law_fio_parent(далее - Покупатель)";
                break;
            case 'individual':
                if ($result->buyer_is_owner_car == 'own_car')
                    $buyer_data = " получил от $buyer_ind_fio_parent паспорт серия $result->buyer_ind_passport_serial, № $result->buyer_ind_passport_number, выдан $result->buyer_ind_passport_bywho дата выдачи $buyer_ind_passport_date, зарегистрированный(ая) по адресу: $buyer_ind_adress (далее - Покупатель)";
                elseif ($result->buyer_is_owner_car == 'not_own_car')
                    $buyer_data = " получил от $buyer_agent_fio_parent $for_agent_proxy_birthday рождения, паспорт серия $result->for_agent_proxy_pass_serial, № $result->for_agent_proxy_pass_number, выдан $result->for_agent_proxy_pass_bywho дата выдачи $for_agent_proxy_pass_date, зарегистрированный(ая) по адресу: $for_agent_proxy_adress действующий на основании доверенности от имени $buyer_ind_fio_parent(далее - Покупатель)";
                break;
        }
        //Для подписи
        $namedata = array
        (
            'phys_name' => $vendor_fio,
            'law_name' => $vendor_law_fio,
            'ind_name' => $vendor_ind_fio,
            'agent_name' => $vendor_agent_fio
        );
        $vendor_name = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $namedata);
        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/receipt_of_money.docx');
        //Заполнение
        $document->setValue('place_of_contract', $result->place_of_contract);
        $document->setValue('date_of_contract', $date_of_contract);
        $document->setValue('vendor_data', $vendor_data);
        $document->setValue('buyer_data', $buyer_data);
        if ($result->payment_date == 'в рассрочку по следующему графику')
        {
            $price = $result->credit ;
            $price_str = $this->num2str($result->credit);
            $currency = $result->credit_currency ;
        }

        else
        {
            $price = $result->price_car;
            $price_str = $price_str;
            $currency = $result->currency;
        }
        $document->setValue('price', $price);
        $document->setValue('price_str', $price_str);
        $document->setValue('currency', $currency);
        $document->setValue('vendor_name', $vendor_name);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/'.$id.'receipt_of_money.docx';//Имя файла и путь к нему
        $document->save($name_of_file); // Сохранение документа
        $name_for_server = '/documents/buy_sale/'.$id.'receipt_of_money.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/buy_sale/'.$id.'receipt_of_money.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    //заявление в ГИБДД для смены собственника
    public function get_doc_statement_gibdd($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='buy_sale'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=buy_sale.id");
        $query = $this->db->get('buy_sale');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        //Подготовка
        //Фио
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Адрес
        $buyer_agent_adress = $this->format_adress($result->for_agent_proxy_city,$result->for_agent_proxy_street,$result->for_agent_proxy_house,$result->for_agent_proxy_flat);
        $buyer_adress = $this->format_adress($result->buyer_city,$result->buyer_street,$result->buyer_house,$result->buyer_flat);
        $buyer_ind_adress = $this->format_adress($result->buyer_ind_city,$result->buyer_ind_street,$result->buyer_ind_house,$result->buyer_ind_flat);
        //Дата
//        $date_of_product = $this->format_date($result->date_of_product);
        $buyer_date = $this->format_date($result->buyer_birthday);
        $buyer_ind_date = $this->format_date($result->buyer_ind_birthday);
        $buyer_law_proxy_date = $this->format_date($result->buyer_law_proxy_date);
        $for_agent_proxy_pass_date = $this->format_date($result->for_agent_proxy_pass_date);
        $buyer_passport_date = $this->format_date($result->buyer_passport_date);
        $buyer_ind_passport_date = $this->format_date($result->buyer_ind_passport_date);
        //новые данные юр.лица
        $vendor_law_date_of_create = $this->format_date($result->vendor_law_date_of_create);
        $buyer_law_date_of_create = $this->format_date($result->buyer_law_date_of_create);

        //Паспорта
        $buyer_pass = "Паспорт: серия $result->buyer_passport_serial № $result->buyer_passport_number выдан $result->buyer_passport_bywho от $buyer_passport_date";
        $buyer_ind_pass = "Паспорт: серия $result->buyer_ind_passport_serial № $result->buyer_ind_passport_number выдан $result->buyer_ind_passport_bywho от $buyer_ind_passport_date";
        $buyer_agent_pass = "Паспорт: серия $result->for_agent_proxy_pass_serial № $result->for_agent_proxy_pass_number выдан $result->for_agent_proxy_pass_bywho от $for_agent_proxy_pass_date";
        //Имя заявителя
        $namedata = array
        (
            'phys_name' => $buyer_fio,
            'law_name' => $buyer_law_fio,
            'ind_name' => $buyer_ind_fio,
            'agent_name' => $buyer_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $namedata);

        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/gibdd.docx');
        //Заполнение
        $document->setValue('gibdd_reg_name', $result->gibdd_reg_name);
        $document->setValue('buyer_name', $buyer_name);
        $document->setValue('mark', $result->mark);
        $document->setValue('date_of_product', $result->date_of_product);
        $document->setValue('vin', $result->vin);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        //Определяем тип заявителя
        switch ($result->type_of_taker)
        {
            case 'physical':
                $giver['name'] = $buyer_fio;
                $giver['date'] = $buyer_date;
                $giver['pass'] = $buyer_pass;
                $giver['adress'] = $buyer_adress;
                $giver['phone'] = $result->buyer_phone;
                break;
            case 'law':
                $giver['name'] = $result->buyer_law_company_name;
                $giver['date'] = $buyer_law_date_of_create ;
                $giver['pass'] = '';
                $giver['adress'] = $result->buyer_law_adress;
                $giver['phone'] = $result->buyer_law_phone;
                break;
            case 'individual':
                $giver['name'] = $buyer_ind_fio;
                $giver['date'] = $buyer_ind_date;
                $giver['pass'] = $buyer_ind_pass;
                $giver['adress'] = $buyer_ind_adress;
                $giver['phone'] = $result->buyer_ind_phone;
                break;
        }
        $document->setValue('giver_name', $giver['name']);
        $document->setValue('giver_date', $giver['date']);
        $document->setValue('giver_pass', $giver['pass']);
        $document->setValue('gibdd_inn', $result->gibdd_inn);
        $document->setValue('giver_adress',  $giver['adress']);
        $document->setValue('giver_phone', $giver['phone']);
        //
        if ($result->statement_form == 'false')
        {
            $document->setValue('buyer_agent_fio', $buyer_agent_fio);
            $document->setValue('buyer_agent_pass', $buyer_agent_pass);
            $document->setValue('buyer_agent_adress', $buyer_agent_adress);
            $document->setValue('buyer_agent_phone', $result->for_agent_proxy_phone);
        }
        else
        {
            $document->setValue('buyer_agent_fio', '');
            $document->setValue('buyer_agent_pass', '');
            $document->setValue('buyer_agent_adress', '');
            $document->setValue('buyer_agent_phone', '');
        }
        $document->setValue('mark', $result->mark);
        $document->setValue('car_type', $result->car_type);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('color_carcass', $result->color_carcass);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('vin', $result->vin);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('shassi', $result->shassi);
        $document->setValue('gibdd_power_engine', $result->gibdd_power_engine);
        $document->setValue('gibdd_eco_class', $result->gibdd_eco_class);
        $document->setValue('gibdd_max_mass', $result->gibdd_max_mass);
        $document->setValue('gibdd_min_mass', $result->gibdd_min_mass);
        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/'.$id.'gibdd.docx';//Имя файла и путь к нему
        $document->save($name_of_file); // Сохранение документа
        $name_for_server = '/documents/buy_sale/'.$id.'gibdd.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/buy_sale/'.$id.'gibdd.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    //заявление продавца о согласии супруга
    public function get_doc_statement_vendor_marriage($id)
    {
        //Работа с базой
        $this->db->select();
        $id_user = $this->data['user_id'];
        $where = "documents.user_id = '$id_user' AND documents.id = '$id ' AND documents.table='buy_sale'";
        $this->db->where($where);
        $this->db->join("documents","documents.doc_id=buy_sale.id");
        $query = $this->db->get('buy_sale');
        $result = $query->row();
        if(empty($result)){
            return false;
        }
        //Если поле пустое - вставляем отсутсвует
        foreach ($result as $key => $value)
        {
            if (empty($value) == true)
            {
                $result->$key = 'не указано';
            }
        }
        //Подготовка
       //Фио
        $vendor_fio = $this->format_fio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $buyer_fio = $this->format_fio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
//        $spouse_fio = $this->format_fio($result->spouse_surname,$result->spouse_name,$result->spouse_patronymic);
//        $vendor_law_fio = $this->format_fio($result->vendor_law_surname,$result->vendor_law_name,$result->vendor_law_patronymic);
        $buyer_law_fio = $this->format_fio($result->buyer_law_actor_surname,$result->buyer_law_actor_name,$result->buyer_law_actor_patronymic);
        $vendor_ind_fio = $this->format_fio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $buyer_ind_fio = $this->format_fio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $vendor_agent_fio = $this->format_fio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $buyer_agent_fio = $this->format_fio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        //Короткое фио
        $short_vendor_fio = $this->format_shortfio($result->vendor_surname, $result->vendor_name, $result->vendor_patronymic);
        $short_spouse_fio = $this->format_shortfio($result->spouse_surname,$result->spouse_name,$result->spouse_patronymic);
        $short_buyer_fio = $this->format_shortfio($result->buyer_surname,$result->buyer_name,$result->buyer_patronymic);
        $short_vendor_law_fio = $this->format_shortfio($result->vendor_law_surname,$result->vendor_law_name,$result->vendor_law_patronymic);
        $short_buyer_law_fio = $this->format_shortfio($result->buyer_law_surname,$result->buyer_law_name,$result->buyer_law_patronymic);
        $short_vendor_ind_fio = $this->format_shortfio($result->vendor_ind_surname,$result->vendor_ind_name,$result->vendor_ind_patronymic);
        $short_buyer_ind_fio = $this->format_shortfio($result->buyer_ind_surname,$result->buyer_ind_name,$result->buyer_ind_patronymic);
        $short_vendor_agent_fio = $this->format_shortfio($result->for_agent_vendor_surname,$result->for_agent_vendor_name,$result->for_agent_vendor_patronymic);
        $short_buyer_agent_fio = $this->format_shortfio($result->for_agent_buyer_surname,$result->for_agent_buyer_name,$result->for_agent_buyer_patronymic);
        // Родительское ФИО
        $vendor_fio_parent = $this->format_fio($result->vendor_surname_parent, $result->vendor_name_parent, $result->vendor_patronymic_parent);
        $vendor_ind_fio_parent = $this->format_fio($result->vendor_ind_surname_parent,$result->vendor_ind_name_parent,$result->vendor_ind_patronymic_parent);
        $vendor_agent_fio_parent = $this->format_fio($result->for_agent_vendor_surname_parent,$result->for_agent_vendor_name_parent,$result->for_agent_vendor_patronymic_parent);
        $spouse_fio_parent = $this->format_fio($result->spouse_surname_parent,$result->spouse_name_parent,$result->spouse_patronymic_parent);
        //Адрес
        $vendor_adress = $this->format_adress($result->vendor_city,$result->vendor_street,$result->vendor_house,$result->vendor_flat);
        $vendor_ind_adress = $this->format_adress($result->vendor_ind_city,$result->vendor_ind_street,$result->vendor_ind_house,$result->vendor_ind_flat);
        $spouse_adress = $this->format_adress($result->spouse_city,$result->spouse_street,$result->spouse_house,$result->spouse_flat);
        //Дата
        $date_of_contract = $this->format_date($result->date_of_contract);
        $vendor_passport_date = $this->format_date($result->vendor_passport_date);
        $date_of_product = $result->date_of_product;
        $spouse_pass_date = $this->format_date($result->spouse_pass_date);
        $marriage_svid_date = $this->format_date($result->marriage_svid_date);
        $date_of_serial_car = $this->format_date($result->date_of_serial_car);
        $vendor_birthday = $this->format_date($result->vendor_birthday);
        $vendor_ind_birthday = $this->format_date($result->vendor_ind_birthday);
        //Имена сторон
        $buyer_namedata = array
        (
            'phys_name' => $buyer_fio,
            'law_name' => $buyer_law_fio,
            'ind_name' => $buyer_ind_fio,
            'agent_name' => $buyer_agent_fio
        );
        $vendor_namedata = array
        (
            'phys_name' => $vendor_fio,
            'ind_name' => $vendor_ind_fio,
            'agent_name' => $vendor_agent_fio
        );
        $vendor_namedata_parent = array
        (
            'phys_name' => $vendor_fio_parent,
            'ind_name' => $vendor_ind_fio_parent,
            'agent_name' => $vendor_agent_fio_parent
        );
        $vendor_shortnamedata = array
        (
            'phys_name' => $short_vendor_fio,
            'ind_name' => $short_vendor_ind_fio,
            'agent_name' => $short_vendor_agent_fio
        );
        $buyer_name = $this->get_side_name($result->type_of_taker, $result->buyer_is_owner_car, $buyer_namedata);
        $vendor_name = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_namedata);
        $vendor_name_parent = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_namedata_parent);
        $vendor_shortname = $this->get_side_name($result->type_of_giver, $result->vendor_is_owner_car, $vendor_shortnamedata);

        $price_str = $this->num2str($result->price_car);
        $document = $this->word->loadTemplate($_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/patterns/statement_vendor_marriage.docx');

        $document->setValue('date_of_contract', $date_of_contract);
        $document->setValue('buyer_name', $buyer_name);
        $document->setValue('vendor_name', $vendor_name);
        $document->setValue('vendor_name_parent', $vendor_name_parent);
        $document->setValue('vendor_shortname', $vendor_shortname);
        //Костыльеты
        //Если физическое лицо
        if ($result->type_of_giver == 'physical')
        {
            $document->setValue('vendor_birthday', $vendor_birthday);
            $document->setValue('vendor_passport_serial', $result->vendor_passport_serial);
            $document->setValue('vendor_passport_number', $result->vendor_passport_number);
            $document->setValue('vendor_passport_bywho', $result->vendor_passport_bywho);
            $document->setValue('vendor_passport_date', $vendor_passport_date);
            $document->setValue('vendor_adress', $vendor_adress);
        }
        //Если инд. лицо
        elseif ($result->type_of_giver == 'individual')
        {
            $document->setValue('vendor_birthday', $vendor_ind_birthday);
            $document->setValue('vendor_passport_serial', $result->vendor_ind_passport_serial);
            $document->setValue('vendor_passport_number', $result->vendor_ind_passport_number);
            $document->setValue('vendor_passport_bywho', $result->vendor_ind_passport_bywho);
            $document->setValue('vendor_passport_date', $vendor_passport_date);
            $document->setValue('vendor_adress', $vendor_ind_adress);
        }

        $document->setValue('place_of_contract', $result->place_of_contract);
        $document->setValue('reg_gov_number', $result->reg_gov_number);
        $document->setValue('vin', $result->vin);
        $document->setValue('mark', $result->mark);
        $document->setValue('date_of_product', $date_of_product);
        $document->setValue('engine_model', $result->engine_model);
        $document->setValue('carcass', $result->carcass);
        $document->setValue('serial_car', $result->serial_car);
        $document->setValue('number_of_serial_car', $result->number_of_serial_car);
        $document->setValue('bywho_serial_car', $result->bywho_serial_car);
        $document->setValue('date_of_serial_car', $date_of_serial_car);
        $document->setValue('spouse_fio', $spouse_fio_parent);
        $document->setValue('spouse_shortname', $short_spouse_fio);
        $document->setValue('spouse_pass_serial', $result->spouse_pass_serial);
        $document->setValue('spouse_pass_number', $result->spouse_pass_number);
        $document->setValue('spouse_pass_bywho', $result->spouse_pass_bywho);
        $document->setValue('spouse_pass_date', $spouse_pass_date);
        $document->setValue('spouse_adress', $spouse_adress);
        $document->setValue('marriage_svid_serial', $result->marriage_svid_serial);
        $document->setValue('marriage_svid_number', $result->marriage_svid_number);
        $document->setValue('marriage_svid_bywho', $result->marriage_svid_bywho);
        $document->setValue('marriage_svid_date', $marriage_svid_date);
        $document->setValue('price_car', $result->price_car);
        $document->setValue('price_str', $price_str);
        $document->setValue('currency', $result->currency);

        // Сохранение результатов
        $name_of_file = $_SERVER['DOCUMENT_ROOT'] . '/documents/buy_sale/'.$id.'statement_vendor_marriage.docx';//Имя файла и путь к нему
        $document->save($name_of_file); // Сохранение документа
        $name_for_server = '/documents/buy_sale/'.$id.'statement_vendor_marriage.docx';
        exec('unoconv -f pdf /var/www/carsdoc.ru'.$name_for_server);
        exec('rm /var/www/carsdoc.ru'.$name_for_server);
        $name_for_server = '/documents/buy_sale/'.$id.'statement_vendor_marriage.pdf';
        $this->file_force_download('/var/www/carsdoc.ru'.$name_for_server);
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------
    //договор аренды
   /* public function get_doc_rent()
    {

    }*/
    //------------------------------------------------------------------------------------------------------------------
    public function insert_into_database_buysale()
    {
        //Проверка на пустоту
        //_______________________________
        //Массив исключений
        $exception = array
        (
            'vendor_phone' => '',
            'vendor_law_proxy_number' => '',
            'vendor_law_proxy_date' => '',
//            'buyer_phone' => '',
            'buyer_law_proxy_number' => '',
            'buyer_law_proxy_date' => '',
            'engine_model' => '',
            'shassi' => '',
            'carcass' => '',
            'other_parameters' => '',
            'additional_devices_array' => '',
            'oil_in_car' => '',
            'car_allstatus' => '',
            'maintenance_date' => '',
            'maintenance_bywho' => '',
            'penalty' => '',
            'gibdd_inn' => '',
            //Правочки
            'reg_gov_number' => '',
            'vendor_ind_phone' => '',
            'vendor_ind_bank_acc' => '',
            'vendor_ind_bank_name' => '',
            'vendor_ind_korr_acc' => '',
            'vendor_ind_bik' => '',
            'buyer_ind_phone' => '',
            'buyer_ind_bank_acc' => '',
            'buyer_ind_bank_name' => '',
            'buyer_ind_korr_acc' => '',
            'buyer_ind_bik' => '',
            'for_agent_vendor_proxy_notary' => '',
            'for_agent_buyer_proxy_notary' => '',
        );

        foreach ($_POST as $key => $value)
        {
            if ($_POST["$key"] == $exception["$key"])
            {
                continue;
            }
            else
            {
                if(empty($_POST["$key"]))
                {
                    redirect('/');
                }
            }
        }
        //_______________________________
        $type_id = $this->set_pack_of_documents($_POST['type_of_giver'], $_POST['type_of_taker'], 'buy_sale', $_POST['police_form'], $_POST['car_in_marriage']);
        if ($_POST['defects'] == 'false') {$_POST['defects'] = 'не указано';}
        if ($_POST['features'] == 'false') {$_POST['features'] = 'не указано';}
        $data = array
        (
            'type_id' => $type_id,
            'date' => date("Y-m-d H:I:s"),
            'type_of_contract' => $_POST['type_of_contract'],
            'place_of_contract' => $_POST['place_of_contract'],
            'date_of_contract' => $_POST['date_of_contract'],
            'type_of_giver' => $_POST['type_of_giver'],
            'vendor_is_owner_car' => $_POST['vendor_is_owner_car'],
            //New info begind
            //Новые инпуты с родительским подажем
            'for_agent_vendor_surname_parent' => $_POST['for_agent_vendor_surname_parent'],
            'for_agent_vendor_name_parent' => $_POST['for_agent_vendor_name_parent'],
            'for_agent_vendor_patronymic_parent' => $_POST['for_agent_vendor_patronymic_parent'],
            'for_agent_buyer_surname_parent' => $_POST['for_agent_buyer_surname_parent'],
            'for_agent_buyer_name_parent' => $_POST['for_agent_buyer_name_parent'],
            'for_agent_buyer_patronymic_parent' => $_POST['for_agent_buyer_patronymic_parent'],
            'vendor_surname_parent' => $_POST['vendor_surname_parent'],
            'vendor_name_parent' => $_POST['vendor_name_parent'],
            'vendor_patronymic_parent' => $_POST['vendor_patronymic_parent'],
            'buyer_surname_parent' => $_POST['buyer_surname_parent'],
            'buyer_name_parent' => $_POST['buyer_name_parent'],
            'buyer_patronymic_parent' => $_POST['buyer_patronymic_parent'],
            'vendor_law_actor_name_parent' => $_POST['vendor_law_actor_name_parent'],
            'vendor_law_actor_surname_parent' => $_POST['vendor_law_actor_surname_parent'],
            'vendor_law_actor_patronymic_parent' => $_POST['vendor_law_actor_patronymic_parent'],
            'buyer_law_actor_name_parent' => $_POST['buyer_law_actor_name_parent'],
            'buyer_law_actor_surname_parent' => $_POST['buyer_law_actor_surname_parent'],
            'buyer_law_actor_patronymic_parent' => $_POST['buyer_law_actor_patronymic_parent'],
            'vendor_ind_surname_parent' => $_POST['vendor_ind_surname_parent'],
            'vendor_ind_name_parent' => $_POST['vendor_ind_name_parent'],
            'vendor_ind_patronymic_parent' => $_POST['vendor_ind_patronymic_parent'],
            'buyer_ind_surname_parent' => $_POST['buyer_ind_surname_parent'],
            'buyer_ind_name_parent' => $_POST['buyer_ind_name_parent'],
            'buyer_ind_patronymic_parent' => $_POST['buyer_ind_patronymic_parent'],
            'spouse_surname_parent' => $_POST['spouse_surname_parent'],
            'spouse_name_parent' => $_POST['spouse_name_parent'],
            'spouse_patronymic_parent' => $_POST['spouse_patronymic_parent'],

            //Доверенное лицо
                //Продавец
            'for_agent_vendor_surname' => $_POST['for_agent_vendor_surname'],
            'for_agent_vendor_name' => $_POST['for_agent_vendor_name'],
            'for_agent_vendor_patronymic' => $_POST['for_agent_vendor_patronymic'],
            'for_agent_vendor_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
            'for_agent_vendor_proxy_date' => $_POST['for_agent_vendor_proxy_date'],
            'for_agent_vendor_proxy_notary' => $_POST['for_agent_vendor_proxy_notary'],
            //Новые. Свежие
            'agent_vendor_birthday' => $_POST['agent_vendor_birthday'],
            'agent_vendor_pass_serial' => $_POST['agent_vendor_pass_serial'],
            'agent_vendor_pass_number' => $_POST['agent_vendor_pass_number'],
            'agent_vendor_pass_date' => $_POST['agent_vendor_pass_date'],
            'agent_vendor_pass_bywho' => $_POST['agent_vendor_pass_bywho'],
            'agent_vendor_city' => $_POST['agent_vendor_city'],
            'agent_vendor_street' => $_POST['agent_vendor_street'],
            'agent_vendor_house' => $_POST['agent_vendor_house'],
            'agent_vendor_flat' => $_POST['agent_vendor_flat'],
            'agent_vendor_phone' => $_POST['agent_vendor_phone'],
                //Покуппатель
            'for_agent_buyer_surname' => $_POST['for_agent_buyer_surname'],
            'for_agent_buyer_name' => $_POST['for_agent_buyer_name'],
            'for_agent_buyer_patronymic' => $_POST['for_agent_buyer_patronymic'],
            'for_agent_buyer_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
            'for_agent_buyer_proxy_date' => $_POST['for_agent_buyer_proxy_date'],
            'for_agent_buyer_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],

            //Юридическое лицо
                //Продавец
            'vendor_law_company_name' => $_POST['vendor_law_company_name'],
            'vendor_law_actor_position' => $_POST['vendor_law_actor_position'],
            'vendor_law_actor_name' => $_POST['vendor_law_actor_name'],
            'vendor_law_actor_surname' => $_POST['vendor_law_actor_surname'],
            'vendor_law_actor_patronymic' => $_POST['vendor_law_actor_patronymic'],
            'vendor_law_document_osn' => $_POST['vendor_law_document_osn'],
            'vendor_law_proxy_number' => $_POST['vendor_law_proxy_number'],
            'vendor_law_proxy_date' => $_POST['vendor_law_proxy_date'],
            'vendor_law_inn' => $_POST['vendor_law_inn'],
            'vendor_law_ogrn' => $_POST['vendor_law_ogrn'],
            'vendor_law_adress' => $_POST['vendor_law_adress'],
            'vendor_law_phone' => $_POST['vendor_law_phone'],
            'vendor_law_acc' => $_POST['vendor_law_acc'],
            'vendor_law_bank_name' => $_POST['vendor_law_bank_name'],
            'vendor_law_korr_acc' => $_POST['vendor_law_korr_acc'],
            'vendor_law_bik' => $_POST['vendor_law_bik'],

                //Покупатель
            'buyer_law_company_name' => $_POST['buyer_law_company_name'],
            'buyer_law_actor_position' => $_POST['buyer_law_actor_position'],
            'buyer_law_actor_name' => $_POST['buyer_law_actor_name'],
            'buyer_law_actor_surname' => $_POST['buyer_law_actor_surname'],
            'buyer_law_actor_patronymic' => $_POST['buyer_law_actor_patronymic'],
            'buyer_law_document_osn' => $_POST['buyer_law_document_osn'],
            'buyer_law_proxy_number' => $_POST['buyer_law_proxy_number'],
            'buyer_law_proxy_date' => $_POST['buyer_law_proxy_date'],
            'buyer_law_inn' => $_POST['buyer_law_inn'],
            'buyer_law_ogrn' => $_POST['buyer_law_ogrn'],
            'buyer_law_adress' => $_POST['buyer_law_adress'],
            'buyer_law_phone' => $_POST['buyer_law_phone'],
            'buyer_law_acc' => $_POST['buyer_law_acc'],
            'buyer_law_bank_name' => $_POST['buyer_law_bank_name'],
            'buyer_law_korr_acc' => $_POST['buyer_law_korr_acc'],
            'buyer_law_bik' => $_POST['buyer_law_bik'],
            //Индивидуальный предприниматель
                //Продавец
            'vendor_ind_surname' => $_POST['vendor_ind_surname'],
            'vendor_ind_name' => $_POST['vendor_ind_name'],
            'vendor_ind_patronymic' => $_POST['vendor_ind_patronymic'],
            'vendor_ind_number_of_certificate' => $_POST['vendor_ind_number_of_certificate'],
            'vendor_ind_date_of_certificate' => $_POST['vendor_ind_date_of_certificate'],
            'vendor_ind_birthday' => $_POST['vendor_ind_birthday'],
            'vendor_ind_passport_serial' => $_POST['vendor_ind_passport_serial'],
            'vendor_ind_passport_number' => $_POST['vendor_ind_passport_number'],
            'vendor_ind_passport_date' => $_POST['vendor_ind_passport_date'],
            'vendor_ind_passport_bywho' => $_POST['vendor_ind_passport_bywho'],
            'vendor_ind_city' => $_POST['vendor_ind_city'],
            'vendor_ind_street' => $_POST['vendor_ind_street'],
            'vendor_ind_house' => $_POST['vendor_ind_house'],
            'vendor_ind_flat' => $_POST['vendor_ind_flat'],
            'vendor_ind_phone' => $_POST['vendor_ind_phone'],
            'vendor_ind_bank_acc' => $_POST['vendor_ind_bank_acc'],
            'vendor_ind_bank_name' => $_POST['vendor_ind_bank_name'],
            'vendor_ind_korr_acc' => $_POST['vendor_ind_korr_acc'],
            'vendor_ind_bik' => $_POST['vendor_ind_bik'],
                //Покупатель
            'buyer_ind_surname' => $_POST['buyer_ind_surname'],
            'buyer_ind_name' => $_POST['buyer_ind_name'],
            'buyer_ind_patronymic' => $_POST['buyer_ind_patronymic'],
            'buyer_ind_number_of_certificate' => $_POST['buyer_ind_number_of_certificate'],
            'buyer_ind_date_of_certificate' => $_POST['buyer_ind_date_of_certificate'],
            'buyer_ind_birthday' => $_POST['buyer_ind_birthday'],
            'buyer_ind_passport_serial' => $_POST['buyer_ind_passport_serial'],
            'buyer_ind_passport_number' => $_POST['buyer_ind_passport_number'],
            'buyer_ind_passport_date' => $_POST['buyer_ind_passport_date'],
            'buyer_ind_passport_bywho' => $_POST['buyer_ind_passport_bywho'],
            'buyer_ind_city' => $_POST['buyer_ind_city'],
            'buyer_ind_street' => $_POST['buyer_ind_street'],
            'buyer_ind_house' => $_POST['buyer_ind_house'],
            'buyer_ind_flat' => $_POST['buyer_ind_flat'],
            'buyer_ind_phone' => $_POST['buyer_ind_phone'],
            'buyer_ind_bank_acc' => $_POST['buyer_ind_bank_acc'],
            'buyer_ind_bank_name' => $_POST['buyer_ind_bank_name'],
            'buyer_ind_korr_acc' => $_POST['buyer_ind_korr_acc'],
            'buyer_ind_bik' => $_POST['buyer_ind_bik'],
            //Для модального окна
            'for_agent_proxy_pass_serial' => $_POST['for_agent_proxy_pass_serial'],
            'for_agent_proxy_pass_number' => $_POST['for_agent_proxy_pass_number'],
            'for_agent_proxy_pass_date' => $_POST['for_agent_proxy_pass_date'],
            'for_agent_proxy_pass_bywho' => $_POST['for_agent_proxy_pass_bywho'],
            'for_agent_proxy_city' => $_POST['for_agent_proxy_city'],
            'for_agent_proxy_street' => $_POST['for_agent_proxy_street'],
            'for_agent_proxy_house' => $_POST['for_agent_proxy_house'],
            'for_agent_proxy_flat' => $_POST['for_agent_proxy_flat'],
            'for_agent_proxy_phone' => $_POST['for_agent_proxy_phone'],
            'for_agent_proxy_birthday' => $_POST['for_agent_proxy_birthday'],
            //Гибдд
            'police_form' => $_POST['police_form'],
            'gibdd_power_engine' => $_POST['gibdd_power_engine'],
            'gibdd_eco_class' => $_POST['gibdd_eco_class'],
            'gibdd_inn' => $_POST['gibdd_inn'],
            'gibdd_max_mass' => $_POST['gibdd_max_mass'],
            'gibdd_min_mass' => $_POST['gibdd_min_mass'],
            'gibdd_reg_name' => $_POST['gibdd_reg_name'],
            'statement_form' =>$_POST['statement_form'],
            //New info end
            'vendor_surname' => $_POST['vendor_surname'],
            'vendor_name' => $_POST['vendor_name'],
            'vendor_patronymic' => $_POST['vendor_patronymic'],
            'vendor_birthday' => $_POST['vendor_birthday'],
            'vendor_passport_serial' => $_POST['vendor_passport_serial'],
            'vendor_passport_number' => $_POST['vendor_passport_number'],
            'vendor_passport_date' => $_POST['vendor_passport_date'],
            'vendor_passport_bywho' => $_POST['vendor_passport_bywho'],
            'vendor_city' => $_POST['vendor_city'],
            'vendor_street' => $_POST['vendor_street'],
            'vendor_house' => $_POST['vendor_house'],
            'vendor_flat' => $_POST['vendor_flat'],
            'vendor_phone' => $_POST['vendor_phone'],
            'type_of_taker' => $_POST['type_of_taker'],
            'buyer_surname' => $_POST['buyer_surname'],
            'buyer_name' => $_POST['buyer_name'],
            'buyer_patronymic' => $_POST['buyer_patronymic'],
            'buyer_is_owner_car' => $_POST['buyer_is_owner_car'],
            'buyer_birthday' => $_POST['buyer_birthday'],
            'buyer_passport_serial' => $_POST['buyer_passport_serial'],
            'buyer_passport_number' => $_POST['buyer_passport_number'],
            'buyer_passport_date' => $_POST['buyer_passport_date'],
            'buyer_passport_bywho' => $_POST['buyer_passport_bywho'],
            'buyer_city' => $_POST['buyer_city'],
            'buyer_street' => $_POST['buyer_street'],
            'buyer_house' => $_POST['buyer_house'],
            'buyer_flat' => $_POST['buyer_flat'],
            'buyer_phone' => $_POST['buyer_phone'],
            'mark' => $_POST['mark'],
            'vin' => $_POST['vin'],
            'reg_gov_number' => $_POST['reg_gov_number'],
            'car_type' => $_POST['car_type'],
            'category' => $_POST['category'],
            'date_of_product' => $_POST['date_of_product'],
            'engine_model' => $_POST['engine_model'],
            'shassi' => $_POST['shassi'],
            'carcass' => $_POST['carcass'],
            'color_carcass' => $_POST['color_carcass'],
            'other_parameters' => $_POST['other_parameters'],
            'serial_car' => $_POST['serial_car'],
            'number_of_serial_car' => $_POST['number_of_serial_car'],
            'date_of_serial_car' => $_POST['date_of_serial_car'],
            'bywho_serial_car' => $_POST['bywho_serial_car'],
            'price_car' => $_POST['price_car'],
            'currency' => $_POST['currency'],
            'additional_devices' => ($_POST['additional_devices']),
            'additional_devices_array' => json_encode($_POST['additional_devices_array']),
            'oil_in_car' => $_POST['oil_in_car'],
            'car_allstatus' => $_POST['car_allstatus'],
            'maintenance_date' => $_POST['maintenance_date'],
            'maintenance_bywho' => $_POST['maintenance_bywho'],
            'defects' => $_POST['defects'],
            'features' => $_POST['features'],
            'payment_date' => $_POST['payment_date'],
            'credit' => $_POST['credit'],
            'credit_currency' => $_POST['credit_currency'],
            'credit_date' => $_POST['credit_date'],
            'documents' => json_encode($_POST['documents']),
            'accessories' => json_encode($_POST['accessories']),
            'car_in_marriage' => $_POST['car_in_marriage'],
            'spouse_surname' => $_POST['spouse_surname'],
            'spouse_name' => $_POST['spouse_name'],
            'spouse_patronymic' => $_POST['spouse_patronymic'],
            'spouse_birthday' => $_POST['spouse_birthday'],
            'spouse_pass_serial' => $_POST['spouse_pass_serial'],
            'spouse_pass_number' => $_POST['spouse_pass_number'],
            'spouse_pass_date' => $_POST['spouse_pass_date'],
            'spouse_pass_bywho' => $_POST['spouse_pass_bywho'],
            'spouse_city' => $_POST['spouse_city'],
            'spouse_street' => $_POST['spouse_street'],
            'spouse_house' => $_POST['spouse_house'],
            'spouse_flat' => $_POST['spouse_flat'],
            'marriage_svid_serial' => $_POST['marriage_svid_serial'],
            'marriage_svid_number' => $_POST['marriage_svid_number'],
            'marriage_svid_date' => $_POST['marriage_svid_date'],
            'marriage_svid_bywho' => $_POST['marriage_svid_bywho'],
            'penalty' => (empty($_POST['penalty']) ? '0%' : $_POST['penalty']),
            //новые данные юр.лица
            'vendor_law_date_of_create' => $_POST['vendor_law_date_of_create'],
            'buyer_law_date_of_create' => $_POST['buyer_law_date_of_create'],
        );
        //Бизопаснасть
        /*foreach ($data as $key)
        {
            mysql_real_escape_string($key);
        }*/
        //Отправка данных
        $this->db->insert('buy_sale', $data);
        $doc_id = $this->db->insert_id();
        return $doc_id;//
    }
    //------------------------------------------------------------------------------------------------------------------
    public function insert_into_database_gift()
    {
        //Проверка на пустоту
        //_______________________________
        //Массив исключений
        $exception = array
        (
            'vendor_phone' => '',
//            'buyer_phone' => '',
            'vendor_law_proxy_number' => '',
            'vendor_law_proxy_date' => '',
            'buyer_law_proxy_number' => '',
            'buyer_law_proxy_date' => '',
            'engine_model' => '',
            'shassi' => '',
            'carcass' => '',
            'gibdd_inn' => '',
            'reg_gov_number' => '',
            'vendor_ind_phone' => '',
            'vendor_ind_bank_acc' => '',
            'vendor_ind_bank_name' => '',
            'vendor_ind_korr_acc' => '',
            'vendor_ind_bik' => '',
            'buyer_ind_phone' => '',
            'buyer_ind_bank_acc' => '',
            'buyer_ind_bank_name' => '',
            'buyer_ind_korr_acc' => '',
            'buyer_ind_bik' => '',
            'for_agent_vendor_proxy_notary' => '',
            'for_agent_buyer_proxy_notary' => '',
        );

        foreach ($_POST as $key => $value)
        {
            if ($_POST["$key"] == $exception["$key"])
            {
                continue;
            }
            else
            {
                if(empty($_POST["$key"]))
                {
                    redirect('/');
                }
            }
        }
        //_______________________________
        $type_id = $this->set_pack_of_documents($_POST['type_of_giver'], $_POST['type_of_taker'], 'gift', $_POST['police_form'] );
        $data = array
        (
            'type_id' => $type_id,
            'type_of_contract' => $_POST['type_of_contract'],
            'place_of_contract' => $_POST['place_of_contract'],
            'date_of_contract' => $_POST['date_of_contract'],
            //Новые переменные
            'for_agent_vendor_surname_parent' => $_POST['for_agent_vendor_surname_parent'],
            'for_agent_vendor_name_parent' => $_POST['for_agent_vendor_name_parent'],
            'for_agent_vendor_patronymic_parent' => $_POST['for_agent_vendor_patronymic_parent'],
            'for_agent_buyer_surname_parent' => $_POST['for_agent_buyer_surname_parent'],
            'for_agent_buyer_name_parent' => $_POST['for_agent_buyer_name_parent'],
            'for_agent_buyer_patronymic_parent' => $_POST['for_agent_buyer_patronymic_parent'],
            'vendor_surname_parent' => $_POST['vendor_surname_parent'],
            'vendor_name_parent' => $_POST['vendor_name_parent'],
            'vendor_patronymic_parent' => $_POST['vendor_patronymic_parent'],
            'buyer_surname_parent' => $_POST['buyer_surname_parent'],
            'buyer_name_parent' => $_POST['buyer_name_parent'],
            'buyer_patronymic_parent' => $_POST['buyer_patronymic_parent'],
            'vendor_law_actor_name_parent' => $_POST['vendor_law_actor_name_parent'],
            'vendor_law_actor_surname_parent' => $_POST['vendor_law_actor_surname_parent'],
            'vendor_law_actor_patronymic_parent' => $_POST['vendor_law_actor_patronymic_parent'],
            'buyer_law_actor_name_parent' => $_POST['buyer_law_actor_name_parent'],
            'buyer_law_actor_surname_parent' => $_POST['buyer_law_actor_surname_parent'],
            'buyer_law_actor_patronymic_parent' => $_POST['buyer_law_actor_patronymic_parent'],
            'vendor_ind_surname_parent' => $_POST['vendor_ind_surname_parent'],
            'vendor_ind_name_parent' => $_POST['vendor_ind_name_parent'],
            'vendor_ind_patronymic_parent' => $_POST['vendor_ind_patronymic_parent'],
            'buyer_ind_surname_parent' => $_POST['buyer_ind_surname_parent'],
            'buyer_ind_name_parent' => $_POST['buyer_ind_name_parent'],
            'buyer_ind_patronymic_parent' => $_POST['buyer_ind_patronymic_parent'],
            'spouse_surname_parent' => $_POST['spouse_surname_parent'],
            'spouse_name_parent' => $_POST['spouse_name_parent'],
            'spouse_patronymic_parent' => $_POST['spouse_patronymic_parent'],
            //Конец новых
            'type_of_giver' => $_POST['type_of_giver'],
            'vendor_is_owner_car' => $_POST['vendor_is_owner_car'],
            'vendor_surname' => $_POST['vendor_surname'],
            'vendor_name' => $_POST['vendor_name'],
            'vendor_patronymic' => $_POST['vendor_patronymic'],
            'vendor_birthday' => $_POST['vendor_birthday'],
            'vendor_passport_serial' => $_POST['vendor_passport_serial'],
            'vendor_passport_number' => $_POST['vendor_passport_number'],
            'vendor_passport_date' => $_POST['vendor_passport_date'],
            'vendor_passport_bywho' => $_POST['vendor_passport_bywho'],
            'vendor_city' => $_POST['vendor_city'],
            'vendor_street' => $_POST['vendor_street'],
            'vendor_house' => $_POST['vendor_house'],
            'vendor_flat' => $_POST['vendor_flat'],
            'vendor_phone' => $_POST['vendor_phone'],
            'vendor_law_company_name' => $_POST['vendor_law_company_name'],
            'vendor_law_actor_position' => $_POST['vendor_law_actor_position'],
            'vendor_law_actor_name' => $_POST['vendor_law_actor_name'],
            'vendor_law_actor_surname' => $_POST['vendor_law_actor_surname'],
            'vendor_law_actor_patronymic' => $_POST['vendor_law_actor_patronymic'],
            'vendor_law_document_osn' => $_POST['vendor_law_document_osn'],
            'vendor_law_proxy_number' => $_POST['vendor_law_proxy_number'],
            'vendor_law_proxy_date' => $_POST['vendor_law_proxy_date'],
            'vendor_law_inn' => $_POST['vendor_law_inn'],
            'vendor_law_ogrn' => $_POST['vendor_law_ogrn'],
            'vendor_law_adress' => $_POST['vendor_law_adress'],
            'buyer_law_adress' => $_POST['buyer_law_adress'],
//            'vendor_law_city' => $_POST['vendor_law_city'],
//            'vendor_law_street' => $_POST['vendor_law_street'],
//            'vendor_law_house' => $_POST['vendor_law_house'],
//            'vendor_law_flat' => $_POST['vendor_law_flat'],
            'vendor_law_phone' => $_POST['vendor_law_phone'],
            'vendor_law_acc' => $_POST['vendor_law_acc'],
            'vendor_law_bank_name' => $_POST['vendor_law_bank_name'],
            'vendor_law_korr_acc' => $_POST['vendor_law_korr_acc'],
            'vendor_law_bik' => $_POST['vendor_law_bik'],
            'vendor_ind_surname' => $_POST['vendor_ind_surname'],
            'vendor_ind_name' => $_POST['vendor_ind_name'],
            'vendor_ind_patronymic' => $_POST['vendor_ind_patronymic'],
            'vendor_ind_number_of_certificate' => $_POST['vendor_ind_number_of_certificate'],
            'vendor_ind_date_of_certificate' => $_POST['vendor_ind_date_of_certificate'],
            'vendor_ind_birthday' => $_POST['vendor_ind_birthday'],
            'vendor_ind_passport_serial' => $_POST['vendor_ind_passport_serial'],
            'vendor_ind_passport_number' => $_POST['vendor_ind_passport_number'],
            'vendor_ind_passport_date' => $_POST['vendor_ind_passport_date'],
            'vendor_ind_passport_bywho' => $_POST['vendor_ind_passport_bywho'],
            'vendor_ind_city' => $_POST['vendor_ind_city'],
            'vendor_ind_street' => $_POST['vendor_ind_street'],
            'vendor_ind_house' => $_POST['vendor_ind_house'],
            'vendor_ind_flat' => $_POST['vendor_ind_flat'],
            'vendor_ind_phone' => $_POST['vendor_ind_phone'],
            'vendor_ind_bank_acc' => $_POST['vendor_ind_bank_acc'],
            'vendor_ind_bank_name' => $_POST['vendor_ind_bank_name'],
            'vendor_ind_korr_acc' => $_POST['vendor_ind_korr_acc'],
            'vendor_ind_bik' => $_POST['vendor_ind_bik'],
            'for_agent_vendor_surname' => $_POST['for_agent_vendor_surname'],
            'for_agent_vendor_name' => $_POST['for_agent_vendor_name'],
            'for_agent_vendor_patronymic' => $_POST['for_agent_vendor_patronymic'],
            'for_agent_vendor_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
            'for_agent_vendor_proxy_date' => $_POST['for_agent_vendor_proxy_date'],
            'for_agent_vendor_proxy_notary' => $_POST['for_agent_vendor_proxy_notary'],
            'type_of_taker' => $_POST['type_of_taker'],
            'buyer_is_owner_car' => $_POST['buyer_is_owner_car'],
            'buyer_surname' => $_POST['buyer_surname'],
            'buyer_name' => $_POST['buyer_name'],
            'buyer_patronymic' => $_POST['buyer_patronymic'],
            'buyer_birthday' => $_POST['buyer_birthday'],
            'buyer_passport_serial' => $_POST['buyer_passport_serial'],
            'buyer_passport_number' => $_POST['buyer_passport_number'],
            'buyer_passport_date' => $_POST['buyer_passport_date'],
            'buyer_passport_bywho' => $_POST['buyer_passport_bywho'],
            'buyer_city' => $_POST['buyer_city'],
            'buyer_street' => $_POST['buyer_street'],
            'buyer_house' => $_POST['buyer_house'],
            'buyer_flat' => $_POST['buyer_flat'],
            'buyer_phone' => $_POST['buyer_phone'],
            'buyer_law_company_name' => $_POST['buyer_law_company_name'],
            'buyer_law_actor_position' => $_POST['buyer_law_actor_position'],
            'buyer_law_actor_name' => $_POST['buyer_law_actor_name'],
            'buyer_law_actor_surname' => $_POST['buyer_law_actor_surname'],
            'buyer_law_actor_patronymic' => $_POST['buyer_law_actor_patronymic'],
            'buyer_law_document_osn' => $_POST['buyer_law_document_osn'],
            'buyer_law_proxy_number' => $_POST['buyer_law_proxy_number'],
            'buyer_law_proxy_date' => $_POST['buyer_law_proxy_date'],
            'buyer_law_inn' => $_POST['buyer_law_inn'],
            'buyer_law_ogrn' => $_POST['buyer_law_ogrn'],
            'buyer_law_city' => $_POST['buyer_law_city'],
            'buyer_law_street' => $_POST['buyer_law_street'],
            'buyer_law_house' => $_POST['buyer_law_house'],
            'buyer_law_flat' => $_POST['buyer_law_flat'],
            'buyer_law_phone' => $_POST['buyer_law_phone'],
            'buyer_law_acc' => $_POST['buyer_law_acc'],
            'buyer_law_bank_name' => $_POST['buyer_law_bank_name'],
            'buyer_law_korr_acc' => $_POST['buyer_law_korr_acc'],
            'buyer_law_bik' => $_POST['buyer_law_bik'],
            'buyer_ind_surname' => $_POST['buyer_ind_surname'],
            'buyer_ind_name' => $_POST['buyer_ind_name'],
            'buyer_ind_patronymic' => $_POST['buyer_ind_patronymic'],
            'buyer_ind_number_of_certificate' => $_POST['buyer_ind_number_of_certificate'],
            'buyer_ind_date_of_certificate' => $_POST['buyer_ind_date_of_certificate'],
            'buyer_ind_birthday' => $_POST['buyer_ind_birthday'],
            'buyer_ind_passport_serial' => $_POST['buyer_ind_passport_serial'],
            'buyer_ind_passport_number' => $_POST['buyer_ind_passport_number'],
            'buyer_ind_passport_date' => $_POST['buyer_ind_passport_date'],
            'buyer_ind_passport_bywho' => $_POST['buyer_ind_passport_bywho'],
            'buyer_ind_city' => $_POST['buyer_ind_city'],
            'buyer_ind_street' => $_POST['buyer_ind_street'],
            'buyer_ind_house' => $_POST['buyer_ind_house'],
            'buyer_ind_flat' => $_POST['buyer_ind_flat'],
            'buyer_ind_phone' => $_POST['buyer_ind_phone'],
            'buyer_ind_bank_acc' => $_POST['buyer_ind_bank_acc'],
            'buyer_ind_bank_name' => $_POST['buyer_ind_bank_name'],
            'buyer_ind_korr_acc' => $_POST['buyer_ind_korr_acc'],
            'buyer_ind_bik' => $_POST['buyer_ind_bik'],
            'mark' => $_POST['mark'],
            'vin' => $_POST['vin'],
            'reg_gov_number' => $_POST['reg_gov_number'],
            'car_type' => $_POST['car_type'],
            'category' => $_POST['category'],
            'date_of_product' => $_POST['date_of_product'],
            'engine_model' => $_POST['engine_model'],
            'shassi' => $_POST['shassi'],
            'carcass' => $_POST['carcass'],
            'color_carcass' => $_POST['color_carcass'],
            'other_parametrs' => $_POST['other_parametrs'],
            'serial_car' => $_POST['serial_car'],
            'number_of_serial_car' => $_POST['number_of_serial_car'],
            'date_of_serial_car' => $_POST['date_of_serial_car'],
            'bywho_serial_car' => $_POST['bywho_serial_car'],
            //GIBDD
            'police_form' => $_POST['police_form'],
            'statement_form' =>$_POST['statement_form'],
            'gibdd_reg_name' => $_POST['gibdd_reg_name'],
            'gibdd_inn' => $_POST['gibdd_inn'],
            'gibdd_power_engine' => $_POST['gibdd_power_engine'],
            'gibdd_eco_class' => $_POST['gibdd_eco_class'],
            'gibdd_max_mass' => $_POST['gibdd_max_mass'],
            'gibdd_min_mass' => $_POST['gibdd_min_mass'],
            //
            'for_agent_proxy_pass_serial' => $_POST['for_agent_proxy_pass_serial'],
            'for_agent_proxy_pass_number' => $_POST['for_agent_proxy_pass_number'],
            'for_agent_proxy_pass_date' => $_POST['for_agent_proxy_pass_date'],
            'for_agent_proxy_pass_bywho' => $_POST['for_agent_proxy_pass_bywho'],
            'for_agent_proxy_city' => $_POST['for_agent_proxy_city'],
            'for_agent_proxy_street' => $_POST['for_agent_proxy_street'],
            'for_agent_proxy_house' => $_POST['for_agent_proxy_house'],
            'for_agent_proxy_flat' => $_POST['for_agent_proxy_flat'],
            'for_agent_proxy_phone' => $_POST['for_agent_proxy_phone'],
            ////Новые. Свежие
            'agent_vendor_birthday' => $_POST['agent_vendor_birthday'],
            'agent_vendor_pass_serial' => $_POST['agent_vendor_pass_serial'],
            'agent_vendor_pass_number' => $_POST['agent_vendor_pass_number'],
            'agent_vendor_pass_date' => $_POST['agent_vendor_pass_date'],
            'agent_vendor_pass_bywho' => $_POST['agent_vendor_pass_bywho'],
            'agent_vendor_city' => $_POST['agent_vendor_city'],
            'agent_vendor_street' => $_POST['agent_vendor_street'],
            'agent_vendor_house' => $_POST['agent_vendor_house'],
            'agent_vendor_flat' => $_POST['agent_vendor_flat'],
            'agent_vendor_phone' => $_POST['agent_vendor_phone'],
            //Покуппатель
            'for_agent_buyer_surname' => $_POST['for_agent_buyer_surname'],
            'for_agent_buyer_name' => $_POST['for_agent_buyer_name'],
            'for_agent_buyer_patronymic' => $_POST['for_agent_buyer_patronymic'],
            'for_agent_buyer_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
            'for_agent_buyer_proxy_date' => $_POST['for_agent_buyer_proxy_date'],
            'for_agent_buyer_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
            //новые данные юр.лица
            'vendor_law_date_of_create' => $_POST['vendor_law_date_of_create'],
            'buyer_law_date_of_create' => $_POST['buyer_law_date_of_create'],
        );
        //Бизопаснасть
        /*foreach ($data as $key)
        {
            mysql_real_escape_string($key);
        }*/
        //Отправка данных
        $this->db->insert('gift', $data);
        $doc_id = $this->db->insert_id();
        return $doc_id;//
    }
    //------------------------------------------------------------------------------------------------------------------
    public function get_data_for_canvas_buysale()
    {
        //Подготовка данных
        strip_tags($_POST);
        // Обновление ифнормации в связи с ПОСТуплением
        if ($_POST['defects'] == 'false') {$_POST['defects'] = 'не указано';}
        if ($_POST['features'] == 'false') {$_POST['features'] = 'не указано';}
        include 'array_for_canvans.php';
        foreach ($_POST as $key => $value)
        {
            if(!empty($_POST["$key"]))
            {
                $data_input["$key"] = $value;
            }
        }
        //ФИО
        $vendor_fio = $this->format_fio($data_input['vendor_surname'], $data_input['vendor_name'], $data_input['vendor_patronymic']);
        $buyer_fio = $this->format_fio($data_input['buyer_surname'],$data_input['buyer_name'],$data_input['buyer_patronymic']);
        $spouse_fio = $this->format_fio($_POST['spouse_surname'],$_POST['spouse_name'],$_POST['spouse_patronymic']);
        
//        $buyer_law_fio = $this->format_fio($_POST['buyer_law_actor_surname'],$_POST['buyer_law_actor_name'],$_POST['buyer_law_actor_patronymic']);
        $vendor_ind_fio = $this->format_fio($_POST['vendor_ind_surname'],$_POST['vendor_ind_name'],$_POST['vendor_ind_patronymic']);
        $buyer_ind_fio = $this->format_fio($_POST['buyer_ind_surname'],$_POST['buyer_ind_name'],$_POST['buyer_ind_patronymic']);
        $vendor_agent_fio = $this->format_fio($_POST['for_agent_vendor_surname'],$_POST['for_agent_vendor_name'],$_POST['for_agent_vendor_patronymic']);
        $buyer_agent_fio = $this->format_fio($_POST['for_agent_buyer_surname'],$_POST['for_agent_buyer_name'],$_POST['for_agent_buyer_patronymic']);
        //Родительское фио
        $vendor_law_fio_parent = $this->format_fio($_POST['vendor_law_actor_surname_parent'],$_POST['vendor_law_actor_name_parent'],$_POST['vendor_law_actor_patronymic_parent']);
        $buyer_law_fio_parent = $this->format_fio($_POST['buyer_law_actor_surname_parent'],$_POST['buyer_law_actor_name_parent'],$_POST['buyer_law_actor_patronymic_parent']);
        $vendor_fio_parent = $this->format_fio($_POST['vendor_surname_parent'], $_POST['vendor_name_parent'], $_POST['vendor_patronymic_parent']);
        $buyer_fio_parent = $this->format_fio($_POST['buyer_surname_parent'],$_POST['buyer_name_parent'],$_POST['buyer_patronymic_parent']);
        $vendor_ind_fio_parent = $this->format_fio($_POST['vendor_ind_surname_parent'],$_POST['vendor_ind_name_parent'],$_POST['vendor_ind_patronymic_parent']);
        $buyer_ind_fio_parent = $this->format_fio($_POST['buyer_ind_surname_parent'],$_POST['buyer_ind_name_parent'],$_POST['buyer_ind_patronymic_parent']);
        $vendor_agent_fio_parent = $this->format_fio($_POST['for_agent_vendor_surname_parent'],$_POST['for_agent_vendor_name_parent'],$_POST['for_agent_vendor_patronymic_parent']);
        $buyer_agent_fio_parent = $this->format_fio($_POST['for_agent_buyer_surname_parent'],$_POST['for_agent_buyer_name_parent'],$_POST['for_agent_buyer_patronymic_parent']);
        //Адрес
        $vendor_adress = $this->format_adress($_POST['vendor_city'],$_POST['vendor_street'],$_POST['vendor_house'],$_POST['vendor_flat']);
        $buyer_adress = $this->format_adress($_POST['buyer_city'],$_POST['buyer_street'],$_POST['buyer_house'],$_POST['buyer_flat']);
        $vendor_ind_adress = $this->format_adress($_POST['vendor_ind_city'],$_POST['vendor_ind_street'],$_POST['vendor_ind_house'],$_POST['vendor_ind_flat']);
        $buyer_ind_adress = $this->format_adress($_POST['buyer_ind_city'],$_POST['buyer_ind_street'],$_POST['buyer_ind_house'],$_POST['buyer_ind_flat']);
        //Новые адресса
        $agent_vendor_adress = $this->format_adress($_POST['agent_vendor_city'],$_POST['agent_vendor_street'],$_POST['agent_vendor_house'],$_POST['agent_vendor_flat']);
        $for_agent_proxy_adress = $this->format_adress($_POST['for_agent_proxy_city'],$_POST['for_agent_proxy_street'],$_POST['for_agent_proxy_house'],$_POST['for_agent_proxy_flat']);
        //Дата
        $date_of_contract = !empty($_POST['date_of_contract']) ? $this->format_date($_POST['date_of_contract']) : $data_input['date_of_contract'];
        $date_of_product = !empty($_POST['date_of_product']) ? $_POST['date_of_product'] : $data_input['date_of_product'];
        $vendor_birthday = $this->format_date($_POST['vendor_birthday'], true);
        $vendor_passport_date = $this->format_date($_POST['vendor_passport_date']);
        $buyer_passport_date = $this->format_date($_POST['buyer_passport_date']);
        $buyer_birthday = $this->format_date($_POST['buyer_birthday'], true);
        $vendor_ind_birthday= $this->format_date($_POST['vendor_ind_birthday'], true);
        $vendor_ind_passport_date= $this->format_date($_POST['vendor_ind_passport_date'], true);
        $for_agent_buyer_proxy_date = $this->format_date($_POST['for_agent_buyer_proxy_date']);
//        $payment_date = $this->format_date($_POST['payment_date']);
        $date_of_serial_car = !empty($_POST['date_of_serial_car']) ? $this->format_date($_POST['date_of_serial_car']) : $data_input['date_of_serial_car'];
        $maintenance_date = !empty($_POST['maintenance_date']) ? $this->format_date($_POST['maintenance_date']) : $data_input['maintenance_date'];
        $for_agent_vendor_proxy_date = $this->format_date($_POST['for_agent_vendor_proxy_date']);
        $buyer_ind_birthday = $this->format_date($_POST['buyer_ind_birthday'], true);
        $buyer_ind_passport_date = $this->format_date($_POST['buyer_ind_passport_date']);
        $vendor_law_proxy_date = $this->format_date($_POST['$vendor_law_proxy_date']);
        $buyer_law_proxy_date =  $this->format_date($_POST['$buyer_law_proxy_date']);
        $vendor_ind_date_of_certificate =  $this->format_date($_POST['$vendor_ind_date_of_certificate']);
        $buyer_ind_date_of_certificate =  $this->format_date($_POST['$buyer_ind_date_of_certificate']);
        $credit_date =  $this->format_date($_POST['credit_date']);
        //Новая дата
        $agent_vendor_birthday = $this->format_date($_POST['agent_vendor_birthday'], true);
        $agent_vendor_pass_date = $this->format_date($_POST['agent_vendor_pass_date']);
        $for_agent_proxy_birthday = $this->format_date($_POST['for_agent_proxy_birthday'], true);
        $for_agent_proxy_pass_date = $this->format_date($_POST['for_agent_proxy_pass_date']);



        //Джсон
        $documents = !empty($_POST['documents']) ? $this->json_to_string(json_encode($_POST['documents'])) : $data_input['documents'];
        $additional_devices_array = !empty($_POST['additional_devices_array']) ? $this->json_to_string(json_encode($_POST['additional_devices_array'])) : $data_input['additional_devices_array'];
        $accessories = !empty($_POST['accessories']) ? $this->json_to_string_accessories(json_encode($_POST['accessories'])) : $data_input['accessories'];
        //Иное
        $marriage = $this->get_marriage_info($data_input['car_in_marriage'], $spouse_fio, true);
        $price_str = $this->num2str($_POST['price_car']);
        //Реквизиты
        //Продавец
//        $data_for_req_giver = array(); //данные продавца
//        $data_for_req_taker = array(); //данные покупателя
        switch ($_POST['type_of_giver'])
        {
            case 'physical':
                $data_for_req_giver = array
                (
                    'type_of_side' => $_POST['type_of_giver'],
                    'fio' => $vendor_fio,
                    'date' => $vendor_birthday,
                    'document_serial' => $_POST['vendor_passport_serial'],
                    'document_number' => $_POST['vendor_passport_number'],
                    'document_bywho' => $_POST['vendor_passport_bywho'],
                    'document_date' => $vendor_passport_date,
                    'adress' => $vendor_adress,
                    'phone' => $data_input['vendor_phone'],
                    'owner_car' => $_POST['vendor_is_owner_car'],
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_vendor_proxy_notary']
                );
                break;
            case 'law':
                $data_for_req_giver = array(
                    'type_of_side' => $_POST['type_of_giver'],
                    'name'=> $_POST['vendor_law_company_name'],
                    'inn'=> $_POST['vendor_law_inn'],
                    'ogrn'=> $_POST['vendor_law_ogrn'],
                    'adress'=> $_POST['vendor_law_adress'],
                    'phone'=> $data_input['vendor_law_phone'],
                    'acc'=> $_POST['vendor_law_acc'],
                    'bank_name'=> $_POST['vendor_law_bank_name'],
                    'korr_acc'=> $_POST['vendor_law_korr_acc'],
                    'bik'=> $_POST['vendor_law_bik'],
                    'owner_car'=> $_POST['vendor_is_owner_car'],
                    'agent_fio'=> $vendor_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_vendor_proxy_notary']
                );
                break;
            case 'individual':
                $data_for_req_giver = array(
                    'type_of_side' => $_POST['type_of_giver'],
                    'fio'=> $vendor_ind_fio,
                    'date'=> $vendor_ind_birthday,
                    'document_serial' => $_POST['vendor_ind_passport_serial'],
                    'document_number' => $_POST['vendor_ind_passport_number'],
                    'document_bywho' => $_POST['vendor_ind_passport_bywho'],
                    'document_date' => $vendor_ind_passport_date,
                    'adress'=> $vendor_ind_adress,
                    'phone'=> $data_input['vendor_ind_phone'],
                    'acc'=> $_POST['vendor_ind_bank_acc'],
                    'bank_name'=> $_POST['vendor_ind_bank_name'],
                    'korr_acc'=> $_POST['vendor_ind_korr_acc'],
                    'bik'=> $_POST['vendor_ind_bik'],
                    'owner_car' => $_POST['vendor_is_owner_car'],
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_vendor_proxy_notary'],
                    'number_of_certificate' => $_POST['vendor_ind_number_of_certificate'],
                    'date_of_certificate' => $vendor_ind_date_of_certificate,
                );
                break;
        }
        //Покупатель
        switch ($_POST['type_of_taker'])
        {
            case 'physical':
                $data_for_req_taker = array
                (
                    'type_of_side' => $_POST['type_of_taker'],
                    'fio' => $buyer_fio,
                    'date' => $buyer_birthday,
                    'document_serial' => $_POST['buyer_passport_serial'],
                    'document_number' => $_POST['buyer_passport_number'],
                    'document_bywho' => $_POST['buyer_passport_bywho'],
                    'document_date' => $buyer_passport_date,
                    'adress' => $buyer_adress,
                    'phone' => $data_input['buyer_phone'],
                    'owner_car' => $_POST['buyer_is_owner_car'],
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
                );
                break;
            case 'law':
                $data_for_req_taker = array(
                    'type_of_side' => $_POST['type_of_taker'],
                    'name'=> $_POST['buyer_law_company_name'],
                    'inn'=> $_POST['buyer_law_inn'],
                    'ogrn'=> $_POST['buyer_law_ogrn'],
                    'adress'=> $_POST['buyer_law_adress'],
                    'phone'=> $data_input['buyer_law_phone'],
                    'acc'=> $_POST['buyer_law_acc'],
                    'bank_name'=> $_POST['buyer_law_bank_name'],
                    'korr_acc'=> $_POST['buyer_law_korr_acc'],
                    'bik'=> $_POST['buyer_law_bik'],
                    'owner_car'=> $_POST['buyer_is_owner_car'],
                    'agent_fio'=> $buyer_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_buyer_proxy_notary']
                );
                break;
            case 'individual':
                $data_for_req_taker = array(
                    'type_of_side' => $_POST['type_of_taker'],
                    'fio'=> $buyer_ind_fio,
                    'date'=> $buyer_ind_birthday,
                    'document_serial' => $_POST['buyer_ind_passport_serial'],
                    'document_number' => $_POST['buyer_ind_passport_number'],
                    'document_bywho' => $_POST['buyer_ind_passport_bywho'],
                    'document_date' => $buyer_ind_passport_date,
                    'adress'=> $buyer_ind_adress,
                    'phone'=> $data_input['buyer_ind_phone'],
                    'acc'=> $_POST['buyer_ind_bank_acc'],
                    'bank_name'=> $_POST['buyer_ind_bank_name'],
                    'korr_acc'=> $_POST['buyer_ind_korr_acc'],
                    'bik'=> $_POST['buyer_ind_bik'],
                    'owner_car' => $_POST['buyer_is_owner_car'],
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
                    'number_of_certificate' => $_POST['buyer_ind_number_of_certificate'],
                    'date_of_certificate' => $buyer_ind_date_of_certificate,
                );
                break;
        }
        $firstside_requisites = $this->get_requisites($data_for_req_giver, true);
        $secondside_requisites = $this->get_requisites($data_for_req_taker, true);
        $data_for_header = array(
            'vendor_fio' =>$vendor_fio,
            'buyer_fio' =>$buyer_fio,
            'vendor_law_company_name' => $_POST['vendor_law_company_name'],
            'vendor_law_actor_position' => $_POST['vendor_law_actor_position'],
            'vendor_law_fio' =>$vendor_law_fio_parent,
            'vendor_law_document_osn' => $_POST['vendor_law_document_osn'],
            'vendor_law_proxy_number' => $_POST['vendor_law_proxy_number'],
            'vendor_law_proxy_date' => $vendor_law_proxy_date,
            'buyer_law_company_name' => $_POST['buyer_law_company_name'],
            'buyer_law_actor_position' => $_POST['buyer_law_actor_position'],
            'buyer_law_fio' =>$buyer_law_fio_parent,
            'buyer_law_document_osn' => $_POST['buyer_law_document_osn'],
            'buyer_law_proxy_number' => $_POST['buyer_law_proxy_number'],
            'buyer_law_proxy_date' => $buyer_law_proxy_date,
            'vendor_ind_fio' =>$vendor_ind_fio,
            'vendor_number_of_certificate' => $_POST['vendor_ind_number_of_certificate'],
            'vendor_date_of_certificate' => $vendor_ind_date_of_certificate,
            'buyer_ind_fio' =>$buyer_ind_fio,
            'buyer_number_of_certificate' => $_POST['buyer_ind_number_of_certificate'],
            'buyer_date_of_certificate' => $buyer_ind_date_of_certificate,
            'vendor_is_owner_car' => $data_input['vendor_is_owner_car'],
            'buyer_is_owner_car' => $data_input['buyer_is_owner_car'],
            'vendor_agent_fio' =>$vendor_agent_fio,
            'for_agent_vendor_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
            'for_agent_vendor_proxy_date' => $for_agent_vendor_proxy_date,
            'for_agent_vendor_proxy_notary' => $_POST['for_agent_vendor_proxy_notary'],
            'buyer_agent_fio' =>$buyer_agent_fio,
            'for_agent_buyer_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
            'for_agent_buyer_proxy_date' => $for_agent_buyer_proxy_date,
            'for_agent_buyer_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
            //Новые данные для реквизитов
            'agent_vendor_birthday' => $agent_vendor_birthday,
            'agent_vendor_pass_serial' => $_POST['agent_vendor_pass_serial'],
            'agent_vendor_pass_number' => $_POST['agent_vendor_pass_number'],
            'agent_vendor_pass_date' => $agent_vendor_pass_date,
            'agent_vendor_pass_bywho' => $_POST['agent_vendor_pass_bywho'],
            'agent_vendor_adress' => $agent_vendor_adress,
            'for_agent_proxy_birthday' => $for_agent_proxy_birthday,
            'for_agent_proxy_pass_serial' => $_POST['for_agent_proxy_pass_serial'],
            'for_agent_proxy_pass_number' => $_POST['for_agent_proxy_pass_number'],
            'for_agent_proxy_pass_bywho' => $_POST['for_agent_proxy_pass_bywho'],
            'for_agent_proxy_pass_date' => $for_agent_proxy_pass_date,
            'for_agent_proxy_adress' => $for_agent_proxy_adress,
            //Ещё правки
            'vendor_birthday' => $vendor_birthday,
            'buyer_birthday' => $buyer_birthday,
            'vendor_ind_birthday' => $vendor_ind_birthday,
            'buyer_ind_birthday' => $buyer_ind_birthday,
            'vendor_passport_serial' => $_POST['vendor_passport_serial'],
            'vendor_passport_number' => $_POST['vendor_passport_number'],
            'vendor_passport_bywho' => $_POST['vendor_passport_bywho'],
            'vendor_passport_date' => $vendor_passport_date,
            'vendor_adress' => $vendor_adress,
            'vendor_ind_passport_serial' => $_POST['vendor_ind_passport_serial'],
            'vendor_ind_passport_number' => $_POST['vendor_ind_passport_number'],
            'vendor_ind_passport_bywho' => $_POST['vendor_ind_passport_bywho'],
            'vendor_ind_passport_date' => $vendor_ind_passport_date,
            'vendor_ind_adress'=> $vendor_ind_adress,
            'buyer_passport_serial'=> $_POST['buyer_passport_serial'],
            'buyer_passport_number'=> $_POST['buyer_passport_number'],
            'buyer_passport_bywho'=> $_POST['buyer_passport_bywho'],
            'buyer_passport_date'=> $buyer_passport_date,
            'buyer_adress'=> $buyer_adress,
            'buyer_ind_passport_serial' => $_POST['buyer_ind_passport_serial'],
            'buyer_ind_passport_number' => $_POST['buyer_ind_passport_number'],
            'buyer_ind_passport_bywho' => $_POST['buyer_ind_passport_bywho'],
            'buyer_ind_passport_date' => $buyer_ind_passport_date,
            'buyer_ind_adress'=> $buyer_ind_adress,
            'vendor_fio_parent'=> $vendor_fio_parent,
            'buyer_fio_parent'=> $buyer_fio_parent,
            'vendor_ind_fio_parent'=> $vendor_ind_fio_parent,
            'buyer_ind_fio_parent'=> $buyer_ind_fio_parent,
            //
            'vendor_agent_fio_parent'=> $vendor_agent_fio_parent,
            'buyer_agent_fio_parent'=> $buyer_agent_fio_parent,
        );
        $header_doc = $this->set_header_doc($data_input['type_of_contract'], $data_input['type_of_giver'], $data_input['type_of_taker'], $data_for_header, true);
        //Сроки подписания договора
        if ($_POST['payment_date']== 'в рассрочку по следующему графику')
        {
            $credit = ": аванс в сумме {$_POST['credit']}(". $this->num2str($_POST['credit']).") {$_POST['credit_currency']} оплачен покупателем при подписании настоящего договора, оставшуюся часть денег покупатель обязуется оплатить до $credit_date ";
        }
        else
        {
            $credit = '';
        }
        //Массив данных для канванса
        $data = array
        (
            0 => array
            (
                'text' => '^2/ДОГОВОР'

            ),
            1 => array
            (
                'text' => '^2/КУПЛИ - ПРОДАЖИ ТРАНСПОРТНОГО СРЕДСТВА',
                'text-type' => 'title'
            ),
            2 => array
            (
                'text' => "^3/г.{$data_input['place_of_contract']} ^3*$date_of_contract ",
                'text-type' => 'columns-left'
            ),
            3 => array
            (
                'text' => "^4/$header_doc",
                'text-type' => 'paragraph'
            ),
            4 => array
            (
                'text' => '^2/1. Предмет Договора',
                'text-type' => 'title'
            ),
            5 => array
            (
                'text' => "^4/ 1.1. Продавец обязуется передать в собственность Покупателя, а Покупатель обязуется принять и оплатить следующее транспортное средство (далее - транспортное средство):",
                'text-type' => 'paragraph',
            ),
            6 => array
            (
                'text' => "^5/- марка, модель: {$data_input['mark']};",
                'text-type' => 'list',
            ),
            7 => array
            (
                'text' => "^5/- идентификационный номер (VIN): {$data_input['vin']};",
                'text-type' => 'list',
            ),
            8 => array
            (
                'text' => "^5/- государственный регистрационный знак: {$data_input['reg_gov_number']};",
                'text-type' => 'list',
            ),
            9 => array
            (
                'text' => "^5/- наименование (тип): {$data_input['car_type']};",
                'text-type' => 'list',
            ),
            10 => array
            (
                'text' => "^5/- категория (А, В, С, D, М, прицеп): {$data_input['category']};",
                'text-type' => 'list',
            ),
            11 => array
            (
                'text' => "^5/- год изготовления: $date_of_product;",
                'text-type' => 'list',
            ),
            12 => array
            (
                'text' => "^5/- модель, N двигателя: {$data_input['engine_model']};",
                'text-type' => 'list',
            ),
            13 => array
            (
                'text' => "^5/- шасси (рама) N: {$data_input['shassi']};",
                'text-type' => 'list',
            ),
            14 => array
            (
                'text' => "^5/- кузов (кабина, прицеп) N: {$data_input['carcass']};",
                'text-type' => 'list',
            ),
            15 => array
            (
                'text' => "^5/- цвет кузова (кабины, прицепа): {$data_input['color_carcass']};",
                'text-type' => 'list',
            ),
            16 => array
            (
                'text' => "^5/- иные индивидуализирующие признаки (голограммы, рисунки и т.д.): {$data_input['other_parameters']}",
                'text-type' => 'list',
            ),
            17 => array
            (
                'text' => "^4/1.2. Продавец обязуется передать Покупателю транспортное средство, оснащенное серийным оборудованием и комплектующими изделиями, установленными заводом-изготовителем, а также следующим дополнительным оборудованием: $additional_devices_array",
                'text-type' => 'paragraph',
            ),
            18 => array
            (
                'text' => "^4/1.3. Транспортное средство, отчуждаемое по настоящему договору принадлежит Продавцу на праве собственности, что подтверждается паспортом транспортного средства (ПТС)  серии {$data_input['serial_car']} N {$data_input['number_of_serial_car']}, выданного {$data_input['bywho_serial_car']} $date_of_serial_car",
                'text-type' => 'paragraph',
            ),
            19 => array
            (
                'text' => "^4/1.4. Продавец гарантирует, что передаваемое транспортное средство в споре или под арестом не состоит, не является предметом залога и не обременено другими правами третьих лиц, в розыске не находится.",
                'text-type' => 'paragraph',
            ),
            20 => array
            (
                'text' => "^4/Продавец гарантирует, что не заключал с иными лицами договоров реализации транспортного средства.",
                'text-type' => 'paragraph',
            ),
            21 => array
            (
                'text' => "^2/2. Качество транспортного средства",
                'text-type' => 'title',
            ),
            22 => array
            (
                'text' => "^4/2.1. Общее состояние транспортного средства: {$data_input['car_allstatus']}.",
                'text-type' => 'paragraph',
            ),
            23 => array
            (
                'text' => "^4/2.2. Последнее техническое обслуживание транспортного средства проведено $maintenance_date {$data_input['maintenance_bywho']}.",
                'text-type' => 'paragraph',
            ),
            24 => array
            (
                'text' => "^4/2.3. Повреждения и эксплуатационные дефекты",
                'text-type' => 'paragraph',
            ),
            25 => array
            (
                'text' => "^4/2.3.1. В период владения Продавцом транспортное средство получило следующие механические повреждения и эксплуатационные дефекты: {$data_input['defects']}",
                'text-type' => 'paragraph',
            ),
            26 => array
            (
                'text' => "^4/2.3.2. Транспортное средство передается Покупателю со следующими неустраненными повреждениями и эксплуатационными дефектами: {$data_input['defects']}",
                'text-type' => 'paragraph',
            ),
            27 => array
            (
                'text' => "^4/2.4. Транспортное средство имеет следующие особенности, которые не влияют на безопасность товара и не являются недостатками: {$data_input['features']}.",
                'text-type' => 'paragraph',
            ),
            28 => array
            (
                'text' => "^2/3. Цена, срок и порядок оплаты",
                'text-type' => 'title',
            ),
            29 => array
            (
                'text' => "^4/3.1. По соглашению Сторон цена транспортного средства составляет {$data_input['price_car']} ($price_str) {$data_input['currency']}.",
                'text-type' => 'paragraph',
            ),
            30 => array
            (
                'text' => "^4/3.2. Стоимость указанных в Договоре инструментов и принадлежностей, а также дополнительно установленного оборудования включена в цену транспортного средства.",
                'text-type' => 'paragraph',
            ),
            31 => array
            (
                'text' => "^4/3.3. Покупатель оплачивает стоимость транспортного средства путем передачи наличных денег продавцу {$data_input['payment_date']}$credit. При получении денежных средств Продавец в соответствии с п. 2 ст. 408 ГК РФ выдает расписку.",
                'text-type' => 'paragraph',
            ),
            32 => array
            (
                'text' => "^4/3.4. Цена транспортного средства не включает расходы, связанные с оформлением Договора. Такие расходы Покупатель несет дополнительно.",
                'text-type' => 'paragraph',
            ),
            33 => array
            (
                'text' => "^2/4. Срок и условия передачи транспортного средства",
                'text-type' => 'title',
            ),
            34 => array
            (
                'text' => "^4/4.1. Продавец передает Покупателю соответствующее условиям Договора транспортное средство со всеми принадлежностями после исполнения Покупателем обязанности по оплате.",
                'text-type' => 'paragraph',
            ),
            35 => array
            (
                'text' => "^4/4.2. Одновременно с передачей транспортного средства Продавец передает Покупателю следующие документы:",
                'text-type' => 'paragraph',
             ),
            36 => array
            (
                'text' => "^5/- паспорт транспортного средства серия {$data_input['serial_car']} N {$data_input['number_of_serial_car']}, дата выдачи $date_of_serial_car, с подписью Продавца в графе \"Подпись прежнего собственника\";$documents",
                'text-type' => 'list',
            ),
            37 => array
            (
                'text' => "^4/4.3. Одновременно с передачей транспортного средства Продавец передает Покупателю следующие инструменты и принадлежности: {$accessories} {$marriage['info']}",
                'text-type' => 'paragraph',
            ),
            38 => array
            (
                'text' => "^4/4.{$marriage['number']}. Право собственности на транспортное средство, а также риск его случайной гибели и случайного повреждения переходит к Покупателю в момент передачи транспортного средства.",
                'text-type' => 'paragraph',
            ),
            39 => array
            (
                'text' => "^2/5. Приемка транспортного средства",
                'text-type' => 'title',
            ),
            40 => array
            (
                'text' => "^4/5.1. Приемка транспортного средства осуществляется в месте его передачи Покупателю. Во время приемки производятся идентификация, осмотр и проверка транспортного средства по качеству и комплектности.",
                'text-type' => 'paragraph',
            ),
            41 => array
            (
                'text' => "^4/5.2. Покупатель проверяет наличие документов на транспортное средство.",
                'text-type' => 'paragraph',
            ),
            42 => array
            (
                'text' => "^4/5.3. Идентификация транспортного средства заключается в проверке соответствия фактических данных сведениям, содержащимся в ПТС.",
                'text-type' => 'paragraph',
            ),
            43 => array
            (
                'text' => "^4/5.4. Осмотр транспортного средства должен проводиться в светлое время суток либо при искусственном освещении, позволяющем провести такой осмотр.",
                'text-type' => 'paragraph',
            ),
            44 => array
            (
                'text' => "^4/5.5. Все обнаруженные при приемке недостатки, в том числе по комплектности, заносятся в акт приема-передачи транспортного средства.",
                'text-type' => 'paragraph',
            ),
            45 => array
            (
                'text' => "^4/5.6. Покупатель обязан в течение 10 (десяти) суток после подписания акта приема-передачи транспортного средства изменить регистрационные данные о собственнике транспортного средства, обратившись с соответствующим заявлением в регистрационное подразделение ГИБДД.",
                'text-type' => 'paragraph',
            ),
            46 => array
            (
                'text' => "^4/5.7. В случае подачи заявления в регистрирующий орган о сохранении регистрационных знаков, Продавец должен сообщить об этом Покупателю в день подачи заявления.",
                'text-type' => 'paragraph',
            ),
            47 => array
            (
                'text' => "^2/6. Ответственность Сторон",
                'text-type' => 'title',
            ),
            48 => array
            (
                'text' => "^4/6.1. За нарушение сроков оплаты стоимости транспортного средства Продавец вправе требовать с Покупателя уплаты неустойки (пеней) в размере {$data_input['penalty']} процента от неуплаченной суммы за каждый день просрочки.",
                'text-type' => 'paragraph',
            ),
            49 => array
            (
                'text' => "^4/6.2. За нарушение сроков передачи транспортного средства Покупатель вправе требовать с Продавца уплаты неустойки (пеней) в размере {$data_input['penalty']} процента от стоимости транспортного средства за каждый день просрочки.",
                'text-type' => 'paragraph',
            ),
            50 => array
            (
                'text' => "^4/6.3. При нарушении предусмотренных Договором гарантий Продавца Покупатель вправе требовать с Продавца уплаты неустойки (штрафа) в размере {$data_input['penalty']} процентов от установленной Договором цены транспортного средства.",
                'text-type' => 'paragraph',
            ),
            51 => array
            (
                'text' => "^4/6.4. При изъятии транспортного средства у Покупателя третьими лицами по основаниям, возникшим до исполнения Договора, Продавец обязан возместить Покупателю понесенные им убытки.",
                'text-type' => 'paragraph',
            ),
            52 => array
            (
                'text' => "^2/7. Расторжение Договора",
                'text-type' => 'title',
            ),
            53 => array
            (
                'text' => "^4/7.1. Договор может быть расторгнут по требованию Покупателя в судебном порядке в случае выявления после подписания акта приема-передачи транспортного средства хотя бы одного из следующих фактов:",
                'text-type' => 'paragraph',
            ),
            54 => array
            (
                'text' => "^5/- обнаружены дефекты и повреждения, не отраженные в Договоре (скрытые дефекты), которые не позволяют в дальнейшем эксплуатировать транспортное средство в соответствии с его назначением;",
                'text-type' => 'list',
            ),
            55 => array
            (
                'text' => "^5/- в период владения Продавцом проведен не оговоренный в Договоре ремонт транспортного средства в связи с повреждением в результате дорожно-транспортных происшествий, а также иных событий, которые ухудшают дальнейшую эксплуатацию транспортного средства.",
                'text-type' => 'list',
            ),
            56 => array
            (
                'text' => "^2/8. Заключительные положения",
                'text-type' => 'title',
            ),
            57 => array
            (
                'text' => "^4/8.1. Договор вступает в силу с момента его подписания и действует до исполнения Сторонами своих обязательств.",
                'text-type' => 'paragraph',
            ),
            58 => array
            (
                'text' => "^4/8.2. Договор составлен в 3 (трех) экземплярах, имеющих равную юридическую силу, по одному для каждой Стороны и один для регистрирующего органа.",
                'text-type' => 'paragraph',
            ),
            59 => array
            (
                'text' => "^2/9. Адреса и реквизиты Сторон",
                'text-type' => 'paragraph',
            ),
            60 => array
            (
                'text' => "^6/Продавец: $firstside_requisites ^+______________ Подпись ^6*Покупатель:  $secondside_requisites ^+______________ Подпись ",
                'text-type' => 'columns-left',
            ),

        );
        $data = json_encode($data);
        echo $data;
        return true;

    }
    //------------------------------------------------------------------------------------------------------------------
    public function get_data_for_canvas_gift()
    {
        //Подготовка данных
        strip_tags($_POST);
        include 'array_for_gift_canvans.php';
        foreach ($_POST as $key => $value)
        {
            if(!empty($_POST["$key"]))
                $data_input["$key"] = $value;
        }
        //ФИО
        $vendor_fio = $this->format_fio($data_input['vendor_surname'], $data_input['vendor_name'], $data_input['vendor_patronymic']);
        $buyer_fio = $this->format_fio($data_input['buyer_surname'],$data_input['buyer_name'],$data_input['buyer_patronymic']);
        $vendor_law_fio_parent = $this->format_fio($_POST['vendor_law_actor_surname_parent'],$_POST['vendor_law_actor_name_parent'],$_POST['vendor_law_actor_patronymic_parent']);
//        $vendor_law_fio = $this->format_fio($_POST['vendor_law_surname'],$_POST['vendor_law_name'],$_POST['vendor_law_patronymic']);
        $buyer_law_fio_parent = $this->format_fio($_POST['buyer_law_actor_surname_parent'],$_POST['buyer_law_actor_name_parent'],$_POST['buyer_law_actor_patronymic_parent']);
//        $buyer_law_fio = $this->format_fio($_POST['buyer_law_surname'],$_POST['buyer_law_name'],$_POST['buyer_law_patronymic']);
        $vendor_ind_fio = $this->format_fio($_POST['vendor_ind_surname'],$_POST['vendor_ind_name'],$_POST['vendor_ind_patronymic']);
        $buyer_ind_fio = $this->format_fio($_POST['buyer_ind_surname'],$_POST['buyer_ind_name'],$_POST['buyer_ind_patronymic']);
        $vendor_agent_fio = $this->format_fio($_POST['for_agent_vendor_surname'],$_POST['for_agent_vendor_name'],$_POST['for_agent_vendor_patronymic']);
        $buyer_agent_fio = $this->format_fio($_POST['for_agent_buyer_surname'],$_POST['for_agent_buyer_name'],$_POST['for_agent_buyer_patronymic']);
        //Родительское фио
        $vendor_law_fio_parent = $this->format_fio($_POST['vendor_law_actor_surname_parent'],$_POST['vendor_law_actor_name_parent'],$_POST['vendor_law_actor_patronymic_parent']);
        $buyer_law_fio_parent = $this->format_fio($_POST['buyer_law_actor_surname_parent'],$_POST['buyer_law_actor_name_parent'],$_POST['buyer_law_actor_patronymic_parent']);
        $vendor_fio_parent = $this->format_fio($_POST['vendor_surname_parent'], $_POST['vendor_name_parent'], $_POST['vendor_patronymic_parent']);
        $buyer_fio_parent = $this->format_fio($_POST['buyer_surname_parent'],$_POST['buyer_name_parent'],$_POST['buyer_patronymic_parent']);
        $vendor_ind_fio_parent = $this->format_fio($_POST['vendor_ind_surname_parent'],$_POST['vendor_ind_name_parent'],$_POST['vendor_ind_patronymic_parent']);
        $buyer_ind_fio_parent = $this->format_fio($_POST['buyer_ind_surname_parent'],$_POST['buyer_ind_name_parent'],$_POST['buyer_ind_patronymic_parent']);
        $vendor_agent_fio_parent = $this->format_fio($_POST['for_agent_vendor_surname_parent'],$_POST['for_agent_vendor_name_parent'],$_POST['for_agent_vendor_patronymic_parent']);
        $buyer_agent_fio_parent = $this->format_fio($_POST['for_agent_buyer_surname_parent'],$_POST['for_agent_buyer_name_parent'],$_POST['for_agent_buyer_patronymic_parent']);
        //Адрес
        $vendor_adress = $this->format_adress($_POST['vendor_city'],$_POST['vendor_street'],$_POST['vendor_house'],$_POST['vendor_flat']);
        $buyer_adress = $this->format_adress($_POST['buyer_city'],$_POST['buyer_street'],$_POST['buyer_house'],$_POST['buyer_flat']);
        $vendor_ind_adress = $this->format_adress($_POST['vendor_ind_city'],$_POST['vendor_ind_street'],$_POST['vendor_ind_house'],$_POST['vendor_ind_flat']);
        $buyer_ind_adress = $this->format_adress($_POST['buyer_ind_city'],$_POST['buyer_ind_street'],$_POST['buyer_ind_house'],$_POST['buyer_ind_flat']);
        //Новые адресса
        $agent_vendor_adress = $this->format_adress($_POST['agent_vendor_city'],$_POST['agent_vendor_street'],$_POST['agent_vendor_house'],$_POST['agent_vendor_flat']);
        $for_agent_proxy_adress = $this->format_adress($_POST['for_agent_proxy_city'],$_POST['for_agent_proxy_street'],$_POST['for_agent_proxy_house'],$_POST['for_agent_proxy_flat']);

        //Дата
        $date_of_contract = !empty($_POST['date_of_contract']) ? $this->format_date($_POST['date_of_contract']) : $data_input['date_of_contract'];
        $date_of_product = !empty($_POST['date_of_product']) ? $_POST['date_of_product'] : $data_input['date_of_product'];
        $vendor_birthday = $this->format_date($_POST['vendor_birthday'], true);
        $vendor_passport_date = $this->format_date($_POST['vendor_passport_date']);
        $buyer_passport_date = $this->format_date($_POST['buyer_passport_date']);
        $buyer_birthday = $this->format_date($_POST['buyer_birthday'], true);
        $vendor_ind_birthday= $this->format_date($_POST['vendor_ind_birthday'], true);
        $vendor_ind_passport_date= $this->format_date($_POST['vendor_ind_passport_date'], true);
        $for_agent_buyer_proxy_date = $this->format_date($_POST['for_agent_buyer_proxy_date']);
//        $payment_date = $this->format_date($_POST['payment_date']);
        $date_of_serial_car = !empty($_POST['date_of_serial_car']) ? $this->format_date($_POST['date_of_serial_car']) : $data_input['date_of_serial_car'];
        $for_agent_vendor_proxy_date = $this->format_date($_POST['for_agent_vendor_proxy_date']);
        $buyer_ind_birthday = $this->format_date($_POST['buyer_ind_birthday'], true);
        $buyer_ind_passport_date = $this->format_date($_POST['buyer_ind_passport_date']);
        $vendor_law_proxy_date = $this->format_date($_POST['$vendor_law_proxy_date']);
        $buyer_law_proxy_date =  $this->format_date($_POST['$buyer_law_proxy_date']);
        $vendor_ind_date_of_certificate =  $this->format_date($_POST['$vendor_ind_date_of_certificate']);
        $buyer_ind_date_of_certificate =  $this->format_date($_POST['$buyer_ind_date_of_certificate']);
        //Новая дата
        $agent_vendor_birthday = $this->format_date($_POST['agent_vendor_birthday'], true);
        $agent_vendor_pass_date = $this->format_date($_POST['agent_vendor_pass_date']);
        $for_agent_proxy_birthday = $this->format_date($_POST['for_agent_proxy_birthday'], true);
        $for_agent_proxy_pass_date = $this->format_date($_POST['for_agent_proxy_pass_date']);

        //Джсон
        $other_parameters = $this->json_to_string($_POST['other_parameters']);
        //Продавец
//        $data_for_req_giver = array(); //данные продавца
//        $data_for_req_taker = array(); //данные покупателя
        switch ($_POST['type_of_giver'])
        {
            case 'physical':
                $data_for_req_giver = array
                (
                    'type_of_side' => $_POST['type_of_giver'],
                    'fio' => $vendor_fio,
                    'date' => $vendor_birthday,
                    'document_serial' => $_POST['vendor_passport_serial'],
                    'document_number' => $_POST['vendor_passport_number'],
                    'document_bywho' => $_POST['vendor_passport_bywho'],
                    'document_date' => $vendor_passport_date,
                    'adress' => $vendor_adress,
                    'phone' => $data_input['vendor_phone'],
                    'owner_car' => $_POST['vendor_is_owner_car'],
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_vendor_proxy_notary']
                );
                break;
            case 'law':
                $data_for_req_giver = array(
                    'type_of_side' => $_POST['type_of_giver'],
                    'name'=> $_POST['vendor_law_company_name'],
                    'inn'=> $_POST['vendor_law_inn'],
                    'ogrn'=> $_POST['vendor_law_ogrn'],
                    'adress'=> $_POST['vendor_law_adress'],
                    'phone'=> $data_input['vendor_law_phone'],
                    'acc'=> $_POST['vendor_law_acc'],
                    'bank_name'=> $_POST['vendor_law_bank_name'],
                    'korr_acc'=> $_POST['vendor_law_korr_acc'],
                    'bik'=> $_POST['vendor_law_bik'],
                    'owner_car'=> $_POST['vendor_is_owner_car'],
                    'agent_fio'=> $vendor_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_vendor_proxy_notary']
                );
                break;
            case 'individual':
                $data_for_req_giver = array(
                    'type_of_side' => $_POST['type_of_giver'],
                    'fio'=> $vendor_ind_fio,
                    'date'=> $vendor_ind_birthday,
                    'document_serial' => $_POST['vendor_ind_passport_serial'],
                    'document_number' => $_POST['vendor_ind_passport_number'],
                    'document_bywho' => $_POST['vendor_ind_passport_bywho'],
                    'document_date' => $vendor_ind_passport_date,
                    'adress'=> $vendor_ind_adress,
                    'phone'=> $data_input['vendor_ind_phone'],
                    'acc'=> $_POST['vendor_ind_bank_acc'],
                    'bank_name'=> $_POST['vendor_ind_bank_name'],
                    'korr_acc'=> $_POST['vendor_ind_korr_acc'],
                    'bik'=> $_POST['vendor_ind_bik'],
                    'owner_car' => $_POST['vendor_is_owner_car'],
                    'agent_fio' => $vendor_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
                    'agent_proxy_date' => $for_agent_vendor_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_vendor_proxy_notary'],
                    'number_of_certificate' => $_POST['vendor_ind_number_of_certificate'],
                    'date_of_certificate' => $vendor_ind_date_of_certificate,
                );
                break;
        }
        //Покупатель
        switch ($_POST['type_of_taker'])
        {
            case 'physical':
                $data_for_req_taker = array
                (
                    'type_of_side' => $_POST['type_of_taker'],
                    'fio' => $buyer_fio,
                    'date' => $buyer_birthday,
                    'document_serial' => $_POST['buyer_passport_serial'],
                    'document_number' => $_POST['buyer_passport_number'],
                    'document_bywho' => $_POST['buyer_passport_bywho'],
                    'document_date' => $buyer_passport_date,
                    'adress' => $buyer_adress,
                    'phone' => $data_input['buyer_phone'],
                    'owner_car' => $_POST['buyer_is_owner_car'],
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
                );
                break;
            case 'law':
                $data_for_req_taker = array(
                    'type_of_side' => $_POST['type_of_taker'],
                    'name'=> $_POST['buyer_law_company_name'],
                    'inn'=> $_POST['buyer_law_inn'],
                    'ogrn'=> $_POST['buyer_law_ogrn'],
                    'adress'=> $_POST['buyer_law_adress'],
                    'phone'=> $data_input['buyer_law_phone'],
                    'acc'=> $_POST['buyer_law_acc'],
                    'bank_name'=> $_POST['buyer_law_bank_name'],
                    'korr_acc'=> $_POST['buyer_law_korr_acc'],
                    'bik'=> $_POST['buyer_law_bik'],
                    'owner_car'=> $_POST['buyer_is_owner_car'],
                    'agent_fio'=> $buyer_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_buyer_proxy_notary']
                );
                break;
            case 'individual':
                $data_for_req_taker = array(
                    'type_of_side' => $_POST['type_of_taker'],
                    'fio'=> $buyer_ind_fio,
                    'date'=> $buyer_ind_birthday,
                    'document_serial' => $_POST['buyer_ind_passport_serial'],
                    'document_number' => $_POST['buyer_ind_passport_number'],
                    'document_bywho' => $_POST['buyer_ind_passport_bywho'],
                    'document_date' => $buyer_ind_passport_date,
                    'adress'=> $buyer_ind_adress,
                    'phone'=> $data_input['buyer_ind_phone'],
                    'acc'=> $_POST['buyer_ind_bank_acc'],
                    'bank_name'=> $_POST['buyer_ind_bank_name'],
                    'korr_acc'=> $_POST['buyer_ind_korr_acc'],
                    'bik'=> $_POST['buyer_ind_bik'],
                    'owner_car' => $_POST['buyer_is_owner_car'],
                    'agent_fio' => $buyer_agent_fio,
                    'agent_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
                    'agent_proxy_date' => $for_agent_buyer_proxy_date,
                    'agent_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
                    'number_of_certificate' => $_POST['buyer_ind_number_of_certificate'],
                    'date_of_certificate' => $buyer_ind_date_of_certificate,
                );
                break;
        }
        $firstside_requisites = $this->get_requisites($data_for_req_giver, true);
        $secondside_requisites = $this->get_requisites($data_for_req_taker, true);
        $data_for_header = array(
            'vendor_fio' =>$vendor_fio,
            'buyer_fio' =>$buyer_fio,
            'vendor_law_company_name' => $_POST['vendor_law_company_name'],
            'vendor_law_actor_position' => $_POST['vendor_law_actor_position'],
            'vendor_law_fio' =>$vendor_law_fio_parent,
            'vendor_law_document_osn' => $_POST['vendor_law_document_osn'],
            'vendor_law_proxy_number' => $_POST['vendor_law_proxy_number'],
            'vendor_law_proxy_date' => $vendor_law_proxy_date,
            'buyer_law_company_name' => $_POST['buyer_law_company_name'],
            'buyer_law_actor_position' => $_POST['buyer_law_actor_position'],
            'buyer_law_fio' =>$buyer_law_fio_parent,
            'buyer_law_document_osn' => $_POST['buyer_law_document_osn'],
            'buyer_law_proxy_number' => $_POST['buyer_law_proxy_number'],
            'buyer_law_proxy_date' => $buyer_law_proxy_date,
            'vendor_ind_fio' =>$vendor_ind_fio,
            'vendor_number_of_certificate' => $_POST['vendor_ind_number_of_certificate'],
            'vendor_date_of_certificate' => $vendor_ind_date_of_certificate,
            'buyer_ind_fio' =>$buyer_ind_fio,
            'buyer_number_of_certificate' => $_POST['buyer_ind_number_of_certificate'],
            'buyer_date_of_certificate' => $buyer_ind_date_of_certificate,
            'vendor_is_owner_car' => $data_input['vendor_is_owner_car'],
            'buyer_is_owner_car' => $data_input['buyer_is_owner_car'],
            'vendor_agent_fio' =>$vendor_agent_fio,
            'for_agent_vendor_proxy_number' => $_POST['for_agent_vendor_proxy_number'],
            'for_agent_vendor_proxy_date' => $for_agent_vendor_proxy_date,
            'for_agent_vendor_proxy_notary' => $_POST['for_agent_vendor_proxy_notary'],
            'buyer_agent_fio' =>$buyer_agent_fio,
            'for_agent_buyer_proxy_number' => $_POST['for_agent_buyer_proxy_number'],
            'for_agent_buyer_proxy_date' => $for_agent_buyer_proxy_date,
            'for_agent_buyer_proxy_notary' => $_POST['for_agent_buyer_proxy_notary'],
            //Новые данные для реквизитов
            'agent_vendor_birthday' => $agent_vendor_birthday,
            'agent_vendor_pass_serial' => $_POST['agent_vendor_pass_serial'],
            'agent_vendor_pass_number' => $_POST['agent_vendor_pass_number'],
            'agent_vendor_pass_date' => $agent_vendor_pass_date,
            'agent_vendor_pass_bywho' => $_POST['agent_vendor_pass_bywho'],
            'agent_vendor_adress' => $agent_vendor_adress,
            'for_agent_proxy_birthday' => $for_agent_proxy_birthday,
            'for_agent_proxy_pass_serial' => $_POST['for_agent_proxy_pass_serial'],
            'for_agent_proxy_pass_number' => $_POST['for_agent_proxy_pass_number'],
            'for_agent_proxy_pass_bywho' => $_POST['for_agent_proxy_pass_bywho'],
            'for_agent_proxy_pass_date' => $for_agent_proxy_pass_date,
            'for_agent_proxy_adress' => $for_agent_proxy_adress,
            //Ещё правки
            'vendor_birthday' => $vendor_birthday,
            'buyer_birthday' => $buyer_birthday,
            'vendor_ind_birthday' => $vendor_ind_birthday,
            'buyer_ind_birthday' => $buyer_ind_birthday,
            'vendor_passport_serial' => $_POST['vendor_passport_serial'],
            'vendor_passport_number' => $_POST['vendor_passport_number'],
            'vendor_passport_bywho' => $_POST['vendor_passport_bywho'],
            'vendor_passport_date' => $vendor_passport_date,
            'vendor_adress' => $vendor_adress,
            'vendor_ind_passport_serial' => $_POST['vendor_ind_passport_serial'],
            'vendor_ind_passport_number' => $_POST['vendor_ind_passport_number'],
            'vendor_ind_passport_bywho' => $_POST['vendor_ind_passport_bywho'],
            'vendor_ind_passport_date' => $vendor_ind_passport_date,
            'vendor_ind_adress'=> $vendor_ind_adress,
            'buyer_passport_serial'=> $_POST['buyer_passport_serial'],
            'buyer_passport_number'=> $_POST['buyer_passport_number'],
            'buyer_passport_bywho'=> $_POST['buyer_passport_bywho'],
            'buyer_passport_date'=> $buyer_passport_date,
            'buyer_adress'=> $buyer_adress,
            'buyer_ind_passport_serial' => $_POST['buyer_ind_passport_serial'],
            'buyer_ind_passport_number' => $_POST['buyer_ind_passport_number'],
            'buyer_ind_passport_bywho' => $_POST['buyer_ind_passport_bywho'],
            'buyer_ind_passport_date' => $buyer_ind_passport_date,
            'buyer_ind_adress'=> $buyer_ind_adress,
            'vendor_fio_parent'=> $vendor_fio_parent,
            'buyer_fio_parent'=> $buyer_fio_parent,
            'vendor_ind_fio_parent'=> $vendor_ind_fio_parent,
            'buyer_ind_fio_parent'=> $buyer_ind_fio_parent,
            //
            'vendor_agent_fio_parent'=> $vendor_agent_fio_parent,
            'buyer_agent_fio_parent'=> $buyer_agent_fio_parent,
        );
        $header_doc = $this->set_header_doc($data_input['type_of_contract'], $data_input['type_of_giver'], $data_input['type_of_taker'], $data_for_header, true);
        //Массив данных для канванса
        $data = array
        (
            0 => array
            (
                'text' => '^2/ДОГОВОР',
                'text-type' => 'title'

            ),
            1 => array
            (
                'text' => '^2/ДАРЕНИЯ ТРАНСПОРТНОГО СРЕДСТВА',
                'text-type' => 'title'
            ),
            2 => array
            (
                'text' => "^3/г.{$data_input['place_of_contract']} ^3*$date_of_contract",
                'text-type' => 'columns'
            ),
            3 => array
            (
                'text' => "^4/$header_doc",
                'text-type' => 'paragraph'
            ),
            4 => array
            (
                'text' => "^4/1. В соответствии с настоящим Договором Даритель безвозмездно передает Одаряемому в собственность автомобиль:",
                'text-type' => 'paragraph',
            ),
            5 => array
            (
                'text' => "^5/- марка, модель: {$data_input['mark']};",
                'text-type' => 'list',
            ),
            6 => array
            (
                'text' => "^5/- идентификационный номер (VIN): {$data_input['vin']};",
                'text-type' => 'list',
            ),
            7 => array
            (
                'text' => "^5/- государственный регистрационный знак: {$data_input['reg_gov_number']};",
                'text-type' => 'list',
            ),
            8 => array
            (
                'text' => "^5/- наименование (тип): {$data_input['car_type']};",
                'text-type' => 'list',
            ),
            9 => array
            (
                'text' => "^5/- категория (А, В, С, D, М, прицеп): {$data_input['category']};",
                'text-type' => 'list',
            ),
            10 => array
            (
                'text' => "^5/- год изготовления: $date_of_product;",
                'text-type' => 'list',
            ),
            11 => array
            (
                'text' => "^5/- модель, N двигателя: {$data_input['engine_model']};",
                'text-type' => 'list',
            ),
            12 => array
            (
                'text' => "^5/- шасси (рама) N: {$data_input['shassi']};",
                'text-type' => 'list',
            ),
            13 => array
            (
                'text' => "^5/- кузов (кабина, прицеп) N: {$data_input['carcass']};",
                'text-type' => 'list',
            ),
            14 => array
            (
                'text' => "^5/- цвет кузова (кабины, прицепа): {$data_input['color_carcass']};",
                'text-type' => 'list',
            ),
            17 => array
            (
                'text' => "^4/2. Одаряемый принимает в дар от Дарителя Автомобиль, указанный в п.1 настоящего Договора, на условиях, согласованных в данном Договоре.",
                'text-type' => 'paragraph',
            ),
            18 => array
            (
                'text' => "^4/3. Принадлежность передаваемого по настоящему Договору Автомобиля Дарителю подтверждается паспортом транспортного средства серии {$data_input['serial_car']} № {$data_input['number_of_serial_car']} выданного {$data_input['bywho_serial_car']} $date_of_serial_car ",
                'text-type' => 'paragraph',
            ),
            19 => array
            (
                'text' => "^4/4. Передача Автомобиля производится в момент подписания настоящего договора, без составления передаточного акта.",
                'text-type' => 'paragraph',
            ),
            20 => array
            (
                'text' => "^4/5. Автомобиль передается в состоянии, пригодном для его использования в соответствии с целевым назначением. Одновременно с передачей Автомобиля Даритель  передает Одаряемому техническую и иную документацию, необходимую для надлежащего владения и пользования Автомобилем.",
                'text-type' => 'paragraph',
            ),
            21 => array
            (
                'text' => "^4/6. До заключения настоящего договора автомобиль, указанный в п.1 настоящего договора, никому не продан, не заложен, в споре и под  арестом не находится.",
                'text-type' => 'title',
            ),
            22 => array
            (
                'text' => "^4/7. Настоящий договор составлен в трех экземплярах - по  одному для каждой из сторон, один экземпляр для регистрирующего органа.",
                'text-type' => 'paragraph',
            ),
            23 => array
            (
                'text' => "^2/8. Адреса и реквизиты сторон",
                'text-type' => 'title',
            ),
            24 => array
            (
                'text' => "^6/Даритель: $firstside_requisites ^+______________ Подпись ^6*Одаряемый:  $secondside_requisites ^+______________ Подпись ",
                'text-type' => 'columns',
            )

        );

        $data = json_encode($data);
        echo $data;
        return true;
    }
    //------------------------------------------------------------------------------------------------------------------

    public function add_documents($doc_id,$user_id,$table)
    {
        $data = array(
            'doc_id' => $doc_id,
            'user_id' => $user_id,
            'table' => $table,
            'date' => date('Y-m-d H:i:s')
        );
        $this->db->insert('documents',$data);
        return $this->db->insert_id();
    }
    public function get_table_to_doc_id($id){
        $this->db->select('table');
        $this->db->where('id',$id);
        $query = $this->db->get('documents',1)->row();
        return $query->table;
    }
    public function bs_save_edit($post,$id){
        unset($post['doc_id']);

        $this->db->select('doc_id as id');
        $this->db->where('id',$id);
        $q = $this->db->get('documents',1)->row();

        $this->db->select('id, type_of_giver, type_of_taker, type_of_contract, car_in_marriage, police_form');
        $this->db->where('id',$q->id);
        $d = $this->db->get('buy_sale',1)->row();

        $post['type_of_giver'] = !empty($post['type_of_giver'])?$post['type_of_giver']:$d->type_of_giver;
        $post['type_of_taker'] = !empty($post['type_of_taker'])?$post['type_of_taker']:$d->type_of_taker;
        $post['type_of_contract'] = !empty($post['type_of_contract'])?$post['type_of_contract']:$d->type_of_contract;
        $post['car_in_marriage'] = !empty($post['car_in_marriage'])?$post['car_in_marriage']:$d->car_in_marriage;
        $post['police_form'] = !empty($post['police_form'])?$post['police_form']:$d->police_form;
        
        $post['type_id'] = $this->set_pack_of_documents($post['type_of_giver'], $post['type_of_taker'], 'buy_sale', $post['car_in_marriage'], $post['police_form']);

        if(!empty($post['additional_devices_array']))
            $post['additional_devices_array'] = json_encode($post['additional_devices_array']);
        if(!empty($post['documents']))
            $post['documents'] = json_encode($post['documents']);
        if(!empty($post['accessories']))
            $post['accessories'] = json_encode($post['accessories']);

        foreach ($post as $key => $item) {
            $data[$key] = $item;
        }
        $this->db->where('id',$q->id);
        $this->db->update('buy_sale', $data);
    }
    public function gift_save_edit($post,$id)
    {
        unset($post['doc_id']);

        $this->db->select('doc_id as id');
        $this->db->where('id',$id);
        $q = $this->db->get('documents',1)->row();

        $this->db->select('id, type_of_giver, type_of_taker, type_of_contract, police_form');
        $this->db->where('id',$q->id);
        $d = $this->db->get('gift',1)->row();

        $post['type_of_giver'] = !empty($post['type_of_giver'])?$post['type_of_giver']:$d->type_of_giver;
        $post['type_of_taker'] = !empty($post['type_of_taker'])?$post['type_of_taker']:$d->type_of_taker;
        $post['type_of_contract'] = !empty($post['type_of_contract'])?$post['type_of_contract']:$d->type_of_contract;
        //$post['car_in_marriage'] = !empty($post['car_in_marriage'])?$post['car_in_marriage']:$d->car_in_marriage;
        $post['police_form'] = !empty($post['police_form'])?$post['police_form']:$d->police_form;

        $post['type_id'] = $this->set_pack_of_documents($post['type_of_giver'], $post['type_of_taker'], 'gift', null, $post['police_form']);

        foreach ($post as $key => $item) {
            $data[$key] = $item;
        }
        $this->db->where('id',$q->id);
        $this->db->update('gift', $data);
    }

}