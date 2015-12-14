<?php

function class_autoloader($className)
{

    require_once 'vendor/autoload.php';

    file_put_contents('../test.txt', 'AAAAAAAAAAAAAAAWOOOO');

    if (strpos($className, 'lib\\') !== false) {

        require_once 'lib/' . str_replace('lib\\', '', $className) . '.class.php';
    }

}

spl_autoload_register('class_autoloader');