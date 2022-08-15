<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Импорт из CSV");
?>
<?$APPLICATION->IncludeComponent(
	"predko:import.csv", 
	"import-csv", 
	array(
		"CACHE_TIME" => "300",
		"CACHE_TYPE" => "A",
		"ENTITY_COUNT" => "20",
		"COMPONENT_TEMPLATE" => "import-csv",
		"LIST_COUNT" => "20"
	),
	false
);?>
<br>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>