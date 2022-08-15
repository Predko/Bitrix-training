<?php
/**
* file class.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 14-08-2022
* 
* 
*/

/**
 * Класс компонента обеспечивающего импорт записей из файла CSV
 * в выбранную сущность(таблицу БД).
 * Предоставляется возможность выбора импортируемых полей 
 * и соответствия их в сущности и CSV файле.
 */
class CImportFromCSV extends CBitrixComponent
{
    // Подготовка параметров компонента.
    public function onPrepareComponentParams($arParams)
    {
        $result = array(
            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
            "CACHE_TIME" => isset($arParams["CACHE_TIME"]) ?$arParams["CACHE_TIME"]: 36000000,
        );
        return $result;
    }


    


    // public function executeComponent()
    // {
    //     if($this->startResultCache())//startResultCache используется не для кеширования html, а для кеширования arResult
    //     {
    //         $this->arResult["Y"] = $this->sqr($this->arParams["X"]);
    //         $this->includeComponentTemplate();
    //     }
    //     return $this->arResult["Y"];
    // }

    
}






?>