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

?><?$APPLICATION->IncludeComponent(
	"predko:entity.list", 
	".default", 
	array(
		"CACHE_TIME" => "300",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "/#ENTITY_NAME#/#ENTITY_NAME#_detail.php?ID=#ENTITY_ID#",
		"ENTITY_COUNT" => "20",
		"ENTITY_FIELDS" => array(
			0 => "0",
			1 => "1",
			2 => "2",
		),
		"ENTITY_NAME" => "Customer",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>