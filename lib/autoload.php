<?php
/**
 * Autoloader
 *
 */

/**
 * Autoloader
 * @param  string $className Class being intiated
 * @return void
 */
function class_autoloader($className)
{

    $fileParts = explode('\\', $className);

    $className = strtolower(array_pop($fileParts));

    $fileName = implode('/', $fileParts) . '/' . $className . '.class.php';


    if (is_file($fileName)) {

        require_once $fileName;
    }

}

spl_autoload_register('class_autoloader');