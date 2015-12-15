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

    public function testReturnsNodeTypeSetInConstructorOrWithMethod()
    {
        $this->assertEquals($this->validElString, $this->validEl->getNodeType());

        $el = new \lib\HTMLElement();

        $el->setNodeType('input');

        $this->assertEquals('input', $el->getNodeType());
    }

    public function testReturnsErrors()
    {

        $this->assertFalse($this->validEl->hasErrors());

        $this->assertEquals(array(), $this->validEl->getErrors());

        $this->assertTrue($this->inValidEl->hasErrors());

        $this->assertNotEmpty($this->inValidEl->getErrors());
    }

    public function testOnlyAcceptsValidElements()
    {
        $this->assertTrue($this->validEl->isValidElement());

        $this->assertFalse($this->inValidEl->isValidElement());

        $invalEl = new \lib\HTMLElement();

        $invalEl->setNodeType($this->inValidElString);

        $this->assertFalse($invalEl->isValidElement());
    }

    public function testOnlyAcceptsValidAttributes()
    {
        $this->validEl->setAttribute('class', 'classOne');

        $this->assertFalse($this->validEl->hasErrors());

        $this->validEl->setAttribute('blanket', 'klink');

        $this->assertTrue($this->validEl->hasErrors());

    }

    public function testOnlyAcceptsValidInputTypes()
    {
        $el = new \lib\HTMLElement('input');

        $el->setInputType('blanket');

        $this->assertTrue($el->hasErrors());
    }

    public function testCanSetVariousAttributes()
    {
        $this->validEl->setAttribute('class', array('classOne'));

        $this->assertEquals(array('class' => array('classOne')), $this->validEl->getAttributes());
    }

    public function testOnlyAcceptsAttributesBelongingToInput()
    {
        $el = new \lib\HTMLElement();

        $el->setNodeType('input');

        $el->setInputType('number');

        $el->setAttribute('id', 'idOne');

        $el->setAttribute('min', 0);

        $expected = "<input type='number' id='idOne' min='0'>";

        $this->assertEquals($expected, $el->getNode(), implode(',',$el->getErrors()));
    }

    public function testCanSetArrayOfDataAttributes()
    {

        $content = 'This is some content';

        $expected = "<div data-test='testValue testValueArray' data-testTwo='testValueTwo'>$content</div>";

        $this->validEl->setContent($content);

        $this->validEl->setAttribute('data', ['test' => ['testValue', 'testValueArray'], 'testTwo' => 'testValueTwo']);

        $this->assertEquals($expected, $this->validEl->getNode());

    }

    public function testReturnsContentWhenSet()
    {
        $content = 'This is some test content.';

        $this->validEl->setContent($content);

        $this->assertEquals($content, $this->validEl->getContent());
    }

    public function testHasNoContentForSelfClosingTags()
    {
        $el = new \lib\HTMLElement('input');

        $el->setContent('This is some content');

        $this->assertEmpty($el->getContent());
    }

    public function testReturnsFalseWithoutContentWhenNeeded()
    {
        $this->assertFalse($this->validEl->getNode());

        $el = new \lib\HTMLElement('br');

        $this->assertTrue($el->getNode() !== false);
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

        $br = new \lib\HTMLElement('br');

        $this->assertEquals("<br>", $br->getNode());
    }

}