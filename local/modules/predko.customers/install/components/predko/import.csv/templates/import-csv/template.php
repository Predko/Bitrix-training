<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?//debug($arResult);?>

<?
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

<div class="news-line">
	<form id="file-csv-form" action="<?=POST_FORM_ACTION_URI?>" method="post">
		<?=bitrix_sessid_post()?>	
		<input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>">
		
		<!-- Выбор сущности-->
		<label for="ENTITY_NAME"><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_ENTITY_NAME');?></label>
		<select name="ENTITY_NAME" id="entity-name-fields-id" required="required"
					onchange='clearAndGetElement()'>
			<?foreach($arResult["ITEMS"] as $name => $arItem):?>
				<option value="<?=$name?>"><?=$arItem["ENTITY_DESC"]?></option>
		  <?endforeach?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		
		<!-- Выбор файла-->
		<label for="FILE_CSV"><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FILE_DESC');?></label>
		<input type="file" name="FILE_CSV" class="input-file-csv" 
				size="0" accept=".csv" onchange="getFieldNamesFromFile(this)">
		<br />
		<br />
		<h2 id="field_names_header" style="display: none;"><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FIELD_NAMES_HEADER');?></h2><br />
		<!-- Здесь вставляется список имён полей в бд и импортируемом файле,
		     для выбора их соответствия.
		-->
		<ul id="place_to_select_field_names">
		</ul>
		
		<br />
		<br />
		<br />
		<br />
		<input type="submit" value="<?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_IMPORT')?>">
	</form>
	<script>
		function getFieldNamesFromFile(input) 
		{
			items = <?=json_encode($arResult["ITEMS"])?>;
			_getFieldNamesFromFile(input, items, 
				"<?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NOT_USED')?>");
		}
	</script>
</div>

<?$this->addExternalJS($templateFolder."\js\scripts.js");?>

