<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="container text-center">
	<h2 class="mb-0"><?=$arParams["PAGER_TITLE"]?></h2>
  	<hr class="divider my-4">
	<?//echo "<pre>" . print_r($arResult["ITEMS"], true) . "</pre>";?>
	<?//debug($arParams);?>
</div>
  
<div class="container text-center">
	<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?><br />
	<?endif;?>
	<div class="row justify-content-center">
		<?foreach ($arResult["ITEMS"] as $arItem):?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
			<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arItem["PREVIEW_PICTURE"])):?>
				<div class="col-lg-4 text-center news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="card bg-secondary border border-dark">
						<img class="card-img-top" src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" 
							alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>">
						<div class="card-body ">
						<h5 class="card-title"><?=$arItem["PREVIEW_PICTURE"]["TITLE"]?></h5>
						<p class="card-text">
						<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
							<?echo $arItem["PREVIEW_TEXT"];?>
						<?endif;?>
						</p>
						<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-primary">
								<?echo $arItem["NAME"]?></a>
						<?else:?>
							<?echo $arItem["NAME"]?></a>
						<?endif?>
						</div>
					</div>
				</div>
			<?else:?>
				<div class="col-lg-4 text-center news-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
					<div class="card bg-secondary border border-dark">
						<div class="card-body ">
						<h5 class="card-title"><?=$arItem["NAME"]?>?></h5>
						<p class="card-text">
						<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arItem["PREVIEW_TEXT"]):?>
							<?echo $arItem["PREVIEW_TEXT"];?>
						<?endif;?>
						</p>
						<?if(!$arParams["HIDE_LINK_WHEN_NO_DETAIL"] || ($arItem["DETAIL_TEXT"] && $arResult["USER_HAVE_ACCESS"])):?>
							<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-primary">
								<?echo $arItem["NAME"]?></a>
						<?else:?>
							<?echo $arItem["NAME"]?></a>
						<?endif?>
						</div>
					</div>
				</div>
			<?endif?>
		<?endforeach?>
	</div>
	<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<br /><?=$arResult["NAV_STRING"]?>
	<?endif;?>
</div>

