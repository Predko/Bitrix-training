<?php
/**
*  file contracts.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
* 
* Таблица договоров.
*/

namespace Predko\Customers;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity\
{
    DataManager,
    IntegerField,
    StringField,
    DateField,
    BooleanField,
    Validator,
};
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

use \Predko\Customers\Contract\
{
    Contract,
    Contracts
};

require_once(__DIR__."/entitytraits.php");

Loc::loadMessages(__FILE__);

include_once(__DIR__."/constants.php");

class ContractTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'predko_customers_contracts';
    }

    public static function getObjectClass()
    {
        return Contract::class;
    }

    public static function getCollectionClass()
    {
        return Contracts::class;
    }

    use EntityTraits;

    // создаем поля таблицы
    public static function getMap()
    {
        return array(
            new IntegerField ('ID', array(
                'primary' => 'true',
                'autocomplete' => 'true',
                'required' => 'true',
            )),
            
            new IntegerField ('CUSTOMER_ID', array(
                'required' => 'true'
            )),
            
            (new Reference('CUSTOMER',
                CustomerTable::class,
                Join::on('this.CUSTOMER_ID', 'ref.ID')))
                ->configureJoinType('inner'),
            
            new IntegerField ('NUMBER', array(
                'required' => 'true',
            )),
            
            new DateField ('DATE', array(
                'required' => 'true',
            )),
            
            new StringField ('PRICE', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,DECIMAL_LENGTH)
                    );
                },
            )),
            
            new StringField ('PREPAYMENT', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,DECIMAL_LENGTH)
                    );
                },
            )),
            
            new BooleanField ('AVAILABLE', array(
                'values' => array('N','Y')
            )),
            
            new StringField ('COMMENT', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,COMMENT_LENGTH)
                    );
                },
            )),
        );
    }
}


?>