<?php
/**
*  file customers.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 05-08-2022
*/

/*
    Описание сущности Customers

    Fields:
    - Id, integer,primary,10
    - Name, string(200)
    - UNP, string(10)
    - Account, string(39)
    - Adress,string(500)
        (разделитель ';' порядок: индекс;страна;область;район;
            населённый пункт;улица;дом;корпус;квартира;помещение)
    - Mail string(100)
    - Phones string(200) (JSON формат { "название" : "телефон, полный номер", ...})
*/


namespace Predko\Customers;

use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity\
{
    DataManager,
    IntegerField,
    StringField,
    Validator,
    Event,
    EventResult
};

use \Predko\Customers\Customer\
{
    Customer,
    Customers,
};

require_once(__DIR__."/entitytraits.php");

Loc::loadMessages(__FILE__);

include_once(__DIR__."/constants.php");

class CustomerTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'predko_customers_customers';
    }

    public static function getObjectClass()
    {
        return Customer::class;
    }

    public static function getCollectionClass()
    {
        return Customers::class;
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
            
            new StringField ('NAME', array(
                'required' => 'true',
                
                'validation' => function() {
                    return array (
                        new Validator\Length(1, NAME_LENGTH),
                    );
                },
            )),
            
            new StringField ('UNP', array(
                'required' => 'true',
                'validation' => function() {
                    return array (
                        new Validator\Length(UNP_LENGTH, UNP_LENGTH),
                    );
                },
            )),
            
            new StringField ('ACCOUNT', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(ACCOUNT_LENGTH,ACCOUNT_LENGTH)
                    );
                },
            )),
            
            new StringField ('ADRESS', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,ADRESS_LENGTH)
                    );
                },
            )),
            
            new StringField ('MAIL', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,MAIL_LENGTH)
                    );
                },
            )),
            
            new StringField ('PHONE', array(
                'validation' => function() {
                    return array (
                        new Validator\Length(1,PHONE_LENGTH)
                    );
                },
            )),
            
            (new OneToMany('CONTRACTS',
                ContractTable::class,
                'CUSTOMER'))->configureJoinType('inner'),
            
            (new OneToMany('INCOMES',
                IncomeTable::class,
                'CUSTOMER'))->configureJoinType('inner')
    
        );
    }


    /**
     * @param Entity\Event $event 
     * @return Entity\EventResult
    **/
    public static function OnBeforeAdd(Event $event)
    {
        return self::CheckDataFields($event);
    }

    /**
     * @param Entity\Event $event 
     * @return Entity\EventResult
    **/
    public static function OnBeforeUpdate(Event $event)
    {
        return self::CheckDataFields($event, false);
    }

    /**
     * @param Entity\Event $event
     * @param bool $isAdd = true if the add operation
     * @return Entity\EventResult
    **/
    public static function CheckDataFields(Event $event, $isAdd = true)
    {
        $result = new EventResult;

        $data = $event->getParameter("fields");

        if (isset($data['NAME']))
        {
            // Unique
            $res = self::query()->where('NAME','=',$data["NAME"])->fetch();
            if ($res)
            {
                $result->addError(new EntityError(Loc::getMessage("PREDKO_CUSTOMERS_NON_UNIQUE_NAME_ERROR")));
            }
        }
        else if ($isAdd)
        {
            // Required
            $result->addError(new EntityError(Loc::getMessage("PREDKO_CUSTOMERS_NAME_REQUIRED_ERROR")));
        }

        if (isset($data["UNP"]))
        {
            // Unique
            $res = self::query()->where('UNP','=',$data["UNP"])->fetch();
            if ($res)
            {
                $result->addError(new EntityError(Loc::getMessage("PREDKO_CUSTOMERS_NON_UNIQUE_UNP_ERROR")));
            }
        }
        else if ($isAdd)
        {
            // Required
            $result->addError(new EntityError(Loc::getMessage("PREDKO_CUSTOMERS_UNP_REQUIRED_ERROR")));
        }

        return $result;
    }


}


?>