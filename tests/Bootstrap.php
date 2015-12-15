<?php

function class_autoloader($className)
{

    $fileName = 'lib/' . str_replace('lib\\', '', $className) . '.class.php';

    require_once 'vendor/autoload.php';

    if (is_file($fileName)) {

        require_once $fileName;
    }

}

spl_autoload_register('class_autoloader');