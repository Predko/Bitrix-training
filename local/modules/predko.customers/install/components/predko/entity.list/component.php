<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Entity;

include_once(__DIR__."../../constants.php");

use \Predko\Customers\CustomerTable;
use \Predko\Customers\Customer\Customer;
use Predko\Customers\Customer\Customers;

if (! \Bitrix\Main\Loader::includeModule('predko.customers'))
{
	ShowError(GetMessage("PREDKO_CUSTOMERS_MODULE_NOT_INSTALLED"));
	return;
}

debug($arParams);

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;

// Entity name preparation
$arParams["ENTITY_NAME"] = trim($arParams["ENTITY_NAME"]);

if(strlen($arParams["ENTITY_NAME"])<=0)
 	$arParams["ENTITY_NAME"] = DEFAULT_ENTITY_NAME;
else
if($arParams["ENTITY_NAME"]=="-")
	$arParams["ENTITY_NAME"] = "";

// Preparing entity field names
if(!is_array($arParams["ENTITY_FIELDS"]))
	$arParams["ENTITY_FIELDS"] = array($arParams["ENTITY_FIELDS"]);

$entityClassName = "\\Predko\\Customers\\".$arParams["ENTITY_NAME"]."Table";
$fieldNames = $entityClassName::getFieldNames();

if (count($arParams["ENTITY_FIELDS"]) == 0)
{
	$arParams["ENTITY_FIELDS"] = $fieldNames;
}

foreach($arParams["ENTITY_FIELDS"] as $k=>$v)
{
	$arParams["ENTITY_FIELDS"][$k] = $fieldNames[$v];
}

$arParams["ENTITY_COUNT"] = intval($arParams["ENTITY_COUNT"]);
if($arParams["ENTITY_COUNT"]<=0)
	$arParams["ENTITY_COUNT"] = 20;

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
if(strlen($arParams["DETAIL_URL"])<=0)
	$arParams["DETAIL_URL"] = "/#ENTITY_NAME#/#ENTITY_NAME#_detail.php?ID=#ENTITY_ID#";


if($this->StartResultCache(false, $USER->GetGroups()))
{
	$arSelect = $arParams["ENTITY_FIELDS"];
	
	$arFilter = array (
	);
	
	$arOrder = array(
		"ID" => "ASC",
	);
	
	$arResult=array(
		"ITEMS"=>array(),
	);
	
	$query = new Entity\Query($entityClassName::getEntity());

	file_put_contents("d:\\component_query.php", $entityClassName);
		
	$query->setSelect($arSelect)
		->setOrder($arOrder)
		->setOffset(0)
		->setLimit($arParams["ENTITY_COUNT"]);
	
	file_put_contents("d:\\component_query.php", "\n".$query->getQuery(), FILE_APPEND);

	$rsItems = $query->exec();
	
	foreach ($rsItems as $id => $arItem) 
	{
		$arItem["DETAIL_PAGE_URL"] = htmlspecialchars(str_replace(
			array("#SERVER_NAME#", "#SITE_DIR#", "#ENTITY_NAME#", "#ENTITY_ID#"),
			array(SITE_SERVER_NAME, SITE_DIR, strtolower($arParams["ENTITY_NAME"]), $arItem["ID"]),
			$arParams["DETAIL_URL"]
		));
		$arResult["ITEMS"][]=$arItem;

		file_put_contents("d:\\component_query.php", print_r($arItem, true), FILE_APPEND);
	}
	
	$this->IncludeComponentTemplate();
}
?>
