<?php
/**
* file include.php
* Created by Visual Studio Code
* User: Victor Predko
* predko.victor@gmail.com
* 09-08-2022
*/

// Decimal type 15.2
const DECIMAL_LENGTH = 18;

$arJsConfig = array( 
    'import_csv' => array( 
        'js' => '/local/components/predko/import.csv/templates/import-csv/js/modules/ProgressBar.js', 
        'rel' => array(), 
    ) 
); 

foreach ($arJsConfig as $ext => $arExt) { 
    \CJSCore::RegisterExt($ext, $arExt); 
}

?>