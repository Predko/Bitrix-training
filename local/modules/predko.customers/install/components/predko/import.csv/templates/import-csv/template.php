<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? //debug($arResult);
?>

<?

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>

<div class="import-csv-tabs">
	<!-- Меню вкладок -->
	<div id="i-csv-tabs-nav">
		<button class="i-csv-tab-link active" onclick="showThisTab(event, 'get-file-and-fields')">
			<?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NAV_GET_FILE') ?>
		</button>
		<? foreach ($arResult["ENTITIES_FIELDS"] as $entityName => $fieldMapping) : ?>
			<button class="i-csv-tab-link" id="<?= $entityName ?>" onclick="enableTab(event, 'put-data-csv-to-db-<?= $entityName ?>')">
				<?= $entityName ?>
			</button>
		<? endforeach ?>
	</div>
	<!-- Меню вкладок -->

	<!-- Выбор файла CSV, выбор соответствия полей и передача файла на сервер. -->
	<div id="get-file-and-fields" class="i-csv-tab-content">
		<!-- <form id="file-csv-form" action="<?= POST_FORM_ACTION_URI ?>" method="post"> -->
		<form id="file-csv-form" action="<?= $arResult['AJAX_HANDLER_URL'] ?>" method="post">
			<input type="hidden" name="lang" value="<? echo LANGUAGE_ID ?>">
			<input type="hidden" name="type-form-data" value="initial">
			<?= bitrix_sessid_post() ?>

			<span style="white-space:nowrap">
				<!-- Выбор файла-->
				<label for="FILE_CSV"><?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FILE_DESC'); ?></label>
				&nbsp;&nbsp;
				<input type="file" name="FILE_CSV" id="input-file-csv" size="0" accept=".csv" onchange="getFieldNamesFromFile(this)">
				<!-- &ensp; -->
			</span>

			<!-- Выбор сущности-->
			<span style="white-space:nowrap">
				<label for="ENTITY_NAME"><?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_ENTITY_NAME'); ?></label>
				&nbsp;&nbsp;
				<select name="ENTITY_NAME" id="entity-name-fields-id" required="required" onchange='clearAndGetElement()'>
					<? foreach ($arResult["ITEMS"] as $name => $arItem) : ?>
						<option value="<?= $name ?>"><?= $arItem["ENTITY_DESC"] ?></option>
					<? endforeach ?>
				</select>
			</span>
			<br />
			<br />
			<h2 id="field_names_header" style="display: none;"><?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FIELD_NAMES_HEADER'); ?></h2><br />

			<!-- Здесь вставляется список имён полей в бд и импортируемом файле,
		     для выбора их соответствия.
		-->
			<ul id="place_to_select_field_names">
			</ul>

			<br /><br /><br /><br />

			<input type="submit" id="import_btn" disabled value="<?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_IMPORT') ?>">
		</form>
		<p id="i_csv_progress_info"></p>
	</div>
	<!-- Выбор файла CSV, выбор соответствия полей и передача файла на сервер. -->

	<!-- Добавление записей в базу данных -->
	<div id="put-data-csv-to-db" class="i-csv-tab-content" style="display: none;">
		<form id="put-data-to-csv-form" action="<?= $arResult['AJAX_HANDLER_URL'] ?>" method="post">
			<input type="hidden" name="lang" value="<? echo LANGUAGE_ID ?>">
			<input type="hidden" name="type-form-data" value="initial">
			<?= bitrix_sessid_post() ?>
			<h2 id="field_names_mapping"><?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_FIELD_NAMES_MAPPING'); ?></h2><br />

			<ul id="i_csv_fields_mapping">
				<!-- Здесь выводится список сопоставления полей(js) -->
			</ul>

			<br /><br /><br /><br />

			<input type="submit" id="del_data_db_btn" value="<?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NAV_PUT_INTO_DB') ?>">
			<input type="submit" id="set_data_db_btn" value="<?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NAV_PUT_INTO_DB') . "entity" ?>">
		</form>
	</div>
	<!-- Добавление записей в базу данных -->
	
	<!-- ProgressBar -->
	<p id="i_csv_progress_info"></p>

	<div class="i_csv_progress_container">
		<progress id="i_csv_progress" min="0" max="100" hidden></progress>
	</div>
	<!-- ProgressBar -->

	<script>
		function getFieldNamesFromFile(input) {
			items = <?= json_encode($arResult["ITEMS"]) ?>;
			_getFieldNamesFromFile(input, items,
				"<?= Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NOT_USED') ?>");
		}
	</script>
</div>

<? $this->addExternalJS($templateFolder . "\js\import_csv.js"); ?>