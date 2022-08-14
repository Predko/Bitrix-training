<?php
/**
*  file menu.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
*/

//подключаем класс и файлы локализации
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

//добавляем пункт меню для нашего модуля
$menu = array(
    array(
        'parent_menu' => 'global_menu_content',//определяем место меню, в данном случае оно находится в главном меню
        'sort' => 400,//сортировка, в каком месте будет находится наш пункт
        'text' => Loc::getMessage('PREDKO_CUSTOMERS_MENU_TITLE'),//описание из файла локализации
        'title' => Loc::getMessage('PREDKO_CUSTOMERS_MENU_TITLE'),//название из файла локализации
        'url' => 'predko_customers_index.php',//ссылка на страницу из меню
        'items_id' => 'menu_references',//описание подпункта, то же, что и ранее, либо другое, можно вставить сколько угодно пунктов меню
        'items' => array(
            array(
                'text' => Loc::getMessage('PREDKO_CUSTOMERS_SUBMENU_TITLE'),
                'url' => 'predko_customers_index.php?lang=' . LANGUAGE_ID,
                'more_url' => array('predko_customers_index.php?lang=' . LANGUAGE_ID),
                'title' => Loc::getMessage('PREDKO_CUSTOMERS_SUBMENU_TITLE'),
            ),
        ),
    ),
);

return $menu;

?>