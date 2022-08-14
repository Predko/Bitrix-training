<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ENTITY_LIST_NAME"),
	"DESCRIPTION" => GetMessage("ENTITY_LIST_DESCRIPTION"),
	"ICON" => "/images/entity_list.jpg",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "predko_components",
		"SORT" => 2000,
		"NAME" => GetMessage("PREDKO_COMPONENTS"),
		"CHILD" => array(
			"ID" => "entity_list",
			"NAME" => GetMessage("ENTITY_LIST"),
			"SORT" => 10,
		),
	),
);


?>