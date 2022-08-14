<?php
/**
* file unstep2.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 04-08-2022
*/


use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
	return;

#работа с .settings.php
$uninstall_count = \Bitrix\Main\Config\Configuration::getInstance()->get('predko_module_adress');
#работа с .settings.php

if ($ex = $APPLICATION->GetException())
	echo CAdminMessage::ShowMessage(array(
		"TYPE" => "ERROR",
		"MESSAGE" => Loc::getMessage("MOD_UNINST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true,
	));
else
	echo CAdminMessage::ShowNote(Loc::getMessage("MOD_UNINST_OK"));

echo CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("PREDKO_ADRESS_UNINSTALL_COUNT").$uninstall_count['uninstall'],"TYPE"=>"OK"));

?>
<form action="<?echo $APPLICATION->GetCurPage(); ?>">
	<input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>">
	<input type="submit" name="" value="<?echo Loc::getMessage("MOD_BACK"); ?>">
<form>