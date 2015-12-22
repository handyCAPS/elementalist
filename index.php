<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Elementalist</title>
</head>
<body>

</body>
</html>

<?php

require_once 'lib/autoload.php';

$Element = new \lib\HTMLElement('input');

$Element->setContent('This is some content');

$Element->setInputType('number');

$Element->setAttribute(['class' => 'classOne','data' => ['test' => 'testValue'], 'class' => 'classTwo']);

$ma = $Element->getNode();

$Element->echoPre($Element);