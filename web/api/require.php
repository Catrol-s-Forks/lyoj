<?php
    error_reporting(E_ERROR);
    ini_set("display_errors","Off");
    require_once "../../config.php";
    require_once "../function.php";
    global $config;
    for ($i=0;$i<count($config["require_code"]);$i++) 
        require_once("../../".$config["require_code"][$i]);
?>
