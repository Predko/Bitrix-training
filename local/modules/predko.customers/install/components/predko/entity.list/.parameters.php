<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

include_once(__DIR__."/constants.php");

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule('predko.customers');

Loc::loadMessages(__FILE__);

$arTypesEntity = array (
	'Customer' => Loc::GetMessage("PREDKO_ENTITY_LIST_CUSTOMER_ENTITY_NAME"),
	'Contract' => Loc::GetMessage("PREDKO_ENTITY_LIST_CONTRACT_ENTITY_NAME"),
	'Income' => Loc::GetMessage("PREDKO_ENTITY_LIST_INCOME_ENTITY_NAME"),
	'Expense' => Loc::GetMessage("PREDKO_ENTITY_LIST_EXPENSE_ENTITY_NAME"),
);

$defaultEntityName = $arCurrentValues["ENTITY_NAME"];

if ($defaultEntityName == "")
{
	$defaultEntityName = DEFAULT_ENTITY_NAME;
}

$currentEntityTable = "\\Predko\\Customers\\" . $defaultEntityName . "Table";

$defaultEntityFields = $currentEntityTable::getFieldNames();

$defaultURL = "/#ENTITY_NAME#/#ENTITY_NAME#_detail.php?ID=#ENTITY_ID#";

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"ENTITY_NAME"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PREDKO_ENTITY_LIST_ENTITY_DESC_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEntity,
			"DEFAULT" => DEFAULT_ENTITY_NAME,
			"REFRESH" => "Y",
		),
		"ENTITY_FIELDS"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PREDKO_ENTITY_LIST_ENTITY_DESC_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $defaultEntityFields,
			"DEFAULT" => array("ID"),
			"MULTIPLE" => "Y",
		),
		"ENTITY_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PREDKO_ENTITY_LIST_ENTITY_DESC_LIST_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"DETAIL_URL" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => GetMessage("PREDKO_ENTITY_LIST_ENTITY_DETAIL_URL"),
			"TYPE" => "STRING",
			"DEFAULT" => $defaultURL,
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>300),
	),
);


?>
