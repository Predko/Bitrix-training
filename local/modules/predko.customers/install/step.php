<?php
/**
* file step.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
*/

use Bitrix\Main\Localization\Loc;

global $APPLICATION;

if (!check_bitrix_sessid())
{
    return;
}

#работа с .settings.php
$install_count=\Bitrix\Main\Config\Configuration::getInstance()->get('predko_module_customers');

//$cache_type=\Bitrix\Main\Config\Configuration::getInstance()->get('cache');
#работа с .settings.php

if ($ex = $APPLICATION->GetException())
{
    echo CAdminMessage::ShowMessage(array (
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::GetMessage("MOD_INST_ERR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true
    ));
}
else
{
    echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));
}

echo CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("PREDKO_CUSTOMERS_INSTALL_COUNT").$install_count['install'],"TYPE"=>"OK"));
?>

<form action="<?= $APPLICATION->GetCurPage();?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID;?>">
    <input type="submit" name="" value="<?= Loc::getMessage("MOD_BACK")?>">
</form>




