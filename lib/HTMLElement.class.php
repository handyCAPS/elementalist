<?php

namespace lib;

class HTMLElement
{

    private $_node;

    private $_nodeType;

    private $_nodeOpen;

    private $_content;

    private $_nodeClose;

    private $_attributes = array();

    private $errors = array();


    private $validElements = array();

    private $validAttributes = array();

    private $globalAttributes = array();

    private $inputTypes = array();

    private $prefixedAttributes = array(
            'data',
            'aria'
        );

    public function __construct($nodeType)
    {

        $this->setUp();

        $this->_nodeType = $nodeType;

        $this->setNodeClose();

        $this->isValidElement();
    }

    private function setError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }

    private function setUp()
    {
        $htmlelements = json_decode(file_get_contents(__DIR__ . '/../htmlelements.json'), true);

        $this->validElements = array_keys($htmlelements['elements']);

        $this->validAttributes = $htmlelements['elements'];

        $this->globalAttributes = $htmlelements['globalattributes'];

        $this->inputTypes = $htmlelements['inputtypes'];
    }

    private function finalCheck()
    {

        if (empty($this->_content)) {
            $this->setError('No content has been set.');
            return false;
        }

        return !$this->hasErrors();
    }

    /**
     * @codeCoverageIgnore
     */
    public function echoPre($content)
    {
        echo '<pre>';
        print_r($content);
        echo '</pre>';
    }

    public function getNodeType()
    {
        return $this->_nodeType;
    }

    private function setNodeClose()
    {
        $this->_nodeClose = "</" . $this->_nodeType . ">";
    }

    public function isValidElement()
    {
        $result = in_array($this->_nodeType, $this->validElements);

        if ($result === false) {
            $this->setError('Invalid element type: ' . $this->_nodeType);
        }

        return $result;
    }

    private function isValidAttribute($attribute)
    {
        foreach ($this->prefixedAttributes as $prefix) {
            if (strpos($attribute, $prefix . '-') === 0) {
                return true;
            }
        }
        if (!in_array($attribute, $this->globalAttributes) && !in_array($attribute, $this->validAttributes[$this->_nodeType])) {
            $this->setError('Invalid attribute ' . $attribute . '.');
            return false;
        } else {
            return true;
        }
    }

    public function setAttribute($attribute, $values = array())
    {
        if (is_array($attribute)) {
            foreach ($attribute as $att => $vals) {
                if ($this->isValidAttribute($att)) {
                    $this->_attributes[$att] = $vals;
                }
            }
        } else {
            if ($this->isValidAttribute($attribute)) {
                $this->_attributes[$attribute] = $values;
            }
        }
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function setContent($content)
    {
        $this->_content = $content;
    }

    public function getContent()
    {
        return $this->_content;
    }

    private function buildNodeOpen()
    {
        $nodeOpen = "<" . $this->_nodeType;

        $attributes = '';

        foreach ($this->_attributes as $attribute => $values) {
            $vals = is_array($values) ? implode(' ', $values) : $values;
            $attributes .= ' ' . $attribute . "='" . $vals . "'";
        }

        $nodeOpen .= $attributes;

        $nodeOpen .= ">";

        $this->_nodeOpen = $nodeOpen;
    }

    private function buildNode()
    {
        $this->_node = $this->_nodeOpen . $this->_content . $this->_nodeClose;
    }

    public function getNode()
    {

        if ($this->finalCheck()) {

            $this->buildNodeOpen();

            $this->buildNode();

            return $this->_node;

        } else {
            return false;
        }
    }

}