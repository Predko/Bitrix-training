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


use Bitrix\Main\
{   Application, 
    Context, 
    Request, 
    Server
};

global $APPLICATION;

echo "1234567890";

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
// переменная класса для работы
// $request = \Bitrix\Main\Context::getCurrent()->getRequest();

//debug($context);
debug($request);

if( check_bitrix_sessid()       // проверка идентификатора сессии
)
{
    file_put_contents("formData.php", print_r("request->getValues()=" . var_export($request->getValues(), true), true));

}






?>