<?php
/**
* file ajax-file.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 14-08-2022
*/

use Bitrix\Main\
{   Application, 
    Context, 
    Request, 
    Server
};

global $APPLICATION;

echo "1234567890";

// $context = Application::getInstance()->getContext();
// $request = $context->getRequest();
// переменная класса для работы
// $request = \Bitrix\Main\Context::getCurrent()->getRequest();

echo "1234567890";

//debug($context);
debug($_REQUEST);

debug($_POST["file-csv"], "file-csv");

if( check_bitrix_sessid()       // проверка идентификатора сессии
)
{
    debug($_POST["file-csv"], "file-csv");

}






?>