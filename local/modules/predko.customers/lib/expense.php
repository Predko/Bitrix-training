<?php
/**
*  file expenses.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
* Таблица расходов с расчётного счёта.
*/

namespace Predko\Customers;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity\
{
    DataManager,
    IntegerField,
    StringField,
    DateField,
    Validator
};

use \Predko\Customers\Expense\
{
    Expense,
    Expenses
};

use Bitrix\Main\Type;

require_once(__DIR__."/entitytraits.php");

Loc::loadMessages(__FILE__);

include_once(__DIR__."/constants.php");

class ExpenseTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'predko_customers_expenses';
    }
    
    public static function getObjectClass()
    {
        return Expense::class;
    }

    public static function getCollectionClass()
    {
        return Expenses::class;
    }

    use EntityTraits;

    // создаем поля таблицы
    public static function getMap()
    {
        return array(
            new IntegerField ('ID', array(
                'primary' => 'true',
                'autocomplete' => 'true',
            )),
            new IntegerField ('NUMBER', array(
            )),
            new DateField ('DATE', array(
            )),
            new StringField ('VALUE', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,DECIMAL_LENGTH)
                    );
                },
            )),
            new StringField ('COMMENT', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,10)
                    );
                },
            )),
        );
    }
}

?>