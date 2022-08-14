<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?debug($arParams);?>

<div class="news-line">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<span class="news-date-time"><?echo $arItem["UNP"]?>&nbsp;&nbsp;</span><a href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a><br />
	<?endforeach;?>
</div>
