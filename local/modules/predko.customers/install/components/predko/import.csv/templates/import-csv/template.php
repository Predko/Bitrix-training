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
					onchange='clearAndGetElementById("place_to_select_field_names")'>
			<?foreach($arResult["ITEMS"] as $name => $arItem):?>
				<option value="<?=$name?>"><?=$arItem["ENTITY_DESC"]?></option>
		  <?endforeach?>
		</select><br />
		
		<!-- Здесь вставляется список имён полей в бд и импортируемом файле,
		     для выбора их соответствия.
		-->
		<ul id="place_to_select_field_names">
		</ul>
		
		<!-- Выбор файла-->
		<label for="FILE_CSV"><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FILE_DESC');?></label>
		<input type="file" name="FILE_CSV" class="input-file-csv" 
				size="0" accept=".csv" onchange="getFieldNamesFromFile(this)">
		
		<input type="submit" value="<?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_GET_FIELD_NAMES')?>">
	</form>
	<script>
		function getFieldNamesFromFile(input) 
		{
			getFieldNamesFromFile(input, "<?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NOT_USED')?>");
			// let file = input.files[0];

			// if (file.type != "text/csv")
			// {
			// 	alert("Need a text/csv file!");
				
			// 	return;
			// };
			
			// var blob = file.slice(0, 500);
			
			// let reader = new FileReader();

			// reader.readAsText(blob);

			// reader.onload = function() {
			// 	createFieldName(reader.result);
			// };

			// reader.onerror = function() {
			// 	console.log(reader.error);
			// };

			// function createFieldName(str) {
			// 	var str = reader.result;
			// 	// Выделяем первую строку
			// 	var strArr = str.split("\r",1);
			// 	// Разбиваем на поля
			// 	var fieldNames = strArr[0].split(";");
			// 	console.log(fieldNames);
			// 	// Получаем список полей выбранной сущности.
			// 	var efn = document.getElementById("entity-name-fields-id").value;
			// 	var entityFieldNames = <?=json_encode($arResult["ITEMS"])?>[efn]["FIELD_NAMES"];
			// 	console.log(entityFieldNames);
				
			// 	createListfieldMatches(entityFieldNames, fieldNames);
			// }

			// function createListfieldMatches(entityFieldNames, fieldNames) 
			// {
			// 	// Число полей в списке.
			// 	var length = fieldNames.length;
			// 	if (entityFieldNames.length > length)
			// 	{
			// 		length = entityFieldNames.length;
			// 	}

			// 	var ul = clearAndGetElementById("place_to_select_field_names");
			// 	if (ul == null)
			// 	{
			// 		alert("Не удалось создать список соответствий полей");
			// 		return;
			// 	}

			// 	for (let i = 0; i < length; i++) 
			// 	{
			// 		var used = false;
			// 		var selected = '""';

			// 		let liLast = document.createElement('li');
			// 		var selectStr = '<select name="ENTITY_NAME_TABLE" required="required">';
			// 		entityFieldNames.forEach((element,indx) => {
			// 			if (typeof indx !== 'undefined' && i == indx)
			// 			{
			// 				used = true;
			// 				selected = ' selected="selected"';
			// 			}
			// 			else
			// 				selected = '';
						
			// 			selectStr += '<option value="' + element + '"' + selected + '>' + element + '</option>';
			// 			console.log(i,indx);
			// 		});
					
			// 		selected = (!used) ? ' selected="selected"' : '';
					
			// 		selectStr += '<option value="not used"' + selected + '><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NOT_USED')?></option><br />';
			// 		selectStr += '</select>&nbsp;&nbsp;&nbsp;\
			// 		<select name="ENTITY_NAME_CSV" required="required">';
			// 		used = false;
			// 		fieldNames.forEach((element,indx) => {
			// 			if (typeof indx !== 'undefined' && i == indx)
			// 			{
			// 				used = true;
			// 				selected = ' selected="selected"';
			// 			}
			// 			else
			// 				selected = '';
						
			// 			selectStr += '<option value="' + element + '"' + selected + '>' + element + '</option><br />';
			// 		});
					
			// 		selected = (!used) ? ' selected="selected"' : '';
					
			// 		selectStr += '<option value="not used"' + selected + '><?=Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NOT_USED')?></option><br />';
			// 		selectStr += '</select><br />'; 

			// 		liLast.innerHTML = selectStr;
			// 		console.log(selectStr);

			// 		ul.append(liLast); // вставить liLast в конец <ul>
			// 	}
			// }
		}

		// Очищает содержимое элемента по id.
		// Возвращает найденный элемент.
		// function clearAndGetElementById(elementID)
		// {
		// 	var element = document.getElementById(elementID);
		// 	element.innerHTML = "";
		// 	return element;
		// }
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
