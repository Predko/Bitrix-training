<?php
/**
*  file income.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
* 
* Таблица оплаты за услуги.
*/

namespace Predko\Customers;

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

use \Bitrix\Main\Entity\
{
    DataManager,
    IntegerField,
    StringField,
    DateField,
    BooleanField,
    Validator
};

use \Predko\Customers\Income\
{
    Income,
    Incomes
};

require_once(__DIR__."/entitytraits.php");

Loc::loadMessages(__FILE__);

include_once(__DIR__."/constants.php");

class IncomeTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'predko_customers_incomes';
    }

    public static function getObjectClass()
    {
        return Income::class;
    }

    public static function getCollectionClass()
    {
        return Incomes::class;
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
            
            new IntegerField ('CUSTOMER_ID', array(
                'required' => 'true',
            )),
            
            (new Reference('CUSTOMER',
                CustomerTable::class,
                Join::on('this.CUSTOMER_ID', 'ref.ID')))
                ->configureJoinType('inner'),
            
            new DateField ('DATE', array(
            )),
            
            new IntegerField ('NUMBER', array(
            )),
            
            new StringField ('VALUE', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,DECIMAL_LENGTH)
                    );
                },
            )),
            
            new BooleanField ('TYPE_OF_PAYMENT', array(
                'values' => array ('N','Y'),
            )),
        );
    }
}

?>