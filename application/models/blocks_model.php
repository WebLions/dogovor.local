<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Blocks_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();//Работа с бд
    }

    public function bs_consent(){
        echo <<<END
<div class="row" id="block_consent" data-id="1">
     <div class="col-lg-12 ">
        <div class = "content-block">
        <div class = "content-radio-header"
            <div class = "content-input-inline">
    		    <input  required  class="content-input-align" data-name="bs_vendor_block" type="checkbox" id="pact">Заполнить персональные данные сторон и адрес объекта
    		</div>
    	</div>
    	</div>
     </div>
</div>
END;
    }
    public function gift_consent(){
        echo <<<END
<div class="row" id="block_consent" data-id="1">
     <div class="col-lg-12 ">
        <div class = "content-block">
            <div class = "content-radio-header"
            <div class = "content-input-inline">
    		    <input  required  class="" data-name="gift_vendor_block" type="checkbox" id="pact">Заполнить персональные данные сторон и адрес объекта
    		</div>
    		</div>
    	</div>
     </div>
</div>
END;
    }
////
    //Базовые блоки продавца
    public function bs_block_deal()
    {
        echo <<<END
        <div class="row" id="block_deal" data-id="1">
     <div class="col-lg-12 ">
        <div class = "content-block">
            <div class = "content-input-group">
                <p style="text-align:justify;">Персональные данные, указанные Вами в конструкторе договора на нашем сайте, недоступны другим пользователям, и используются для генерации видимого только Вам текста договора. Данные передаются через защищенное шифрованием соединение, что подтверждает SSL сертификат Thawte. Мы уделяем серьезное внимание информационной безопасности наших серверов и конфиденциальности персональных данных наших клиентов.</p>
            </div>
        </div>
     </div>
    <div class="col-lg-12 ">
        <div class = "content-block">
            <div class = "content-input-group">
                <input  required   required  class = "form-control" type="text" name="place_of_contract"  placeholder="Место заключения договора:">
            </div>
            <div class = "content-input-group">
                <input  required   required  id="date_of_contract" class="form-control datetimepicker"  name="date_of_contract"  placeholder="Дата заключения договора:">
            </div>
        </div>
    </div>
</div>

END;
    }
    public function bs_block_vendor()
    {
        echo <<<END
<div class="row" id="block_vendor">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Продавец транспортного средства:</p>
            <div class = "content-radio-group">
                <div class = "content-radio">
                    <input  required   required  data-id="block_seller" class="ajax-button" data-name="bs_block_vendor_state" type="radio" name="type_of_giver" value="physical">
                    <span class = "content-input-align">Физическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required   required  data-id="block_seller" class="ajax-button" data-name="bs_block_vendor_state" type="radio" name="type_of_giver" value="law">
                    <span class = "content-input-align">Юридическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required   required  data-id="block_seller" class="ajax-button" data-name="bs_block_vendor_state" type="radio" name="type_of_giver" value="individual">
                    <span class = "content-input-align">Индивидуальный предприниматель</span>
                </div>
            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_vendor_state()
{
    echo <<<END
<div class="row" id="block_seller_info">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Статус продавца:</p>
            <div class="content-radio-group">

                <div class = "content-radio">
                    <input  required   required  class="ajax-button agent"  data-name="bs_block_vendor_selected_owner" type="radio" name="vendor_is_owner_car" value="own_car">
                    <span class = "content-input-align">Продавец является собственником ТС</span>
                </div>


                <div class = "content-radio">
                    <input  required   required  class="ajax-button agent" data-name="bs_block_vendor_selected_not_owner" type="radio" name="vendor_is_owner_car" value="not_own_car">
                    <span class = "content-input-align">Продавец не является собственником ТС и действует по доверенности</span>
                </div>
            </div>

        </div>
    </div>
</div>
END;
}
    public function bs_block_vendor_info()
    {
        echo <<<END
         <div class="row" id="vendor_info">
            <div class="col-lg-12">
            <div class = "content-block">
             <p class = "content-header">Введите данныe продавца:</p>
                <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="vendor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="vendor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="vendor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                <input  required   required  id="vendor_birthday" class="form-control datetimepicker" type="text"  name="vendor_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_passport_serial"  placeholder="Серия паспорта:">
                </div>
                <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="vendor_passport_number"  placeholder="Номер паспорта:">
                </div>
                <div class = "content-input-group">
                <input  required  id="vendor_passport_date" class = "form-control datetimepicker" type="text" name="vendor_passport_date"  placeholder="Дата выдачи паспорта:">
                </div>
                <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="vendor_passport_bywho"  placeholder="Кем выдан паспорт:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_city"  placeholder="Город (адрес регистрации):">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_house"  placeholder="№ Дома:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_flat"  placeholder="Квартира:">
                </div>
                <div class = "content-input-group">
                <input class="form-control" type="text" name="vendor_phone"  placeholder="Телефон:">
                </div>
                </div>
                </div>
            </div>
        </div>
END;
    }
    public function bs_block_vendor_agent()
    {
        echo <<<END
         <div class="row" id="for_agent_vendor_info">
            <div class="col-lg-12">
            <div class = "content-block">
             <p class = "content-header">Введите данныe представителя:</p>

                <div class = "content-input">
                    <div class = "content-input-group" style="display:inline-block;width:100%">
                    <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_vendor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="for_agent_vendor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_vendor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="for_agent_vendor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_vendor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="for_agent_vendor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="agent_vendor_birthday"  placeholder="Дата рождения:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="for_agent_vendor_proxy_number"  placeholder="Номер доверенности:">
                    </div>
                    <div class = "content-input-group">
                    <input  class="form-control" type="text" name="for_agent_vendor_proxy_notary"  placeholder="Кем выдана доверенность:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text"  name="for_agent_vendor_proxy_date"  placeholder="Дата выдачи доверенности:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="agent_vendor_pass_serial"  placeholder="Серия паспорта:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_pass_number"  placeholder="Номер паспорта:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text"  name="agent_vendor_pass_date"  placeholder="Когда выдан :">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_pass_bywho"  placeholder="Кем выдан:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="agent_vendor_city"  placeholder="Адрес(город):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_street"  placeholder="Адрес(улица):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  id="vendor_birthday" class="form-control" type="text"  name="agent_vendor_house"  placeholder="Адрес(дом):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_flat"  placeholder="Адрес(квартира):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="agent_vendor_phone"  placeholder="Телефон:">
                    </div>
             </div>
         </div>
    </div>
</div>
END;
    }
    public function gift_block_vendor_agent()
    {
        echo <<<END
         <div class="row" id="for_agent_vendor_info">
            <div class="col-lg-12">
            <div class = "content-block">
             <p class = "content-header">Введите данныe представителя:</p>
                <div class = "content-input">
                    <div class = "content-input-group" style="display:inline-block;width:100%">
                    <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_vendor_surname"  placeholder="Фамилия, например Иванов">
                <input  required  class = "form-control" style="float:left;width:48%" type="text" name="for_agent_vendor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_vendor_name"  placeholder="Имя, например Иван">
                <input  required  class="form-control" style="float:left;width:48%;" type="text" name="for_agent_vendor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_vendor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required  class = "form-control" style="float:left;width:48%;" type="text" name="for_agent_vendor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                    <div class="content-input-group">
                        <input required="" class="form-control datetimepicker" type="text" name="for_agent_proxy_birthday" placeholder="Дата рождения:" id="dp1460280812193">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="for_agent_vendor_proxy_number"  placeholder="Номер доверенности:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_vendor_proxy_notary"  placeholder="Кем выдана доверенность:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text"  name="for_agent_vendor_proxy_date"  placeholder="Дата выдачи доверенности:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="agent_vendor_pass_serial"  placeholder="Серия паспорта:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_pass_number"  placeholder="Номер паспорта:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text"  name="agent_vendor_pass_date"  placeholder="Когда выдан :">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_pass_bywho"  placeholder="Кем выдан:">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="agent_vendor_city"  placeholder="Адрес(город):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_street"  placeholder="Адрес(улица):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  id="vendor_birthday" class="form-control" type="text"  name="agent_vendor_house"  placeholder="Адрес(дом):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="agent_vendor_flat"  placeholder="Адрес(квартира):">
                    </div>
                    <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="agent_vendor_phone"  placeholder="Телефон:">
                    </div>
             </div>
         </div>
    </div>
</div>
END;
    }
    public function bs_block_buyer_agent()
    {
        echo <<<END
<div class="row" id="for_agent_vendor_info">
    <div class="col-lg-12">
        <div class = "content-block">
             <p class = "content-header">Введите данныe представителя:</p>
             <div class = "content-input">
                 <div class = "content-input-group" style="display:inline-block;width:100%">
                 <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_buyer_surname"  placeholder="Фамилия, например Иванов">
                <input  required class = "form-control" style="float:left;width:48%" type="text" name="for_agent_buyer_surname_parent"  placeholder="Фамилия, например Иванова">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_buyer_name"  placeholder="Имя, например Иван">
                <input  required class="form-control" style="float:left;width:48%;" type="text" name="for_agent_buyer_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_buyer_patronymic"  placeholder="Отчество, например Иванович">
                <input  required class = "form-control" style="float:left;width:48%;" type="text" name="for_agent_buyer_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                 <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="for_agent_proxy_birthday"  placeholder="Дата рождения:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="for_agent_buyer_proxy_number"  placeholder="Номер доверенности:">
                 </div>
                 <div class = "content-input-group">
                    <input class="form-control" type="text" name="for_agent_buyer_proxy_notary"  placeholder="Кем выдана доверенность:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text"  name="for_agent_buyer_proxy_date"  placeholder="Дата выдачи доверенности:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_pass_serial"  placeholder="Серия паспорта">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_pass_number"  placeholder="Номер паспорта">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text" name="for_agent_proxy_pass_date"  placeholder="Когда выдан">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_pass_bywho"  placeholder="Кем выдан">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_city"  placeholder="Адрес (Город)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_street"  placeholder="Адрес (Улица)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_house"  placeholder="Адрес (Дом)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_flat"  placeholder="Адрес (Квартира)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_phone"  placeholder="Телефон">
                 </div>
             </div>
         </div>
    </div>
</div>
END;
    }
    public function gift_block_buyer_agent()
    {
        echo <<<END
<div class="row" id="for_agent_vendor_info">
    <div class="col-lg-12">
        <div class = "content-block">
             <p class = "content-header">Введите данныe представителя:</p>
             <div class = "content-input">
                 <div class = "content-input-group" style="display:inline-block;width:100%">
                 <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_buyer_surname"  placeholder="Фамилия, например Иванов">
                <input  required  class = "form-control" style="float:left;width:48%" type="text" name="for_agent_buyer_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_buyer_name"  placeholder="Имя, например Иван">
                <input  required  class="form-control" style="float:left;width:48%;" type="text" name="for_agent_buyer_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="for_agent_buyer_patronymic"  placeholder="Отчество, например Иванович">
                <input  required  class = "form-control" style="float:left;width:48%;" type="text" name="for_agent_buyer_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="for_agent_proxy_birthday"  placeholder="Дата рождения:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="for_agent_buyer_proxy_number"  placeholder="Номер доверенности:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_buyer_proxy_notary"  placeholder="Кем выдана доверенность:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text"  name="for_agent_buyer_proxy_date"  placeholder="Дата выдачи доверенности:">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_pass_serial"  placeholder="Серия паспорта">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_pass_number"  placeholder="Номер паспорта">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text" name="for_agent_proxy_pass_date"  placeholder="Когда выдан">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_pass_bywho"  placeholder="Кем выдан">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_city"  placeholder="Адрес (Город)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_street"  placeholder="Адрес (Улица)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_house"  placeholder="Адрес (Дом)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_flat"  placeholder="Адрес (Квартира)">
                 </div>
                 <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="for_agent_proxy_phone"  placeholder="Телефон">
                 </div>
             </div>
         </div>
    </div>
</div>
END;
    }
    public function bs_block_buyer()
    {
        echo <<<END
<div class="row" id="block_buyer">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Покупатель транспортного средства:</p>

            <div class = "content-radio-group">
                <div class = "content-radio">
                    <input  required  data-id="block_buyer" class="ajax-button" data-name="bs_block_buyer_state" type="radio" name="type_of_taker" value="physical">
                    <span class = "content-input-align">Физическое лицо</span>
                </div>
                <div class = "content-radio">

                    <input  required  data-id="block_seller" class="ajax-button" data-name="bs_block_buyer_state" type="radio" name="type_of_taker" value="law">
                    <span class = "content-input-align">Юридическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required  data-id="block_seller" class="ajax-button" data-name="bs_block_buyer_state"" type="radio" name="type_of_taker" value="individual">
                    <span class = "content-input-align">Индивидуальный предприниматель</span>
                </div>
            </div>

        </div>
    </div>
</div>
END;
    }
    public function bs_block_buyer_info()
    {
        echo <<<END
        <div class="row" id="block_buyer_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe покупателя:</p>
        <div class = "content-radio">
            <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="buyer_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="buyer_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="buyer_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_birthday" class="form-control datetimepicker" type="text"  name="buyer_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_passport_serial"  placeholder="Серия паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_passport_number"  placeholder="Номер паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_passport_date" class = "form-control datetimepicker" type="text" name="buyer_passport_date"  placeholder="Дата выдачи паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_passport_bywho"  placeholder="Кем выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_city"  placeholder="Город (адрес регистрации):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_house"  placeholder="№ Дома:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_flat"  placeholder="Квартира:">
                </div>
                <div class = "content-input-group">
                    <input class="form-control" type="text" name="buyer_phone"  placeholder="Телефон">
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_buyer_state()
    {
        echo <<<END
<div class="row" id="block_buyer_state">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Статус покупателя:</p>
            <div class="content-radio-group">
                <div class = "content-radio">
                    <input  required  class="ajax-button agent" data-name="bs_block_buyer_selected_owner" type="radio" name="buyer_is_owner_car" value="own_car">
                    <span class = "content-input-align">Покупатель является новым собственником ТС</span>
                </div>
                <div class = "content-radio">
                    <input  required  class="ajax-button agent" data-name="bs_block_buyer_selected_not_owner" type="radio" name="buyer_is_owner_car" value="not_own_car">
                    <span class = "content-input-align">Покупатель не является новым собственником ТС и действует по доверенности</span>
                </div>
            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_ts_info()
    {
        echo <<<END
<div class="row" id="block_ts_info" data-id="7">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Сведения о траспортном средстве:</p>
            <div class = "content-radio">

                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="mark"  placeholder="Модель,марка:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vin"  placeholder="Идентификационный номер (VIN):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="reg_gov_number"  placeholder="Государственный регистрационный знак:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text"  name="car_type"  placeholder="Наименование(тип):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="category"  placeholder="Категория:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="date_of_product" class="form-control datetimepicker" type="text" name="date_of_product"  placeholder="Год изготовления:">
                </div>
                <div class = "content-input-group">
                    <input  class = "form-control" type="text" name="engine_model"  placeholder="Модель, номер двигателя:">
                </div>
                <div class = "content-input-group">
                    <input class="form-control" type="text" name="shassi"  placeholder="Номер рамы,шасси:">
                </div>
                <div class = "content-input-group">
                    <input  class = "form-control" type="text" name="carcass"  placeholder="Кузов(кабина,прицеп):">
                </div>
                <div class = "content-input-group">
                    <input  class = "form-control" type="text" name="color_carcass"  placeholder="Цвет кузова,кабины,прицепа:">
                </div>
                <div class = "content-input-group">
                    <input  class="form-control" type="text" name="other_parameters"  placeholder="Иные индивидуальные признаки:">
                </div>
            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_serial_car()
    {
        echo <<<END
<div class="row" id="block_serial_car" data-id="8">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Сведения о паспорте транспортного средства(ПТС):</p>

            <div class = "content-input-group">
                <input  required  class="form-control" type="text"  name="serial_car"  placeholder="Серия:">
            </div>
            <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="number_of_serial_car"  placeholder="Номер:">
            </div>
            <div class = "content-input-group">
                <input  required  id="date_of_serial_car" class="form-control datetimepicker" type="text" name="date_of_serial_car"  placeholder="Дата выдачи:">
            </div>
            <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="bywho_serial_car"  placeholder="Кем выдан">
            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_car_price()
    {
        echo <<<END
<div class="row" id="block_car_price" data-id="9">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Стоимость транспортного средства по договору:</p>

            <div style="width:100%"class = "content-input-group">
                   <input  required  style="width:80%;float:left;"class="form-control" type="text"  name="price_car"  placeholder="Стоимость:">
                <select style="width:15%" class="form-control" name="currency">
                    <option value="рублей">рублей</option>
                    <option value="долларов">долларов</option>
                    <option value="евро">евро</option>
                </select>
            </div>


        </div>
    </div>
</div>

END;
    }
    public function bs_block_additional_devices()
    {
        echo <<<END
<div class="row" id="block_additional_devices">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Серийное и дополнительное оборудование, установленное на ТС(Указать?):</p>
            <div class = "content-radio-header">
                <div class = "content-input-inlane">

                    <input  required  data-id="block_additional_devices" class="ajax-button" data-name="bs_block_additional_devices_yes" id = "mods_yes" type="radio" name="additional_devices" value="true">
                    <span class = "content-input-align">Да</span>

                    <input  required  data-id="block_additional_devices" class="ajax-button" data-name="bs_block_additional_devices_no" id = "mods_no"  type="radio" name="additional_devices" value="false">
                    <span class = "content-input-align">Нет</span>

                </div>

            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_additional_devices_list()
    {
        echo <<<END
<div class="row" id="block_additional_devices_list">
    <div class="col-lg-12">
        <div class = "content-block">
            <div class = "content-input-group">
                <input class="form-control" type="text"  name="oil_in_car"  placeholder="Залитое в двигатель масло">
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class = "content-radio-group">

                        <div class = "content-input">
                            <input  type="checkbox" data-name="rule" name="additional_devices_array[]" value="Левый руль">
                            <span class = "content-input-align">Левый руль</span>
                        </div>

                        <div class = "content-input">
                            <input type="checkbox" data-name="rule" name="additional_devices_array[]" value="Правый руль">
                            <span class = "content-input-align">Правый руль</span>
                        </div>

                        <div class = "content-input">
                            <input type="checkbox" data-name="dvs" name="additional_devices_array[]" value="Бензиновый ДВС">
                            <span class = "content-input-align">Бензиновый ДВС</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="dvs" name="additional_devices_array[]" value="Дизельный ДВС">
                            <span class = "content-input-align">Дизельный ДВС</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Газовое оборудование">
                            <span class = "content-input-align">Газовое оборудование</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Турбонаддув">
                            <span class = "content-input-align">Турбонаддув</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Интеркулер">
                            <span class = "content-input-align">Интеркулер</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="typed" name="additional_devices_array[]" value="Карбюратор">
                            <span class = "content-input-align">Карбюратор</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="typed" name="additional_devices_array[]" value="Инжектор">
                            <span class = "content-input-align">Инжектор</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="kpp" name="additional_devices_array[]" value="Механическая КПП">
                            <span class = "content-input-align">Механическая КПП</span>
                        </div>
                        <div class = "content-input">
                            <input   type="checkbox" data-name="kpp" name="additional_devices_array[]" value="Автоматическая КПП">
                            <span class = "content-input-align">Автоматическая КПП</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Галогеновые фары">
                            <span class = "content-input-align">Галогеновые фары</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Противотуманные фары">
                            <span class = "content-input-align">Противотуманные фары</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Омыватель фар">
                            <span class = "content-input-align">Омыватель фар</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="system" name="additional_devices_array[]" value="Противоугонная система штатная">
                            <span class = "content-input-align">Противоугонная система штатная</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="system" name="additional_devices_array[]" value="Противоугонная система механическая">
                            <span class = "content-input-align">Противоугонная система механическая</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" data-name="system" name="additional_devices_array[]" value="Противоугонная система электронная">
                            <span class = "content-input-align">Противоугонная система электронная</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Центральный замок">
                            <span class = "content-input-align">Центральный замок</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Аудиосистема">
                            <span class = "content-input-align">Аудиосистема</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Антенна наружная">
                            <span class = "content-input-align">Антенна наружная</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Антенна на ветровом стекле">
                            <span class = "content-input-align">Антенна на ветровом стекле</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Электрические стеклоподъемники">
                            <span class = "content-input-align">Электрические стеклоподъемники</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Окрашенные бамперы">
                            <span class = "content-input-align">Окрашенные бамперы</span>
                        </div>
                        <div class = "content-input">
                            <input    type="checkbox" data-name="lining" name="additional_devices_array[]" value="Накладки окрашенные">
                            <span class = "content-input-align">Накладки окрашенные</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="lining" name="additional_devices_array[]" value="Накладки хромированные">
                            <span class = "content-input-align">Накладки хромированные</span>
                        </div>

                        <div class = "content-input">
                            <input    type="checkbox" data-name="privod" name="additional_devices_array[]" value="Привод передний">
                            <span class = "content-input-align">Привод передний</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="privod" name="additional_devices_array[]" value="Привод задний">
                            <span class = "content-input-align">Привод задний</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" data-name="privod" name="additional_devices_array[]" value="Полный привод">
                            <span class = "content-input-align">Полный привод</span>
                        </div>

                    </div>
                </div>

                <div class="col-lg-6">
                    <div class = "content-radio">

                        <div class = "content-input">
                            <input  type="checkbox" data-name="block-system" name="additional_devices_array[]" value="Антиблокировочная тормозная система">
                            <span class = "content-input-align">Антиблокировочная тормозная система</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="rudder" name="additional_devices_array[]" value="Гидроусилитель руля">
                            <span class = "content-input-align">Гидроусилитель руля</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="rudder" name="additional_devices_array[]" value="Электроусилитель руля">
                            <span class = "content-input-align">Электроусилитель руля</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox"  data-name="reg_rudder" name="additional_devices_array[]" value="Регулируемая рулевая колонка">
                            <span class = "content-input-align">Регулируемая рулевая колонка</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="ton_glass" name="additional_devices_array[]" value="Тонированное ветровое стекло">
                            <span class = "content-input-align">Тонированное ветровое стекло</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" data-name="ton_glass" name="additional_devices_array[]" value="Тонированные стекла прочие">
                            <span class = "content-input-align">Тонированные стекла прочие</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="disk" name="additional_devices_array[]" value="Диски легкосплавные">
                            <span class = "content-input-align">Диски легкосплавные</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="disk" name="additional_devices_array[]" value="Диски штампованные">
                            <span class = "content-input-align">Диски штампованные</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" data-name="korrekt" name="additional_devices_array[]" value="Корректор фар">
                            <span class = "content-input-align">Корректор фар</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Спойлер передний">
                            <span class = "content-input-align">Спойлер передний</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Спойлер задний">
                            <span class = "content-input-align">Спойлер задний</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" data-name="luk_fun" name="additional_devices_array[]" value="Люк механический">
                                <span class = "content-input-align">Люк механический</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="luk_fun" name="additional_devices_array[]" value="Люк с электроприводом">
                            <span class = "content-input-align">Люк с электроприводом</span>
                        </div>

                        <div class = "content-input">
                            <input  type="checkbox" data-name="luk_material" name="additional_devices_array[]" value="Люк стеклянный">
                            <span class = "content-input-align">Люк стеклянный</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" data-name="luk_material" name="additional_devices_array[]" value="Люк металлический">
                            <span class = "content-input-align">Люк металлический</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Зеркала с электроприводом">
                            <span class = "content-input-align">Зеркала с электроприводом</span>
                        </div>
                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Зеркала с подогревом">
                            <span class = "content-input-align">Зеркала с подогревом</span>
                        </div>
                        <div class = "content-input">
                            <input   type="checkbox" data-name="salon" name="additional_devices_array[]" value="Салон кожаный">
                            <span class = "content-input-align">Салон кожаный</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" data-name="salon" name="additional_devices_array[]" value="Салон велюровый">
                            <span class = "content-input-align">Салон велюровый</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Подогрев сидений">
                            <span class = "content-input-align">Подогрев сидений</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Подушка безопасности водителя">
                            <span class = "content-input-align">Подушка безопасности водителя</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Подушка безопасности пассажира">
                            <span class = "content-input-align">Подушка безопасности пассажира</span>
                        </div>
                        <div class = "content-input">
                            <input  type="checkbox" name="additional_devices_array[]" value="Прочие подушки безопасности">
                            <span class = "content-input-align">Прочие подушки безопасности</span>
                        </div>
                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Кондиционер">
                            <span class = "content-input-align">Кондиционер</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Климат-контроль">
                            <span class = "content-input-align">Климат-контроль</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Круиз-контроль">
                            <span class = "content-input-align">Круиз-контроль</span>
                        </div>

                        <div class = "content-input">
                            <input   type="checkbox" name="additional_devices_array[]" value="Парктроник">
                            <span class = "content-input-align">Парктроник</span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
END;
    }
    public function bs_block_car_state()
    {
        echo <<<END
<div class="row" id="block_car_state" data-id="12">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Общее состояние транспортного средства:</p>

            <div class = "content-radio-group">
                <div class = "content-radio">
                    <input  type="radio" name="car_allstatus" value="Отличное">
                    <span class = "content-input-align">Отличное</span>
                </div>

                <div class = "content-radio">
                    <input type="radio" name="car_allstatus" value="Хорошее">
                    <span class = "content-input-align">Хорошее</span>
                </div>

                <div class = "content-radio">
                    <input  type="radio" name="car_allstatus" value="Удовлетворительное">
                    <span class = "content-input-align">Удовлетворительное</span>
                </div>

                <div class = "content-radio">
                    <input type="radio" name="car_allstatus" value="Не на ходу">
                    <span class = "content-input-align">Не на ходу</span>
                </div>

                <div class = "content-radio">
                    <input type="radio" name="car_allstatus" value="После ДТП">
                    <span class = "content-input-align">После ДТП</span>
                </div>

                <div class = "content-radio">
                    <input type="radio" name="car_allstatus" value="Восстановлению не подлежит">
                    <span class = "content-input-align">Восстановлению не подлежит</span>
                </div>
            </div>

        </div>
    </div>
</div>
END;
    }
    public function bs_block_maintenance()
    {
        echo <<<END
<div class="row" id="block_maintenance" data-id="13">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Последнее техническое обслуживание транспортного средства проведено:</p>

            <div class = "content-input-group">
                <input  id="maintenance_date" class="form-control datetimepicker" type="text"  name="maintenance_date"  placeholder="Дата:">
            </div>
            <div class = "content-input-group">
                <input  class = "form-control" type="text" name="maintenance_bywho"  placeholder="Кем проведено:">
            </div>

        </div>
    </div>
</div>
END;
    }
    public function bs_block_defects()
    {
        echo <<<END
<div class="row" id="block_defects">
    <div class="col-lg-12">
        <div class = "content-block" id="defects_block">
            <p class = "content-header">Неустраненные повреждения и эксплуатационные дефекты:</p>

            <div class = "content-radio-header">
                <div class = "content-input-inlane">
                    <input  required   id="defects_yes" type="radio" name="defects" value="true">
                    <span class = "content-input-align">Есть</span>

                    <input  required  id="defects_no" type="radio" name="defects" value="false">
                    <span class = "content-input-align">Нет</span>
                </div>
            </div>

        </div>
    </div>
</div>
END;
    }
    public function bs_block_features()
    {
        echo <<<END
<div class="row" id="block_features" data-id="15">
    <div class="col-lg-12">
        <div class = "content-block" id="features_block">
            <p class = "content-header">Особенности, которые не влияют на безопасность ТС:</p>

            <div class = "content-radio-header">
                <div class = "content-input-inlane">
                    <input  required  id="features_yes" type="radio" name="features" value="true">
                    <span class = "content-input-align">Есть</span>

                    <input  required  id="features_no" type="radio" name="features" value="false">
                    <span class = "content-input-align">Нет</span>
                </div>
            </div>

        </div>
    </div>
</div>

END;
    }
    public function bs_block_payment_date()
    {
        echo <<<END
<div class="row" id="bs_block_payment_date" data-id="16">
    <div class="col-lg-12">
        <div class = "content-block" id="block_payment_date">
            <p class = "content-header">Сроки оплаты:</p>
            <div class = "content-radio-group">

                <div class = "content-radio">
                    <input  required  type="radio" name="payment_date" value="до подписания настоящего договора">
                    <span class = "content-input-align">До подписания настоящего договора</span>
                </div>

                <div class = "content-radio">
                    <input  required  type="radio" name="payment_date" value="при подписании настоящего договора">
                    <span class = "content-input-align">При подписании настоящего договора</span>
                </div>

                <div class = "content-radio">
                    <input  required  id="credit" type="radio" name="payment_date" value="в рассрочку по следующему графику">
                    <span class = "content-input-align">В рассрочку по следующему графику:</span>
                </div>

            </div>
        </div>
    </div>
</div>


END;
    }
    public function bs_block_documents()
    {
        echo <<<END
<div class="row" id="block_documents" data-id="17">
    <div class="col-lg-12">
        <div class = "content-block content-seller-doc">
            <p class = "content-header">Продавец передает Покупателю следующие документы(Выберите из списка):</p>
            <div class = "content-radio-group">

                <div class = "content-input">
                    <input   type="checkbox" name="documents[]" value="Свидетельство о регистрации транспортного средства">
                    <span class = "content-input-align">Свидетельство о регистрации транспортного средства:</span>
                </div>

                <div class = "content-input">
                    <input   type="checkbox" name="documents[]" value="Диагностическую карту (талон технического осмотра)">
                    <span class = "content-input-align">Диагностическую карту (талон технического осмотра)</span>
                </div>

                <div class = "content-input">
                    <input   type="checkbox" name="documents[]" value="Гарантийную (сервисную) книжку">
                    <span class = "content-input-align">Гарантийную (сервисную) книжку</span>
                </div>

                <div class = "content-input">
                    <input   type="checkbox" name="documents[]" value="Инструкцию (руководство) по эксплуатации транспортного средства">
                    <span class = "content-input-align">Инструкцию (руководство) по эксплуатации транспортного средства</span>
                </div>

                <div class = "content-input">
                    <input  type="checkbox" name="documents[]" value="Гарантийные талоны и инструкции по эксплуатации на дополнительно установленное оборудование">
                    <span class = "content-input-align">Гарантийные талоны и инструкции по эксплуатации на дополнительно установленное оборудование</span>
                </div>

            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_accessories()
    {
        echo <<<END
<div class="row" id="block_accessories_other" data-id="18">
    <div class="col-lg-12">
        <div class = "content-block" id="block_accessories">
            <p class = "content-header">Продавец передает Покупателю следующие инструменты и принадлежности:</p>
            <div class = "content-radio-group">

                <div class = "content-input">

                    <input  type="checkbox" name="accessories[0][0]" value="Оригинальные ключи в количестве">
                    <span class = "content-input-align">Оригинальные ключи в количестве: <input  type="text" name="accessories[0][1]"></span>
                </div>

                <div class = "content-input">
                    <input type="checkbox" name="accessories[1][0]" value="Ключи от иммобилайзера в количестве">
                    <span class = "content-input-align">Ключи от иммобилайзера в количестве: <input  type="text" name="accessories[1][1]"></span>

                </div>

                <div class = "content-input">
                    <input type="checkbox" name="accessories[2]" value="Запасное колесо">
                    <span class = "content-input-align">Запасное колесо</span>
                </div>

                <div class = "content-input">
                    <input type="checkbox" name="accessories[3]" value="Домкрат">
                    <span class = "content-input-align">Домкрат</span>
                </div>

                <div class = "content-input">
                    <input  type="checkbox" name="accessories[4]" value="Балонный ключ">
                    <span class = "content-input-align">Балонный ключ</span>
                </div>

                <div class = "content-input">
                    <input  type="radio" id="accessories_other">
                    <span class = "content-input-align">Иное:</span>
                </div>

            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_car_in_marriage()
    {
        echo <<<END
<div class="row" id="block_car_in_marriage">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Автомобиль приобретен в период брака?</p>
            <div class = "content-radio-header">

                <div class = "content-input-inlane">
                    <input  required   class="ajax-button" data-name="bs_block_car_in_marriage_yes" type="radio" name="car_in_marriage" value="true">
                    <span class = "content-input-align">Да</span>

                    <input  required   class="ajax-button" data-name="bs_block_car_in_marriage_no" type="radio" name="car_in_marriage" value="false">
                    <span class = "content-input-align">Нет</span>
                </div>

            </div>
        </div>
    </div>
</div>

END;
    }
    public function bs_block_car_in_marriage_checked()
    {
        echo <<<END
<div class="row" id="block_car_in_marriage">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Автомобиль приобретен в период брака?</p>
            <div class = "content-radio-header">

                <div class = "content-input-inlane">
                    <input  required  checked class="ajax-button" data-name="bs_block_car_in_marriage_yes" type="radio" name="car_in_marriage" value="true">
                    <span class = "content-input-align">Да</span>

                    <input  required   class="ajax-button" data-name="bs_block_car_in_marriage_no" type="radio" name="car_in_marriage" value="false">
                    <span class = "content-input-align">Нет</span>
                </div>

            </div>
        </div>
    </div>
</div>

END;
    }
    public function bs_block_spounse()
    {
        echo <<<END
<div class="row" id="block_spouse" data-id="20">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Введите данныe супруги:</p>
            <div class = "content-radio">

                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="spouse_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text"  name="spouse_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="spouse_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="spouse_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="spouse_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="spouse_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  id="spouse_birthday" class="form-control datetimepicker" type="text"  name="spouse_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="spouse_pass_serial"  placeholder="Серия паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="spouse_pass_number"  placeholder="Номер паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="spouse_pass_date" class = "form-control datetimepicker" type="text" name="spouse_pass_date"  placeholder="Дата выдачи паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="spouse_pass_bywho"  placeholder="Кем выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="spouse_city"  placeholder="Адрес регистрации(город):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="spouse_street"  placeholder="Адрес регистрации(улица):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="spouse_house"  placeholder="Адрес регистрации(дом):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="spouse_flat"  placeholder="Адрес регистрации(квартира):">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="marriage_svid_serial"  placeholder="Серия свидетельства о браке:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="marriage_svid_number"  placeholder="Номер свидетельства о браке:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control datetimepicker" type="text" name="marriage_svid_date"  placeholder="Дата выдачи свидетельства о браке:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="marriage_svid_bywho"  placeholder="Kем выдано свидетельство о браке:">
                </div>

            </div>
        </div>
    </div>
</div>

END;
    }
    public function bs_block_penalty()
    {
        echo <<<END
        <div class="row" id="block_penalty" data-id="21">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Размер неустойки по договору</p>
            <div class = "content-radio-header">

                <div class = "content-input-inlane">
                    <input  type="radio" name="penalty" value="0,02%">
                    <span class = "content-input-align">0,02%</span>

                    <input  type="radio" name="penalty" value="0,05%">
                    <span class = "content-input-align">0,05%</span>

                    <input  type="radio" name="penalty" value="0,1%">
                    <span class = "content-input-align">0,1%</span>
                </div>

            </div>
        </div>
    </div>
</div>
END;
    }
    public function bs_block_ready()
    {
        echo <<<END
<div class="row" id="block_ready">
    <div class="col-lg-12">
        <div class = "content-button">
            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modal_ready">
                Оплатить и скачать
            </button>
            <!-- Modal -->
            <div class="modal fade" id="modal_ready" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" id="modal_ready">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Спасибо за использование нашего сервиса.</h4>
        </div>

        <div class="modal-body">
           Хотите дополнительно оформить заявление в ГИБДД?(Это совершенно бесплатно).
            <div class = "content-radio-header">
                <div class = "content-input-inlane">
                    <input  required   class="modal-button" data-type="final" data-name="police_yes"type="radio" name="police_form" value="true">
                    <span class = "content-input-align">Да</span>

                    <input  required   class="modal-button" data-type="final" data-name="police_no" type="radio" name="police_form" value="false">
                    <span class = "content-input-align">Нет</span>
                </div>
            </div>
            <div class="modal-body-final"></div>
            <div class="modal-body-statement"></div>
        </div>

        <div class="modal-footer" style="text-align: center">

        </div>
    </div>
</div>

            </div>
        </div>
    </div>
</div>
END;

    }

    public function bs_block_statement_no($email)
    {
        echo <<<END
        <div class="row" >
            {$this->getEmailInput($email)}
            <div class="col-lg-12">
                <button id="final_button" type="submit" class="btn btn-success">Сохранить и оплатить</button>
            </div>
        </div>
END;
    }

    public function bs_block_statement_gibdd($email)
    {
        $this->bs_block_statement_no($email);
        return false;

        echo <<<END
<div class="row">
    <div class="col-lg-12">
    <div class = "content-block">
        <div class = "content-input">
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_pass_serial"  placeholder="Серия паспорта">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_pass_number"  placeholder="Номер паспорта">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_pass_date"  placeholder="Когда выдан">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_pass_bywho"  placeholder="Кем выдан">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_city"  placeholder="Адрес (Город)">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_street"  placeholder="Адрес (Улица)">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_house"  placeholder="Адрес (Дом)">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_flat"  placeholder="Адрес (Квартира)">
             </div>
             <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="for_agent_proxy_phone"  placeholder="Телефон">
             </div>
        </div>
        </div>
    </div>
</div>
    <div class="row">
            {$this->getEmailInput($email)}
            <div class="col-lg-12">
                <button id="ready_button" type="submit" class="btn btn-success">Сохранить и оплатить</button>
            </div>
     </div>

END;

    }

    public function bs_block_vendor_law_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe продавца:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_company_name"  placeholder="Наименование компании: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="vendor_law_date_of_create"  placeholder="Дата регистрации/создания общества">
                </div>
                <div class = "content-input-group">
                <p class = "content-input-title" style="width:48%;margin-right:20px;">В лице:</p>
                    <select  required  class="form-control" name="vendor_law_actor_position" ">
                        <option value="генерального директора">Генеральный директор</option>
                        <option value="директора">Директор</option>
                    </select>
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_law_actor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="vendor_law_actor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_law_actor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="vendor_law_actor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_law_actor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="vendor_law_actor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_document_osn"  placeholder="Действующего на основании:">
                </div>
                <div class = "content-input-group">
                    <input id="vendor_birthday" class="form-control datetimepicker" type="text"  name="vendor_law_proxy_date"  placeholder="Дата выдачи доверенности:">
                </div>
                <div class = "content-input-group">
                    <input class = "form-control" type="text" name="vendor_law_proxy_number"  placeholder="Номер доверенности: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_inn"  placeholder="ИНН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_ogrn"  placeholder="ОГРН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_adress"  placeholder="Юридический адрес: ">
                </div>
                <div class = "content-input-group">
                    <input  class = "form-control" type="text" name="vendor_law_phone"  placeholder="Телефон:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_acc"  placeholder="Расчетный счет">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_bank_name"  placeholder="Наименование банка:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;

    }
    public function bs_block_vendor_individual_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe продавца:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_ind_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="vendor_ind_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_ind_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="vendor_ind_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_ind_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="vendor_ind_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  id="vendor_birthday" class="form-control datetimepicker" type="text"  name="vendor_ind_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_number_of_certificate"  placeholder="Номер свидетельства: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="vendor_ind_date_of_certificate" class="form-control datetimepicker" type="text" name="vendor_ind_date_of_certificate"  placeholder="Дата выдачи: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_passport_serial"  placeholder="Паспорт серия:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_passport_number"  placeholder="Паспорт номер: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="vendor_passport_date"  class="form-control datetimepicker" type="text" name="vendor_ind_passport_date"  placeholder="Когда выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required   class = "form-control" type="text" name="vendor_ind_passport_bywho"  placeholder="Кем выдан:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_city"  placeholder="Адрес регистрации(город):">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_house"  placeholder="№ дома: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_flat"  placeholder="Номер квартиры:">
                </div>
                <div class = "content-input-group">
                    <input   class = "form-control" type="text" name="vendor_ind_phone"  placeholder="Телефон">
                </div>
                <div class = "content-input-group">
                    <input  class = "form-control" type="text" name="vendor_ind_bank_acc"  placeholder="Расчетный счет:">
                </div>
                <div class = "content-input-group">
                    <input    class = "form-control" type="text" name="vendor_ind_bank_name"  placeholder="В банке:">
                </div>
                <div class = "content-input-group">
                    <input   class="form-control" type="text" name="vendor_ind_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input   class="form-control" type="text" name="vendor_ind_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;
    }

    public function bs_block_buyer_law_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe покупателя:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_company_name"  placeholder="Наименование компании: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="buyer_law_date_of_create"  placeholder="Дата регистрации/создания общества">
                </div>
                <div class = "content-input-group">
                 <p class = "content-input-title" style="float:left;width:48%;margin-right:20px;">В лице:</p>
                    <select  required  class="form-control" name="buyer_law_actor_position" ">
                        <option value="генерального директора">Генеральный директор</option>
                        <option value="директора">Директор</option>
                    </select>
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_law_actor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="buyer_law_actor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_law_actor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="buyer_law_actor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_law_actor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="buyer_law_actor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>

                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_document_osn"  placeholder="Действующего на основании:">
                </div>
                <div class = "content-input-group">
                    <input id="buyer_birthday" class="form-control datetimepicker" type="text"  name="buyer_law_proxy_date"  placeholder="Дата выдачи доверенности:">
                </div>
                <div class = "content-input-group">
                    <input class = "form-control" type="text" name="buyer_law_proxy_number"  placeholder="Номер доверенности: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_inn"  placeholder="ИНН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_ogrn"  placeholder="ОГРН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_adress"  placeholder="Юридический адрес: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_phone"  placeholder="Телефон:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_acc"  placeholder="Расчетный счет">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_bank_name"  placeholder="Наименование банка:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;

    }
    public function bs_block_buyer_individual_state()
    {
        echo <<<END
<div class="row" id="block_buyer_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe покупателя:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_ind_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="buyer_ind_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_ind_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="buyer_ind_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_ind_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="buyer_ind_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_birthday" class="form-control datetimepicker" type="text"  name="buyer_ind_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_number_of_certificate"  placeholder="Номер свидетельства: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_ind_date_of_certificate" class="form-control datetimepicker" type="text" name="buyer_ind_date_of_certificate"  placeholder="Дата выдачи: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_passport_serial"  placeholder="Паспорт серия:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_passport_number"  placeholder="Паспорт номер: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_passport_date" class="form-control datetimepicker" type="text" name="buyer_ind_passport_date"  placeholder="Когда выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_passport_bywho" placeholder="Кем выдан:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_city"  placeholder="Адрес регистрации(Город):">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_house"  placeholder="№ дома: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_flat"  placeholder="Номер квартиры:">
                </div>
                <div class = "content-input-group">
                    <input required class = "form-control" type="text" name="buyer_ind_phone"  placeholder="Телефон:">
                </div>
                <div class = "content-input-group">
                    <input class = "form-control" type="text" name="buyer_ind_bank_acc"  placeholder="Расчетный счет:">
                </div>
                <div class = "content-input-group">
                    <input   class = "form-control" type="text" name="buyer_ind_bank_name"  placeholder="В банке:">
                </div>
                <div class = "content-input-group">
                    <input    class="form-control" type="text" name="buyer_ind_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input   class="form-control" type="text" name="buyer_ind_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;
    }

    public function bs_block_police_no($email)
    {
        echo <<<END
        <div class="row" id="block_police" >
            {$this->getEmailInput($email)}
            <div class="col-lg-12">
                <button id="ready_button" type="submit" class="btn btn-success">Сохранить и оплатить</button>
            </div>
        </div>
END;
    }

    public function bs_block_police_yes($email)
    {
        $text = "";
        if($_GET['buyer']=='true')
            $text = <<<END
    <div class="col-lg-12">
        Кто несет заявление в ГИБДД?
            <div class = "content-radio-header">
                <div class = "content-input-inlane">
                    <input  required   class="modal-button" data-type="statement" data-name="statement_buy" type="radio" name="statement_form" value="true">
                    <span class = "content-input-align">Приобретатель лично</span>

                    <input  required   class="modal-button" data-type="statement" data-name="statement_repres" type="radio" name="statement_form" value="false">
                    <span class = "content-input-align">Представитель</span>
                </div>
            </div>
    </div>
END;
        else
            $text = <<<END
<div class="row" id="block_police" >
            {$this->getEmailInput($email)}
            <div class="col-lg-12">
                <button id="ready_button" type="submit" class="btn btn-success">Сохранить и оплатить</button>
            </div>
        </div>
END;

        echo <<<END
        <div class="row" id="block_police" >
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Заявление в ГИББД</p>
            <div class = "content-radio-header">

              <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_reg_name"  placeholder="Наименование регистрационного подразделения ГИБДД:">
              </div>
            </div>
            <p class = "content-header">Сведения из ПТС транспортного средства:</p>
            <div class = "content-radio-header">

             <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_power_engine"  placeholder="Мощность двигателя в кВт:">
              </div>
              <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_eco_class"  placeholder="Экологический класс:">
              </div>
              <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_max_mass"  placeholder="Разрешенная максимальная масса:">
              </div>
               <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_min_mass"  placeholder="Разрешенная минимальная  масса:">
              </div>

            </div>
        </div>
    </div>
    {$text}
</div>
END;

    }
    public function bs_block_police_yes_physical($email)
    {
        $text = "";
        if($_GET['buyer']=='true')
            $text = <<<END
    <div class="col-lg-12">
        Кто несет заявление в ГИБДД?
            <div class = "content-radio-header">
                <div class = "content-input-inlane">
                    <input  required   class="modal-button" data-type="statement" data-name="statement_buy" type="radio" name="statement_form" value="true">
                    <span class = "content-input-align">Приобретатель лично</span>

                    <input  required   class="modal-button" data-type="statement" data-name="statement_repres" type="radio" name="statement_form" value="false">
                    <span class = "content-input-align">Представитель</span>
                </div>
            </div>
    </div>
END;
        else
            $text = <<<END
<div class="row" id="block_police" >
            {$this->getEmailInput($email)}
            <div class="col-lg-12">
                <button id="ready_button" type="submit" class="btn btn-success">Сохранить и оплатить</button>
            </div>
        </div>
END;
        echo <<<END
        <div class="row" id="block_police" >
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Заявление в ГИББД</p>
            <div class = "content-radio-header">

              <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_reg_name"  placeholder="Наименование регистрационного подразделения ГИБДД:">
              </div>
              <div class = "content-input-group">
                 <input class = "form-control" type="text" name="gibdd_inn"  placeholder="ИНН (для физических лиц при наличии):">
             </div>
            </div>
            <p class = "content-header">Сведения из ПТС транспортного средства:</p>
            <div class = "content-radio-header">

             <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_power_engine"  placeholder="Мощность двигателя в кВт:">
              </div>
              <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_eco_class"  placeholder="Экологический класс:">
              </div>
              <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_max_mass"  placeholder="Разрешенная максимальная масса:">
              </div>
               <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="gibdd_min_mass"  placeholder="Разрешенная минимальная  масса:">
              </div>

            </div>
        </div>
    </div>
    {$text}
</div>
END;

    }

    //Дарение ТС
    public function gift_block_vendor()
    {

        echo <<<END
<div class="row" id="block_seller">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Даритель транспортного средства:</p>
            <div class = "content-radio-group">
                <div class = "content-radio">
                    <input  required  data-id="block_seller" class="ajax-button" data-name="gift_block_vendor_state" type="radio" name="type_of_giver" value="physical">
                    <span class = "content-input-align">Физическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required  data-id="block_seller" class="ajax-button" data-name="gift_block_vendor_state" type="radio" name="type_of_giver" value="law">
                    <span class = "content-input-align">Юридическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required  class="ajax-button" data-name="gift_block_vendor_state" type="radio" name="type_of_giver" value="individual">
                    <span class = "content-input-align">Индивидуальный предприниматель</span>

                </div>
            </div>
        </div>
    </div>
</div>
END;
    }
    public function gift_block_buyer()
    {
        echo <<<END
<div class="row" id="block_seller">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Одаряемый:</p>
            <div class = "content-radio-group">
                <div class = "content-radio">
                    <input  required  data-id="block_seller" class="ajax-button" data-name="gift_block_buyer_state" type="radio" name="type_of_taker" value="physical">
                    <span class = "content-input-align">Физическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required  data-id="block_seller" class="ajax-button" data-name="gift_block_buyer_state" type="radio" name="type_of_taker" value="law">
                    <span class = "content-input-align">Юридическое лицо</span>
                </div>
                <div class = "content-radio">
                    <input  required  class="ajax-button" data-name="gift_block_buyer_state" type="radio" name="type_of_taker" value="individual">
                    <span class = "content-input-align">Индивидуальный предприниматель</span>

                </div>
            </div>
        </div>
    </div>
</div>
END;
    }
    public function gift_block_vendor_info()
    {
        echo <<<END
         <div class="row" id="vendor_info">
            <div class="col-lg-12">
            <div class = "content-block">
             <p class = "content-header">Введите данныe дарителя:</p>
                <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="vendor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="vendor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="vendor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                <input  required  id="vendor_birthday" class="form-control datetimepicker" type="text"  name="vendor_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_passport_serial"  placeholder="Серия паспорта:">
                </div>
                <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="vendor_passport_number"  placeholder="Номер паспорта:">
                </div>
                <div class = "content-input-group">
                <input  required  id="vendor_passport_date" class = "form-control datetimepicker" type="text" name="vendor_passport_date"  placeholder="Дата выдачи паспорта:">
                </div>
                <div class = "content-input-group">
                <input  required  class="form-control" type="text" name="vendor_passport_bywho"  placeholder="Кем выдан паспорт:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_city"  placeholder="Город (адрес регистрации):">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_house"  placeholder="№ Дома:">
                </div>
                <div class = "content-input-group">
                <input  required  class = "form-control" type="text" name="vendor_flat"  placeholder="Квартира:">
                </div>
                <div class = "content-input-group">
                <input class="form-control" type="text" name="vendor_phone"  placeholder="Телефон:">
                </div>
                </div>
                </div>
            </div>
        </div>
END;
    }
    public function gift_block_buyer_info()
    {
        echo <<<END
        <div class="row" id="block_buyer_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe одаряемого:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="buyer_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="buyer_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="buyer_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_birthday" class="form-control datetimepicker" type="text"  name="buyer_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_passport_serial"  placeholder="Серия паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_passport_number"  placeholder="Номер паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_passport_date" class = "form-control datetimepicker" type="text" name="buyer_passport_date"  placeholder="Дата выдачи паспорта:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_passport_bywho"  placeholder="Кем выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_city"  placeholder="Город (адрес регистрации):">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_house"  placeholder="№ Дома:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_flat"  placeholder="Квартира:">
                </div>
                <div class = "content-input-group">
                    <input required class="form-control" type="text" name="buyer_phone"  placeholder="Телефон">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;
    }
    public function gift_block_vendor_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Статус дарителя:</p>
            <div class="content-radio-group">

                <div class = "content-radio">
                    <input  required  class="ajax-button" data-name="gift_block_vendor_selected_owner" type="radio" name="vendor_is_owner_car" value="own_car">
                    <span class = "content-input-align">Даритель является собственником ТС</span>
                </div>
                <div class = "content-radio">
                    <input  required  class="ajax-button" data-name="gift_block_vendor_selected_not_owner" type="radio" name="vendor_is_owner_car" value="not_own_car">
                    <span class = "content-input-align">Даритель не является собственником ТС и действует по доверенности</span>
                </div>
            </div>

        </div>
    </div>
</div>
END;
    }
    public function gift_block_buyer_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Статус одаряемого:</p>
            <div class="content-radio-group">

                <div class = "content-radio">
                    <input  required   class="ajax-button"  data-name="gift_block_buyer_selected_owner" type="radio" name="buyer_is_owner_car" value="own_car">
                    <span class = "content-input-align">Одаряемый является собственником ТС</span>
                </div>


                <div class = "content-radio">
                    <input  required  class="ajax-button"  data-name="gift_block_buyer_selected_not_owner" type="radio" name="buyer_is_owner_car" value="not_own_car">
                    <span class = "content-input-align">Одаряемый не является собственником ТС и действует по доверенности</span>
                </div>
            </div>

        </div>
    </div>
</div>
END;
    }
    public function gift_block_pts_info()
    {
        echo <<<END
<div class="row" id="block_pts_info" data-id="7">
    <div class="col-lg-12">
        <div class = "content-block">
            <p class = "content-header">Сведения о паспорте транспортного средства(ПТС)</p>
            <div class = "content-radio">

                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="serial_car"  placeholder="Серия:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="number_of_serial_car"  placeholder="Номер:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="date_of_serial_car" class = "form-control datetimepicker" type="text" name="date_of_serial_car"  placeholder="Дата выдачи:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text"  name="bywho_serial_car"  placeholder="Кем выдан:">
                </div>
            </div>
        </div>
    </div>
</div>
END;
    }

    public function gift_block_vendor_law_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe дарителя:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_company_name"  placeholder="Наименование компании: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="vendor_law_date_of_create"  placeholder="Дата регистрации/создания общества">
                </div>

                <div class = "content-input-group">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:20px;">В лице:</p>
                    <select  required  class="form-control" name="vendor_law_actor_position" ">
                        <option value="генерального директора">Генеральный директор</option>
                        <option value="директора">Директор</option>
                    </select>
                </div>
                 <div class = "content-input-group" style="display:inline-block;width:100%">
                 <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_law_actor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="vendor_law_actor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_law_actor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="vendor_law_actor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_law_actor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="vendor_law_actor_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_document_osn"  placeholder="Действующего на основании:">
                </div>
                <div class = "content-input-group">
                    <input id="vendor_birthday" class="form-control datetimepicker" type="text"  name="vendor_law_proxy_date"  placeholder="Дата выдачи доверенности:">
                </div>
                <div class = "content-input-group">
                    <input class = "form-control" type="text" name="vendor_law_proxy_number"  placeholder="Номер доверенности: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_inn"  placeholder="ИНН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_ogrn"  placeholder="ОГРН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_adress"  placeholder="Юридический адрес: ">
                </div>
                <div class = "content-input-group">
                    <input  class = "form-control" type="text" name="vendor_law_phone"  placeholder="Телефон:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_acc"  placeholder="Расчетный счет">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_bank_name"  placeholder="Наименование банка:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_law_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_law_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;

    }
    public function gift_block_vendor_individual_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe дарителя:</p>
        <div class = "content-radio">

            <div class = "content-input">
               <div class = "content-input-group" style="display:inline-block;width:100%">
               <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_ind_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="vendor_ind_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_ind_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="vendor_ind_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="vendor_ind_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="vendor_ind_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_number_of_certificate"  placeholder="Номер свидетельства: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="vendor_ind_date_of_certificate" class="form-control datetimepicker" type="text" name="vendor_ind_date_of_certificate"  placeholder="Дата выдачи: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_passport_serial"  placeholder="Паспорт серия:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="vendor_birthday" class="form-control datetimepicker" type="text"  name="vendor_ind_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_passport_number"  placeholder="Паспорт номер: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="vendor_passport_date"  class="form-control datetimepicker" type="text" name="vendor_ind_passport_date"  placeholder="Когда выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required   class = "form-control" type="text" name="vendor_ind_passport_bywho"  placeholder="Кем выдан:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_city"  placeholder="Адрес регистрации:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_house"  placeholder="№ дома: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_flat"  placeholder="Номер квартиры:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_phone"  placeholder="Телефон">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_bank_acc"  placeholder="Расчетный счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="vendor_ind_bank_name"  placeholder="В банке:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="vendor_ind_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;
    }

    public function gift_block_buyer_law_state()
    {
        echo <<<END
<div class="row" id="block_seller_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe одаряемого:</p>
        <div class = "content-radio">

            <div class = "content-input">

                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_company_name"  placeholder="Наименование компании: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control datetimepicker" type="text" name="buyer_law_date_of_create"  placeholder="Дата регистрации/создания общества">
                </div>

                <div class = "content-input-group">
                   <p class = "content-input-title" style="float:left;width:48%;margin-right:20px;">В лице:</p>
                    <select  required  class="form-control" name="buyer_law_actor_position" ">
                        <option value="генерального директора">Генеральный директор</option>
                        <option value="директора">Директор</option>
                    </select>
                </div>
                 <div class = "content-input-group" style="display:inline-block;width:100%">
                 <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_law_actor_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="buyer_law_actor_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_law_actor_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="buyer_law_actor_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_law_actor_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="buyer_law_actor_patronymic_parent"  placeholder="Отчество, например Ивановича:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_document_osn"  placeholder="Действующего на основании:">
                </div>
                <div class = "content-input-group">
                    <input id="buyer_birthday" class="form-control datetimepicker" type="text"  name="buyer_law_proxy_date"  placeholder="Дата выдачи доверенности:">
                </div>
                <div class = "content-input-group">
                    <input class = "form-control" type="text" name="buyer_law_proxy_number"  placeholder="Номер доверенности: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_inn"  placeholder="ИНН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_ogrn"  placeholder="ОГРН:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_adress"  placeholder="Юридический адрес: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_phone"  placeholder="Телефон:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_acc"  placeholder="Расчетный счет">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_bank_name"  placeholder="Наименование банка:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_law_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_law_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;

    }
    public function gift_block_buyer_individual_state()
    {
        echo <<<END
<div class="row" id="block_buyer_info">
<div class="col-lg-12">
    <div class = "content-block">
        <p class = "content-header">Введите данныe одаряемого:</p>
        <div class = "content-radio">

            <div class = "content-input">
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для договора:</p>
                <p class = "content-input-title" style="float:left;width:48%;margin-right:6px;">Для остальных документов:</p>
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_ind_surname"  placeholder="Фамилия, например Иванов">
                <input  required   required  class = "form-control" style="float:left;width:48%" type="text" name="buyer_ind_surname_parent"  placeholder="Фамилия, например Иванова:">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class="form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_ind_name"  placeholder="Имя, например Иван">
                <input  required   required  class="form-control" style="float:left;width:48%;" type="text" name="buyer_ind_name_parent"  placeholder="Имя, например Ивана">
                </div>
                <div class = "content-input-group" style="display:inline-block;width:100%">
                <input  required   required  class = "form-control" style="float:left;width:48%;margin-right:6px;" type="text" name="buyer_ind_patronymic"  placeholder="Отчество, например Иванович">
                <input  required   required  class = "form-control" style="float:left;width:48%;" type="text" name="buyer_ind_patronymic_parent"  placeholder="Отчество, например Ивановича">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_number_of_certificate"  placeholder="Номер свидетельства: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_ind_date_of_certificate" class="form-control datetimepicker" type="text" name="buyer_ind_date_of_certificate"  placeholder="Дата выдачи: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_passport_serial"  placeholder="Паспорт серия:">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_birthday" class="form-control datetimepicker" type="text"  name="buyer_ind_birthday"  placeholder="Дата рождения:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_passport_number"  placeholder="Паспорт номер: ">
                </div>
                <div class = "content-input-group">
                    <input  required  id="buyer_passport_date" class="form-control datetimepicker" type="text" name="buyer_ind_passport_date"  placeholder="Когда выдан паспорт:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_passport_bywho" placeholder="Кем выдан:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_city"  placeholder="Адрес регистрации:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_street"  placeholder="Улица:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_house"  placeholder="№ дома: ">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_flat"  placeholder="Номер квартиры:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_phone"  placeholder="Телефон">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_bank_acc"  placeholder="Расчетный счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class = "form-control" type="text" name="buyer_ind_bank_name"  placeholder="В банке:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_korr_acc"  placeholder="Корр.счет:">
                </div>
                <div class = "content-input-group">
                    <input  required  class="form-control" type="text" name="buyer_ind_bik"  placeholder="БИК:">
                </div>
            </div>
           </div>
        </div>
    </div>
</div>
END;
    }


    private function getEmailInput($email){

        if($email)
            return <<<END
    <div class="col-lg-12">
        <h4>Введите ваш E-mail, после оплаты документа вам прийдет пароль от собственного аккаунта где вы сможете скачать документ</h4>
        <div class = "content-input-group">
            <input  type="text" name="email" required>
        </div>
    </div>
END;
    }



}



