<?php
/**
* file customer.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 11-08-2022
* Добавить клиента
*/

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("");

use \Predko\Customers\CustomerTable;
use \Predko\Customers\Customer\Customer;

use Bitrix\Main\Application;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$response = "";

if ($request["Name"] == "")
{
    $response = "Название организации обязательно";
}
else
if ($request["UNP"] == "") 
{
    $response = "УНП организации обязательно";
}
else 
{
    $customer = new Customer;

    $customer->setName($request["Name"]);
    $customer->setUnp($request["UNP"]);
    $customer->save();


}

?><?$APPLICATION->IncludeComponent("bitrix:form.result.new", "import_CSV", Array(
	"CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CHAIN_ITEM_LINK" => "",	// Ссылка на дополнительном пункте в навигационной цепочке
		"CHAIN_ITEM_TEXT" => "",	// Название дополнительного пункта в навигационной цепочке
		"EDIT_URL" => "result_edit.php",	// Страница редактирования результата
		"IGNORE_CUSTOM_TEMPLATE" => "N",	// Игнорировать свой шаблон
		"LIST_URL" => "result_list.php",	// Страница со списком результатов
		"SEF_MODE" => "N",	// Включить поддержку ЧПУ
		"SUCCESS_URL" => "",	// Страница с сообщением об успешной отправке
		"USE_EXTENDED_ERRORS" => "Y",	// Использовать расширенный вывод сообщений об ошибках
		"VARIABLE_ALIASES" => array(
			"RESULT_ID" => "RESULT_ID",
			"WEB_FORM_ID" => "WEB_FORM_ID",
		),
		"WEB_FORM_ID" => "3",	// ID веб-формы
	),
	false
);?>
<div>
 <br>
</div>
<div>
	 <?$APPLICATION->IncludeComponent(
	"predko:entity.list",
	".default",
	Array(
		"CACHE_TIME" => "300",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => ".default",
		"DETAIL_URL" => "/#ENTITY_NAME#/#ENTITY_NAME#_detail.php?ID=#ENTITY_ID#",
		"ENTITY_COUNT" => "20",
		"ENTITY_FIELDS" => array(0=>"0",1=>"1",2=>"2",),
		"ENTITY_NAME" => "Customer"
	)
);?>
</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>