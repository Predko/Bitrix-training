<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("IMPORT_CSV_NAME"),
	"DESCRIPTION" => GetMessage("IMPORT_CSV_DESCRIPTION"),
	"ICON" => "/images/import_csv.jpg",
	"SORT" => 10,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "predko_components",
		"SORT" => 2000,
		"NAME" => GetMessage("PREDKO_COMPONENTS"),
		"CHILD" => array(
			"ID" => "import_csv",
			"NAME" => GetMessage("IMPORT_CSV"),
			"SORT" => 10,
		),
	),
);


?>