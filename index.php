<?php

require 'vendor/autoload.php';

define('OUT_FOLDER',dirname(__FILE__).DIRECTORY_SEPARATOR.'VCFiles'.DIRECTORY_SEPARATOR);

if (empty($_GET)) {
    include 'view/list.php';
}

if (isset($_GET['c']) && isset($_GET['f'])) {
    $controllerName = "\\Controller\\{$_GET['c']}Controller";
    $controller = new $controllerName();
    $funName = trim($_GET['f']);
    call_user_func(array($controller,$funName));
}

