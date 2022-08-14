<?php
/**
*  file index.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
*/

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

use Predko\Customers\CustomerTable;
use Predko\Customers\ContractTable;
use Predko\Customers\ExpenseTable;
use Predko\Customers\IncomeTable;

Loc::loadMessages(__FILE__);

//в названии класса пишем название директории нашего модуля, только вместо точки ставим нижнее подчеркивание
class predko_customers extends CModule
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
    $this->MODULE_ID = 'predko.customers';
    
    // название модуля
    $this->MODULE_NAME = Loc::getMessage('PREDKO_CUSTOMERS_MODULE_NAME');
    
    //описание модуля
    $this->MODULE_DESCRIPTION = Loc::getMessage('PREDKO_CUSTOMERS_MODULE_DESCRIPTION');
    
    //используем ли индивидуальную схему распределения прав доступа, мы ставим N, так как не используем ее
    $this->MODULE_GROUP_RIGHTS = 'N';
    
    //название компании партнера предоставляющей модуль
    $this->PARTNER_NAME = Loc::getMessage('PREDKO_CUSTOMERS_MODULE_PARTNER_NAME');
    $this->PARTNER_URI = Loc::getMessage('PREDKO_CUSTOMERS_MODULE_PARTNER_URI');//адрес вашего сайта
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
      $predko_module_customers = $configuration->get('predko_module_customers');
      $predko_module_customers['install'] = $predko_module_customers['install'] + 1;
      $configuration->add('predko_module_customers', $predko_module_customers);
      $configuration->saveConfiguration();
      #работа с .settings.php
    }
    else
    {
      $APPLICATION->ThrowExeption(Loc::getMessage("PREDKO_CUSTOMERS_MODULE_ERROR_VERSION_D7"));
    }
    
    $now = (new DateTime())->format("d-m-Y H:i:s");
    file_put_contents("111.txt", "\n\n".$now."\nInstall\t".$this->GetPath()."/install/step.php", FILE_APPEND);
    
    $APPLICATION->IncludeAdminFile(Loc::getMessage("PREDKO_CUSTOMERS_MODULE_INSTALL_TITLE"),
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
      $APPLICATION->IncludeAdminFile(Loc::getMessage("PREDKO_CUSTOMERS_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
    }
    elseif($request["step"] == 2)
    {
      if($request["savedata"] != "Y")
        $this->UnInstallDB();

      ModuleManager::unRegisterModule($this->MODULE_ID);

      #работа с .settings.php
      $configuration = Configuration::getInstance();
      $predko_module_customers = $configuration->get('predko_module_customers');
      $predko_module_customers['uninstall'] = $predko_module_customers['uninstall'] + 1;
      $configuration->add('predko_module_customers', $predko_module_customers);
      $configuration->saveConfiguration();
      #работа с .settings.php

      $APPLICATION->IncludeAdminFile(Loc::getMessage("PREDKO_CUSTOMERS_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
    }
  }
  
  //вызываем метод создания таблицы из выше подключенного класса
  public function installDB()
  {
    if (Loader::includeModule($this->MODULE_ID)) 
    {
      // CustomerTable
      $tableName = Base::getInstance('\Predko\Customers\CustomerTable')->getDBTableName();
      
      if (!Application::getConnection(\Predko\Customers\CustomerTable::getConnectionName())->isTableExists($tableName))
      {
        CustomerTable::getEntity()->createDbTable();
      }
      
      // ContractTable
      $tableName = Base::getInstance('\Predko\Customers\ContractTable')->getDBTableName();
      
      if (!Application::getConnection(\Predko\Customers\ContractTable::getConnectionName())->isTableExists($tableName))
      {
        ContractTable::getEntity()->createDbTable();
      }
      
      // ExpenseTable
      $tableName = Base::getInstance('\Predko\Customers\ExpenseTable')->getDBTableName();
      
      if (!Application::getConnection(\Predko\Customers\ExpenseTable::getConnectionName())->isTableExists($tableName))
      {
        ExpenseTable::getEntity()->createDbTable();
      }
      
      // IncomeTable
      $tableName = Base::getInstance('\Predko\Customers\IncomeTable')->getDBTableName();
      
      if (!Application::getConnection(\Predko\Customers\IncomeTable::getConnectionName())->isTableExists($tableName))
      {
        IncomeTable::getEntity()->createDbTable();
      }
    }
  }
  
  //вызываем метод удаления таблицы, если она существует
  public function uninstallDB()
  {
    if (Loader::includeModule($this->MODULE_ID)) 
    {
      // CustomerTable
      if (Application::getConnection()->isTableExists(Base::getInstance('\Predko\Customers\CustomerTable')->getDBTableName())) 
      {
        $connection = Application::getInstance()->getConnection();
        $connection->dropTable(CustomerTable::getTableName());
      }
      
      // ContractTable
      if (Application::getConnection()->isTableExists(Base::getInstance('\Predko\Customers\ContractTable')->getDBTableName())) 
      {
        $connection = Application::getInstance()->getConnection();
        $connection->dropTable(ContractTable::getTableName());
      }
      
      // ExpenseTable
      if (Application::getConnection()->isTableExists(Base::getInstance('\Predko\Customers\ExpenseTable')->getDBTableName())) 
      {
        $connection = Application::getInstance()->getConnection();
        $connection->dropTable(ExpenseTable::getTableName());
      }
      
      // IncomeTable
      if (Application::getConnection()->isTableExists(Base::getInstance('\Predko\Customers\IncomeTable')->getDBTableName())) 
      {
        $connection = Application::getInstance()->getConnection();
        $connection->dropTable(IncomeTable::getTableName());
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
        "D ".Loc::getMessage("PREDKO_CUSTOMERS_RIGHTS_DENIED"),
        "K ".Loc::getMessage("PREDKO_CUSTOMERS_RIGHTS_READ_COMPONENT"),
        "S ".Loc::getMessage("PREDKO_CUSTOMERS_RIGHTS_WRITE_SETTING"),
        "W ".Loc::getMessage("PREDKO_CUSTOMERS_RIGHTS_FULL")
      ) 
    );
  }
  
}

?>