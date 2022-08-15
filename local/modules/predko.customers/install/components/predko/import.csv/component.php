<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Entity;

include_once(__DIR__."../../constants.php");

use \Predko\Customers\CustomerTable;
use \Predko\Customers\Customer\Customer;
use Predko\Customers\Customer\Customers;
use \Bitrix\Main\Localization\Loc;

use Bitrix\Main\
{   Application, 
    Context, 
    Request, 
    Server
};


if (! \Bitrix\Main\Loader::includeModule('predko.customers'))
{
	ShowError(GetMessage("PREDKO_CUSTOMERS_MODULE_NOT_INSTALLED"));
	return;
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

if(check_bitrix_sessid())
{
    debug($request, "request");

	return;
}

debug($arParams);

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;

$arTypesEntity = array (
	'Customer' => Loc::GetMessage("PREDKO_IMPORT_CSV_CUSTOMER_ENTITY_NAME"),
	'Contract' => Loc::GetMessage("PREDKO_IMPORT_CSV_CONTRACT_ENTITY_NAME"),
	'Income' => Loc::GetMessage("PREDKO_IMPORT_CSV_INCOME_ENTITY_NAME"),
	'Expense' => Loc::GetMessage("PREDKO_IMPORT_CSV_EXPENSE_ENTITY_NAME"),
);

// Preparing entity field names
$arEntityFieldNames = [];
foreach ($arTypesEntity as $name => $desc) 
{
	$entityClassName = "\\Predko\\Customers\\".$name."Table";
	$fieldNames = $entityClassName::getFieldNames();
	
	$arEntityFieldNames[$name] = [
		'ENTITY_NAME' => $name,
		'ENTITY_DESC' => $desc,
		'FIELD_NAMES' => $fieldNames,
		'ENTITY_CLASS_NAME' => $entityClassName
	];
}

$arParams["LIST_COUNT"] = intval($arParams["LIST_COUNT"]);
if($arParams["LIST_COUNT"]<=0)
	$arParams["LIST_COUNT"] = 20;

if($this->StartResultCache(false, $USER->GetGroups()))
{
	$arResult=array(
		"ITEMS" => $arEntityFieldNames,
		"LIST_COUNT" => $arParams["LIST_COUNT"],
		"AJAX_HANDLER_URL" => $componentPath."/ajax-file.php"
	);
	
	$this->IncludeComponentTemplate();
}
?>
