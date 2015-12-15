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

require_once 'lib/HTMLElement.class.php';

$Element = new \lib\HTMLElement('input');

$Element->setContent('This is some content');

$Element->setAttribute(['class' => 'classOne','data' => ['test' => 'testValue']]);

$ma = $Element->getNode();


$el = new \lib\HTMLElement('div');

        $content = 'This is some content';

        $expected = "<div id='divOne' class='classOne classTwo' data-category='sports'>" . $content . "</div>";;

        $el->setContent($content);

        $el->setAttribute(array(
                'id' => 'divOne',
                'class' => array(
                    'classOne',
                    'classTwo'
                    ),
                'data-category' => 'sports'
                ));



$Element->echoPre($el);