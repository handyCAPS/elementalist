<?php

class HTMLElementTest extends PHPUnit_Framework_TestCase
{

    private $validEl;

    private $validElString = 'div';

    private $inValidEl;

    private $inValidElString = 'basket';




    protected function setUp()
    {

        $this->validEl = new \lib\HTMLElement($this->validElString);

        $this->inValidEl = new \lib\HTMLElement($this->inValidElString);

    }

    protected function tearDown()
    {
        unset($this->validEl);
        unset($this->inValidEl);
    }

    public function testReturnsNodeTypeSetInConstructor()
    {
        $this->assertEquals($this->validElString, $this->validEl->getNodeType());
    }

    public function testHasErrors()
    {

        $this->assertFalse($this->validEl->hasErrors());

        $this->assertEquals(array(), $this->validEl->getErrors());

        $this->assertTrue($this->inValidEl->hasErrors());
    }

    public function testIsValidElement()
    {
        $this->assertTrue($this->validEl->isValidElement());

        $this->assertFalse($this->inValidEl->isValidElement());
    }

    public function testReturnsAProperlyFormattedNode()
    {
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

        $this->assertEquals($expected, $el->getNode());
    }

    public function testReturnsContentWhenSet()
    {
        $content = 'This is some test content.';

        $this->validEl->setContent($content);

        $this->assertEquals($content, $this->validEl->getContent());
    }

    public function testCanSetVariousAttributes()
    {
        $this->validEl->setAttribute('class', array('classOne'));

        $this->assertEquals(array('class' => array('classOne')), $this->validEl->getAttributes());
    }

    public function testReturnsFalseWhitoutContent()
    {
        $this->assertFalse($this->validEl->getNode());
    }

    public function testOnlyAcceptsValidAttributes()
    {
        $this->validEl->setAttribute('class', 'classOne');

        $this->assertFalse($this->validEl->hasErrors());

        $this->validEl->setAttribute('blanket', '');

        $this->assertTrue($this->validEl->hasErrors());

    }

}