<?php

//define("DEFAULT_TEMPLATE_PATH","/local/templates/.default");

function debug($data, $name = "")
{
    $prefix = "";
    if ($name[0] == "\t" || $name[0] == "\n")
        $prefix = $name;
    elseif ($name != "")
        $prefix = "$".$name." = ";
    
    echo "<pre>". $prefix . print_r($data, true) . "</pre>";
}