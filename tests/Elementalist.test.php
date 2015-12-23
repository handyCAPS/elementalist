<?php

namespace lib;

class HTMLElementTest extends \PHPUnit_Framework_TestCase
{

    private $validEl;

    private $validElString = 'div';

    private $inValidEl;

    private $inValidElString = 'basket';

    private $testContent = 'This is some content';


    protected function setUp()
    {

        $this->validEl = new HTMLElement($this->validElString);

        $this->inValidEl = new HTMLElement($this->inValidElString);

    }

    protected function tearDown()
    {
        unset($this->validEl);
        unset($this->inValidEl);
    }

    public function testReturnsErrors()
    {

        $this->assertFalse($this->validEl->hasErrors());

        $this->assertEquals(array(), $this->validEl->getErrors());

        $this->assertTrue($this->inValidEl->hasErrors());

        $this->assertNotEmpty($this->inValidEl->getErrors());
    }

    public function testThrowsExceptionIfNoNodetypeHasBeenSet()
    {
        $el = new HTMLElement();

        $el->setContent($this->testContent);

        $this->setExpectedException('\lib\InvalidElementException');

        $el->getNode();
    }

    public function testOnlyAcceptsValidElements()
    {
        $this->setExpectedException('\lib\InvalidElementException');

        $el = new HTMLElement();

        $el->setNodeType($this->inValidElString);

        $el->setContent($this->testContent);

        $el->getNode();
    }

    public function testOnlyAcceptsValidAttributes()
    {
        $expected = "<div class='classOne' data-category='sport'>$this->testContent</div>";

        $this->validEl->setContent($this->testContent);

        $this->validEl->setAttribute('class', 'classOne');

        $this->validEl->setAttribute(['data' => ["category" => "sport"]]);

        $this->assertFalse($this->validEl->hasErrors());

        $this->assertEquals($expected, $this->validEl->getNode());

        $this->validEl->setAttribute('blanket', 'klink');

        $this->assertTrue($this->validEl->hasErrors());

        $this->setExpectedException('\lib\InvalidElementException');

        $this->validEl->getNode();

    }

    public function testSetsAnyDataAttribute()
    {
        $expected = "<div data-test='testOne' data-testother='testOther'>$this->testContent</div>";

        $this->validEl->setContent($this->testContent);

        $this->validEl->setAttribute([
            'data' => [
                        'test' => 'testOne',
                        'testOther' => 'testOther'
                        ]
                    ]);

        $this->assertEquals($expected, $this->validEl->getNode());

    }

    public function testOnlyAcceptsValidInputTypes()
    {
        $el = new HTMLElement('input');

        $el->setInputType('blanket');

        $this->assertTrue($el->hasErrors());
    }

    public function testOnlyAcceptsAttributesValidForInputType()
    {
        $expected = "<input type='number' min='0' class='input-number'>";

        $inputEl = new HTMLElement('input');

        $inputEl->setInputType('number');

        $inputEl->setAttribute([
            'min' => '0',
            'class' => 'input-number'
            ]);

        $this->assertFalse($inputEl->hasErrors());

        $this->assertEquals($expected, $inputEl->getNode());

        $inputEl->setAttribute('formaction', 'form');

        $this->assertTrue($inputEl->hasErrors());
    }

    public function testCanSetVariousAttributes()
    {
        $expected = "<div id='idOne' class='classOne classTwo'>$this->testContent</div>";

        $this->validEl->setAttribute('id', 'idOne');

        $this->validEl->setAttribute('class', array('classOne', 'classTwo'));

        $this->validEl->setContent($this->testContent);

        $this->assertEquals($expected, $this->validEl->getNode());
    }

    public function testOnlyAcceptsAttributesBelongingToInput()
    {
        $el = new HTMLElement();

        $el->setNodeType('input');

        $el->setInputType('number');

        $el->setAttribute('id', 'idOne');

        $el->setAttribute('min', 0);

        $expected = "<input type='number' id='idOne' min='0'>";

        $this->assertEquals($expected, $el->getNode(), implode(',',$el->getErrors()));
    }

    public function testCanSetArrayOfDataAttributes()
    {

        $expected = "<div data-test='testValue testValueArray' data-testtwo='testValueTwo'>$this->testContent</div>";

        $this->validEl->setContent($this->testContent);

        $this->validEl->setAttribute('data', ['test' => ['testValue', 'testValueArray'], 'testTwo' => 'testValueTwo']);

        $this->assertEquals($expected, $this->validEl->getNode());

    }

    public function testCanSetArrayOfEmptyAttributes()
    {
        $expected = "<input type='checkbox' disabled='disabled' required='required' checked='checked'>";
        $input = new HTMLElement('input');
        $input->setInputType('checkbox');
        $input->setAttribute(['disabled','required','checked']);

        $this->assertEquals($expected, $input->getNode());
    }

    public function testReturnsContentWhenSet()
    {
        $this->validEl->setContent($this->testContent);

        $this->assertEquals($this->testContent, $this->validEl->getContent());
    }

    public function testHasNoContentForSelfClosingTags()
    {
        $el = new HTMLElement('input');

        $el->setContent($this->testContent);

        $this->assertEmpty($el->getContent());
    }

    public function cantSetAttributeBeforeNodetypeHasBeenSet()
    {
        $el = new HTMLElement();

        $this->setExpectedException('\lib\InvalidElementException');

        $el->setAttribute('class', 'classOne');

        $this->assertTrue($el->hasErrors());
    }


    public function testCantSetAttributesForInputBeforeTypeHasBeenSet()
    {
        $el = new HTMLElement('input');

        $this->setExpectedException('\lib\InvalidElementException');

        $el->setAttribute('class', 'testClass');

        $this->assertTrue($el->hasErrors());
    }

    public function testReturnsAProperlyFormattedNode()
    {
        $el = new HTMLElement('div');

        $expected = "<div id='divOne' class='classOne classTwo' data-category='sports'>" . $this->testContent . "</div>";;

        $el->setContent($this->testContent);

        $el->setAttribute(array(
                'id' => 'divOne',
                'class' => array(
                    'classOne',
                    'classTwo'
                    ),
                'data-category' => 'sports'
                ));

        $this->assertEquals($expected, $el->getNode());

        $br = new HTMLElement('br');

        $this->assertEquals("<br>", $br->getNode());
    }

    public function testCanSetAnotherElementAsContent()
    {
        $expected = "<a href='test'><i class='icon'></i></a>";

        $icon = new HTMLElement('i');
        $icon->setAttribute('class', 'icon');

        $anchor = new HTMLElement('a');
        $anchor->setAttribute('href', 'test');
        $anchor->setContent($icon->getNode());

        $this->assertEquals($expected, $anchor->getNode());
    }

    public function testCanSetMultipleValuesForSameAttribute()
    {
        $expected = "<h1 class='one two'>$this->testContent</h1>";

        $el = new HTMLElement;
        $el->setNodeType('h1');
        $el->setContent($this->testContent);

        $el->class = 'one';
        $el->setAttribute('class','two');

        $this->assertEquals($expected, $el->getNode());
    }

    public function testItCanSetAttributesByName()
    {
        $expected = "<nav class='classOne'></nav>";

        $el = new HTMLElement('nav');
        $el->class = 'classOne';

        $this->assertEquals($expected, $el->getNode(), $el->getErrors());
    }

}