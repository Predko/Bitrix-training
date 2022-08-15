<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

\Bitrix\Main\Loader::includeModule('predko.customers');

//Loc::loadMessages(__FILE__);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS"  =>  array(
		"LIST_COUNT"  =>  Array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PREDKO_IMPORT_CSV_LIST_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>300),
	),
);


?>
