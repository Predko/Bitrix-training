<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Test");
?>

<?
// use Bitrix\Main\Diag\ExceptionHandler;

\Bitrix\Main\Loader::includeModule('predko.customers');

// $result = \Bitrix\Main\Config\Configuration::getInstance();

// debug($result, 'configuration');

// $main_option = \Bitrix\Main\Config\Option::getForModule('predko.customers');

// debug($main_option, 'main_option');

use \Predko\Customers\CustomerTable;
use \Predko\Customers\Customer\Customer;
use \Predko\Customers\IncomeTable;
use Bitrix\Main\Entity;
use Predko\Customers\Customer\Customers;

$entity = "\\Predko\\Customers\\" . "Customer" . "Table"; 

// $customers = new Customers;

// for ($i=0; $i < 10; $i++) 
// {
//     $customer = new Customer;
//     $customer->setName("Name".$i + 20);
//     $customer->setUnp("12345678".sprintf("%02d", $i+20));

//     $customers[] = $customer;
// }

// $customers->save(true);

$res = $entity::getFieldNames();

$query = new Entity\Query($entity::getEntity());
	
$query->setSelect($res)
        ->setOrder(["ID" => "ASC"])
        ->setOffset(0)
        ->setLimit(30);

file_put_contents("d:\\component_query.php", $query->getQuery(), FILE_APPEND);

$rsItems = $query->exec();

foreach ($rsItems as $id => $arItem) 
{
    $arItem["DETAIL_PAGE_URL"] = htmlspecialchars(str_replace(
        array("#SERVER_NAME#", "#SITE_DIR#", "#ENTITY_NAME#", "#ENTITY_ID#"),
        array(SITE_SERVER_NAME, SITE_DIR, "Customer", $arItem["ID"]),
        "/#ENTITY_NAME#/#ENTITY_NAME#_detail.php?ID=#ENTITY_ID#"
    ));
    $arResult["ITEMS"][]=$arItem;
}

debug($res);
debug($arResult);




?>

<form id="the-add-form" action="/test/add/customer.php" method="post">
    <label for="Name">Название организации</label>
    <input type="text" name="Name">
    <label for="UNP">УНП организации</label>
    <input type="number" name="UNP">
    <input type="submit" value="Добавить">
</form>

<!-- <table>
    <thead>
        <tr>
            <th></th>
        </tr>
    </thead>
</table> -->











<?





/*



// $incomes = IncomeTable::createCollection();

// $incomes[] = IncomeTable::add( array(
//     'CustomerId' => 1,
//     'Number' => sprintf("%02d", $i + 10),
//     'Date' => new Type\Date(null, 'Y-m-d'),
//     'Value' => "10000.20",
//     'TypeOfPayment' => 'Y'
// ));

//addIncome();

// Удаление
//deleteCustomers();

// $customers = new Customers;

// debug($customers, 'customers');

// $customer = new Customer;

// $customer->setName('name7');
// $customer->setUnp('1098765437');
// $customer->save();

// $customers->fill();

// $customers[] = $customer;

$customers = CustomerTable::getList()->fetchCollection();

foreach ($customers as $key => $customer) 
{
    echo "<pre>"."key = ". print_r($key, true) . "\ncustomer = " . print_r($customer->getId(), true)."</pre>";
}

// $res = IncomeTable::getList()->fetchCollection();
// foreach ($res as $key => $income) 
// {
//     echo "<pre>"."key = ". print_r($key, true) . "\nincome = " . print_r($income, true)."</pre>";
// }



/**
 * 
 *
 * @param int $start 
 * @param int $end 
 **
function addIncome(int $start = 0, int $end = 5)
{
    for ($i = $start; $i < $end; $i++) 
    { 
        $result = IncomeTable::add( array(
            'CustomerId' => 1,
            'Number' => sprintf("%02d", $i + 10),
            'Date' => new Type\Date(null, 'Y-m-d'),
            'Value' => "10000.20",
            'TypeOfPayment' => 'Y'
        ));
        
        if ($result->isSuccess())
        {
            $id = $result->getId();
            debug($id, "\nЗапись добавлена");
        }
        else 
        {
            $errors = $result->getErrorMessages();
            echo "<pre>"."\nЗапись не добавлена: ". var_export($errors, true)."</pre>";
        }
    }
       
}

/**
 *
 *
 * @param int $start 
 * @param int $end
 **/
/*
 function deleteCustomers(int $start = 1, int $end = 5)
{
    for ($i = $start; $i < $end; $i++) 
    { 
        $result = CustomerTable::delete($i);
        
        if ($result->isSuccess())
        {
            debug($i, "\nЗапись удалена");
        }
        else 
        {
            $errors = $result->getErrorMessages();
            echo "<pre>"."\nОшибка удаления: ". var_export($errors, true)."</pre>";
        }
    }
}
*/

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>