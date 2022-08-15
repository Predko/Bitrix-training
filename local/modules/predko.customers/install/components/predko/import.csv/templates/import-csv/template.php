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
		<select name="ENTITY_NAME" id="entity-name-fields-id" required="required">
			<?foreach($arResult["ITEMS"] as $name => $arItem):?>
				<option value="<?=$name?>"><?=$arItem["ENTITY_DESC"]?></option>
		  <?endforeach?>
		</select><br />
		
		<!-- Выбор файла-->
		<label for="FILE_CSV"><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FILE_DESC');?></label>
		<input type="file" name="FILE_CSV" class="input-file-csv" 
				size="0" accept=".csv" onchange="getFieldNamesFromFile(this)">
		
		<input type="submit" value="<?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_GET_FIELD_NAMES')?>">
	</form>
	<script>
		function getFieldNamesFromFile(input) 
		{
			let file = input.files[0];

			if (file.type != "text/csv")
			{
				alert("Need a text/csv file!");
				
				return;
			};
			
			console.log(file.name, file.type, file.size);
			
			var blob = file.slice(0, 500);
			
			let reader = new FileReader();

			reader.readAsText(blob);

			reader.onload = function() {
				createFieldName(reader.result);
			};

			reader.onerror = function() {
				console.log(reader.error);
			};

			function createFieldName(str) {
				var str = reader.result;
				// Выделяем первую строку
				var strArr = str.split("\r",1);
				// Разбиваем на поля
				var fieldNames = strArr[0].split(";");
				console.log(fieldNames);
				// Формируем id элемента в зависимости от выбранного значения в entity-name-fields-id
				// var efn_id = document.getElementById("entity-name-fields-id").value + "-field-names";
				// // Получем массив полей выбранного элемента
				// var entityFieldNames = document.getElementById(efn_id).name;
				var efn = document.getElementById("entity-name-fields-id").value;
				var items = <?=json_encode($arResult["ITEMS"])?>;
				console.log(items);
				
				var entityFieldNames = items[efn]["FIELD_NAMES"];

				alert(entityFieldNames);
			}
		}
	</script>
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<span><?=$arItem["ENTITY_NAME"]?>&nbsp;&nbsp;<?=$arItem['ENTITY_CLASS_NAME']?></span><br />
		<?foreach ($arItem['FIELD_NAMES'] as $fieldName):?>
			<span><?=$fieldName?>&nbsp;&nbsp;</span>
		<?endforeach;?>
		<br />
		<br />
	<?endforeach;?>
</div>
<!-- 
	<form id="the-add-form" action="/test/add/customer.php" method="post">
    <label for="file-csv">Импортировать из файла</label>
    <input type="file" name="file-csv" class="input-file-csv" size="0">
    <label for="UNP">УНП организации</label>
    <input type="number" name="UNP">
    <input name="form_file" class="inputfile1" size="0" type="file">
    <input type="submit" value="Добавить">
</form>
 -->
