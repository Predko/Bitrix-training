<?php

/**
 * file ajax-file.php
 * Created by Visual Studio Code
 * User: Victor Predko
 * predko.victor@gmail.com
 * 14-08-2022
 */

//Подключаем ядро 1С Битрикс
require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);


use Bitrix\Main\{
    Application,
    // Context, 
    // Request, 
    // Server
};

global $APPLICATION;

$request = Application::getInstance()->getContext()->getRequest();

if (!check_bitrix_sessid())       // проверка идентификатора сессии
{
    echo json_encode(['error' => Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_SESS_ID_ERROR')]);
}

// Это исходные данные для импорта.
if ($request['type-form-data'] == 'initial') 
{
    $session = Application::getInstance()->getSession();

    $fields = [];
    foreach ($request['ENTITY_NAME_TABLE'] as $index => $field) 
    {
        $fields[$field] = $request['ENTITY_NAME_CSV'][$index];
    }

    $session['FIELDS_DB_CSV'] = $fields;
    
    $session['CURRENT_FILE_SIZE'] = 0;
    $session['FILE_SIZE'] = $request['file-size'];
    
    file_put_contents(
        "formData.php","", true);
        // print_r($request->getPostList()
    

    echo json_encode(['result' => 'ok']);

    return;
} 
else // Это исходные данные для импорта?
if ($request['type-form-data'] == 'get_file') 
{
    file_put_contents(
        "formData.php",
        print_r("{<".$request['file-index-part']."><".$request['file-data'].">}", true),
        FILE_APPEND
    );

    $session = Application::getInstance()->getSession();

    $blobSize = intval($request['blob-size']);

    $session['CURRENT_FILE_SIZE'] = intval($session['CURRENT_FILE_SIZE']) + $blobSize;
    
    if (intval($session['CURRENT_FILE_SIZE']) == intval($session['FILE_SIZE']))
        echo json_encode(['result' => 'end']);
    else
        echo json_encode(['result' => 'ok']);
    

    // Проверяем, были ли сохранены поля БД и файла CSV
    if (!$session->has('FIELDS_DB_CSV')) 
    {
        echo "{'result': 'error', 'error': '" . Loc::getMessage('PREDKO_CUSTOMERS_IMPORT_CSV_NO_FIELDS_ERROR') . "'}";
        return;
    }

    $fields = $session['FIELDS_DB_CSV'];

    foreach ($fields as $db => $csv) {
    }

    return;
}


echo json_encode(['error' => '', 'success' => 'PREDKO_CUSTOMERS_IMPORT_CSV_SESS_ID_ERROR']);

// file_put_contents(
//     "formData.php",
//     print_r("request->getValues()=" . var_export($request->getValues(), true), true),
//     FILE_APPEND
// );
