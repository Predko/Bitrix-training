<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?debug($arResult);?>

<div class="news-line">
	<?foreach ($arResult["ITEMS"] as $arItem):?>
		<span><?=$arItem["ENTITY_NAME"]?>&nbsp;&nbsp;<?=$arItem['ENTITY_CLASS_NAME']?></span><br />
		<?foreach ($arItem['FIELD_NAMES'] as $fieldName):?>
			<span><?=$fieldName?>&nbsp;&nbsp;</span>
		<?endforeach;?>
		<br />
		<br />
	<?endforeach;?>
</div>
