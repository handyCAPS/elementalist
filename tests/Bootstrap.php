<?php

function class_autoloader($className)
{

    $fileParts = explode('\\', $className);

    $className = strtolower(array_pop($fileParts));

    $fileName = implode('/', $fileParts) . '/' . $className . '.class.php';


    if (is_file($fileName)) {

        require_once $fileName;
    }

    require_once 'vendor/autoload.php';

}

spl_autoload_register('class_autoloader');