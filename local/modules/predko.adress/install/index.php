<?php
//подключаем основные классы для работы с модулем
use Bitrix\Main\
{
  Application,
  Loader,
  Entity\Base,
  Localization\Loc,
  ModuleManager,
  Config\Configuration
};

//в данном модуле создадим адресную книгу, и здесь мы подключаем класс, который создаст нам эту таблицу
use Predko\Adress\AdressTable;

Loc::loadMessages(__FILE__);

//в названии класса пишем название директории нашего модуля, только вместо точки ставим нижнее подчеркивание
class predko_adress extends CModule
{
  public function __construct()
  {
    $arModuleVersion = array();
    
    //подключаем версию модуля (файл будет следующим в списке)
    include (__DIR__ . '/version.php');
    
    //присваиваем свойствам класса переменные из нашего файла
    if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) 
    {
      $this->MODULE_VERSION = $arModuleVersion['VERSION'];
      $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
    }
    
    //пишем название нашего модуля как и директории
    $this->MODULE_ID = 'predko.adress';
    
    // название модуля
    $this->MODULE_NAME = Loc::getMessage('PREDKO_ADRESS_MODULE_NAME');
    
    //описание модуля
    $this->MODULE_DESCRIPTION = Loc::getMessage('PREDKO_ADRESS_MODULE_DESCRIPTION');
    
    //используем ли индивидуальную схему распределения прав доступа, мы ставим N, так как не используем ее
    $this->MODULE_GROUP_RIGHTS = 'N';
    
    //название компании партнера предоставляющей модуль
    $this->PARTNER_NAME = Loc::getMessage('PREDKO_ADRESS_MODULE_PARTNER_NAME');
    $this->PARTNER_URI = Loc::getMessage('PREDKO_ADRESS_MODULE_PARTNER_NAME');//адрес вашего сайта
  }
  
  //здесь мы описываем все, что делаем до инсталляции модуля, мы добавляем наш модуль в регистр и вызываем метод создания таблицы
  public function doInstall()
  {
    global $APPLICATION;

    if ($this->isVersionD7())
    {
      ModuleManager::registerModule($this->MODULE_ID);
      
      $this->installDB();

      #работа с .settings.php
      $configuration = Configuration::getInstance();
      $predko_module_adress = $configuration->get('predko_module_adress');
      $predko_module_adress['install'] = $predko_module_adress['install'] + 1;
      $configuration->add('predko_module_adress', $predko_module_adress);
      $configuration->saveConfiguration();
      #работа с .settings.php
    }
    else
    {
      $APPLICATION->ThrowExeption(Loc::getMessage("PREDKO_ADRESS_MODULE_ERROR_VERSION_D7"));
    }
    
    $now = (new DateTime())->format("d-m-Y H:i:s");
    file_put_contents("111.txt", "\n\n".$now."\nInstall\t".$this->GetPath()."/install/step.php", FILE_APPEND);
    
    $APPLICATION->IncludeAdminFile(Loc::getMessage("PREDKO_ADRESS_MODULE_INSTALL_TITLE"),
      $this->GetPath()."/install/step.php");
  }
  
  //вызываем метод удаления таблицы и удаляем модуль из регистра
  public function doUninstall()
  {
    global $APPLICATION;

    $context = Application::getInstance()->getContext();
    $request = $context->getRequest();

    if($request["step"] < 2)
    {
      $now = (new DateTime())->format("d-m-Y H:i:s");
      
      file_put_contents("111.txt", "\n\n".$now."\nUninstall\t".$this->GetPath()."/install/unstep1.php", FILE_APPEND);

      $APPLICATION->IncludeAdminFile(Loc::getMessage("PREDKO_ADRESS_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
    }
    elseif($request["step"] == 2)
    {
      if($request["savedata"] != "Y")
        $this->UnInstallDB();

      ModuleManager::unRegisterModule($this->MODULE_ID);

      file_put_contents("111.txt", "\nUninstall\t".$this->GetPath()."/install/unstep2.php", FILE_APPEND);

      #работа с .settings.php
      $configuration = Configuration::getInstance();
      $predko_module_adress = $configuration->get('predko_module_adress');
      $predko_module_adress['uninstall'] = $predko_module_adress['uninstall'] + 1;
      $configuration->add('predko_module_adress', $predko_module_adress);
      $configuration->saveConfiguration();
      #работа с .settings.php

      $APPLICATION->IncludeAdminFile(Loc::getMessage("PREDKO_ADRESS_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
    }
  }
  
  //вызываем метод создания таблицы из выше подключенного класса
  public function installDB()
  {
    if (Loader::includeModule($this->MODULE_ID)) 
    {
      $tableName = Base::getInstance('\Predko\Adress\AdressTable')->getDBTableName();
      
      if (!Application::getConnection(\Predko\Adress\AdressTable::getConnectionName())->isTableExists($tableName))
      {
          AdressTable::getEntity()->createDbTable();
      }
    }
  }
  
  //вызываем метод удаления таблицы, если она существует
  public function uninstallDB()
  {
    if (Loader::includeModule($this->MODULE_ID)) 
    {
      if (Application::getConnection()->isTableExists(Base::getInstance('\Predko\Adress\AdressTable')->getDBTableName())) 
      {
        $connection = Application::getInstance()->getConnection();
        $connection->dropTable(AdressTable::getTableName());
      }
    }
  }

  //Проверяем что система поддерживает D7
  public function isVersionD7()
  {
      return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
  }
  
  //Определяем место размещения модуля
  public function GetPath($notDocumentRoot=false)
  {
    if($notDocumentRoot)
      return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__));
    else
      return dirname(__DIR__);
  }
  
  public function GetModuleRightList()
  {
    return array(
      'reference_id' => array ("D", "K", "S", "W"),
      'reference' => array (
        "D ".Loc::getMessage("PREDKO_ADRESS_RIGHTS_DENIED"),
        "K ".Loc::getMessage("PREDKO_ADRESS_RIGHTS_READ_COMPONENT"),
        "S ".Loc::getMessage("PREDKO_ADRESS_RIGHTS_WRITE_SETTING"),
        "W ".Loc::getMessage("PREDKO_ADRESS_RIGHTS_FULL")
      ) 
    );
  }
  
}