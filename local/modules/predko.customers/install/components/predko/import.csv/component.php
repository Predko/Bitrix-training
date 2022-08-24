<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Entity;

// include_once(__DIR__."../../constants.php");
include_once(__DIR__ . "/constants.php");

$tmp_data_file = $_SERVER['DOCUMENT_ROOT'] . TMP_FILE_TEMPLATE;

use \Predko\Customers\CustomerTable;
use \Predko\Customers\Customer\Customer;
use \Predko\Customers\Customer\Customers;
use \Bitrix\Main\Localization\Loc;

use Bitrix\Main\{
	Application,
	Context,
	Request,
	Server
};


if (!\Bitrix\Main\Loader::includeModule('predko.customers'))
{
	ShowError(GetMessage("PREDKO_CUSTOMERS_MODULE_NOT_INSTALLED"));
	return;
}

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

if (check_bitrix_sessid())
{
	debug($request->getPostList(), "request");

	return;
}

//debug($arParams);

if (!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 300;

$arTypesEntity = array(
	'Customer' => Loc::GetMessage("PREDKO_IMPORT_CSV_CUSTOMER_ENTITY_NAME"),
	'Contract' => Loc::GetMessage("PREDKO_IMPORT_CSV_CONTRACT_ENTITY_NAME"),
	'Income' => Loc::GetMessage("PREDKO_IMPORT_CSV_INCOME_ENTITY_NAME"),
	'Expense' => Loc::GetMessage("PREDKO_IMPORT_CSV_EXPENSE_ENTITY_NAME"),
);

// Preparing entity field names
$arEntityFieldNames = [];
foreach ($arTypesEntity as $name => $desc)
{
	$entityClassName = "\\Predko\\Customers\\" . $name . "Table";
	$fieldNames = $entityClassName::getFieldNames();

	$arEntityFieldNames[$name] = [
		'ENTITY_NAME' => $name,
		'ENTITY_DESC' => $desc,
		'FIELD_NAMES' => $fieldNames,
		'ENTITY_CLASS_NAME' => $entityClassName
	];
}

$arParams["LIST_COUNT"] = intval($arParams["LIST_COUNT"]);
if ($arParams["LIST_COUNT"] <= 0)
	$arParams["LIST_COUNT"] = 20;

// Проверяем, существует ли файл с данными CSV, для каждой сущности
// и заполняем данными о соответствии полей, для каждой сущности.

$arEntitiesFields = false;

foreach ($arTypesEntity as $nameEntity => $desc)
{
	// Возможно несоответствие имени сущности и имени файла CSV. 
	$hdrFileName = str_replace("filename", strtolower($nameEntity) . "_hdr", $tmp_data_file);

	$header = false;
	if (file_exists($hdrFileName))
	{
		$result = file_get_contents($hdrFileName);
		if ($result)
		{
			$header = json_decode($result, null, 512, JSON_OBJECT_AS_ARRAY);
		}
		
		$dataFileName = str_replace("filename", strtolower($nameEntity), $tmp_data_file);

		// Если данные получены полностью, создаём массив данных для шаблона.
		if (file_exists($dataFileName) && $header != false && $header[$nameEntity]['RECEIVED'] == true)
		{
			$arEntitiesFields[$nameEntity] = $header[$nameEntity]['FIELDS'];
		}
	}
}

if ($this->StartResultCache(false, $USER->GetGroups()))
{
	$arResult = array(
		"ITEMS" => $arEntityFieldNames,
		"LIST_COUNT" => $arParams["LIST_COUNT"],
		"AJAX_HANDLER_URL" => $componentPath . "/ajax-file.php",
		"ENTITIES_FIELDS" => $arEntitiesFields
	);

	$this->IncludeComponentTemplate();
}
