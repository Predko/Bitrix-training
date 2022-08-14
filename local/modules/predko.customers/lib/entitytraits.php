<?php
/**
* file entitytraits.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 13-08-2022
*/

namespace Predko\Customers;

/**
 * 
 */
trait EntityTraits
{
    /**
     * Retuns array field names, except field types in array $except
     *
     * @return array		 Array of field names
     **/
    public static function getFieldNames()
    {
        $fields = self::getEntity()->getFields();
        
        $except = ["OneToMany", "Reference"];
        
        $res = [];

        foreach ($fields as $fieldName => $value) 
        {
            $fullClassName = explode("\\", get_class($value));
            $i = count($fullClassName) - 1;

            if ($i >= 0)
            {
                $className = $fullClassName[$i];
                
                if (!in_array($className, $except))
                {
                    $res[] = $fieldName;
                }
            }
        }
        
        return $res;
    }
    
}



?>