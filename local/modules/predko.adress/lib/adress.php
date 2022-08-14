<?php
namespace Predko\Adress;

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Entity\ 
{
    DataManager,
    IntegerField,
    StringField,
    DatetimeField,
    Validator
};

use Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

class AdressTable extends DataManager
{
    // название таблицы
    public static function getTableName()
    {
        return 'adressbook';
    }
    // создаем поля таблицы
    public static function getMap()
    {
        return array(
            new IntegerField('ID', array(
                'autocomplete' => true,
                'primary' => true
            )),// autocomplite с первичным ключом
            new StringField('NAME', array(
                'required' => true,
                'title' => Loc::getMessage('PREDKO_ADRESS_NAME'),
                'default_value' => function () {
                    return Loc::getMessage('PREDKO_ADRESS_NAME_DEFAULT_VALUE');
                },
                'validation' => function () {
                    return array(
                        new Validator\Length(null, 255),
                    );
                },
            )),//обязательная строка с default значением и длиной не более 255 символов
            new StringField('ADRESS', array(
                'required' => false,
                'title' => Loc::getMessage('PREDKO_ADRESS_ADRESS'),
                'default_value' => function () {
                    return Loc::getMessage('PREDKO_ADRESS_ADRESS_DEFAULT_VALUE');
                },
                'validation' => function () {
                    return array(
                        new Validator\Length(null, 255),
                    );
                }              
            )),//обязательная строка с default значением  и длиной не более 255 символов
            new DatetimeField('UPDATED_AT',array(
                'required' => true)),//обязательное поле даты
            new DatetimeField('CREATED_AT',array(
                'required' => true)),//обязательное поле даты
        );
    }
}