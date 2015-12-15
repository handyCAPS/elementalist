<?php

namespace lib;

class HTMLElement
{

    private $_node;

    private $_nodeType;

    private $_nodeOpen;

    private $_content;

    private $_nodeClose;

    private $_inputType;

    private $_attributes = array();

    private $_dataAttributes = array();

    private $errors = array();

    private $isInput = false;

    private $isSelfClosing = false;


    private $validElements = array();

    private $validAttributes = array();

    private $globalAttributes = array();

    private $inputTypes = array();

    private $selfClosingTags = array();


    public function __construct($nodeType)
    {

        $this->setUp();

        $this->setNodeType($nodeType);

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

        $this->validElements    = array_keys($htmlelements['elements']);
        
        $this->validAttributes  = $htmlelements['elements'];
        
        $this->globalAttributes = $htmlelements['globalattributes'];
        
        $this->inputTypes       = $htmlelements['inputtypes'];
        
        $this->selfClosingTags  = $htmlelements['selfclosingtags'];
    }

    private function finalCheck()
    {

        if (!$this->isSelfClosing) {
            if (empty($this->_content)) {
                $this->setError('No content has been set.');
                return false;
            }
        }

        return !$this->hasErrors();
    }

    private function checkForData($attribute)
    {
        $prefix = 'data';

        return $attribute === $prefix || strpos($attribute, $prefix . '-') === 0;
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

    private function setNodeType($type)
    {
        $this->_nodeType = $type;

        if ($type == 'input') {
            $this->isInput = true;
        }

        if (in_array($type, $this->selfClosingTags)) {
            $this->isSelfClosing = true;
        } else {
            $this->setNodeClose();
        }
    }

    public function getNodeType()
    {
        return $this->_nodeType;
    }

    private function setNodeClose()
    {
        $this->_nodeClose = "</" . $this->_nodeType . ">";
    }

    public function setInputType($type)
    {
    
        if ($this->isValidInputType($type)) {
            $this->_inputType = $type;
        } else {
            $this->setError('Invalid inputtype ' . $type . '.');
        }
    
    }

    private function isValidInputType($type)
    {
        return in_array($type, array_keys($this->inputTypes));
    }

    public function isValidElement()
    {
        $result = in_array($this->_nodeType, $this->validElements);

        if ($result === false) {
            $this->setError('Invalid element type: ' . $this->_nodeType);
        }

        return $result;
    }

    private function isValidInputAttribute($attribute)
    {

        return in_array($attribute, $this->inputTypes[$this->_inputType]['attributes']);

    }

    private function isValidAttribute($attribute)
    {
        $res = false;

        if ($this->checkForData($attribute)) {
            $res = true;
        } else {
            if ($this->isInput) {
                if (!$this->isValidInputAttribute($attribute)) {
                    $this->setError('Invalid attribute ' . $attribute . ' for ' . $this->_nodeType . ' type: ' . $this->_inputType . '.');
                    $res = false;
                } else {
                    $res = true;
                }
            } else {
                if (!in_array($attribute, $this->globalAttributes) && !in_array($attribute, $this->validAttributes[$this->_nodeType])) {

                    $this->setError('Invalid attribute ' . $attribute . ' for ' . $this->_nodeType . '.');
                    $res = false;

                } else {
                    $res = true;
                }
            }
        }

        return $res;
    }

    public function setAttribute($attribute, $values = array())
    {
        $allAttributes = array();

        // Inputs need a type before any attributes can be set
        if ($this->isInput && empty($this->_inputType)) {

            $this->setError('Trying to set input attributes before input type.');

            return false;

        } 

        // Attributes can be an array, if so, merge with others
        if (is_array($attribute)) {
            foreach ($attribute as $att => $vals) {
                    $allAttributes[$att] = $vals;
            }
        } else {
            $allAttributes[$attribute] = $values;
        }

        // Loop over all attributes
        foreach ($allAttributes as $att => $vls) {

            if ($this->checkForData($att)) {

                // Data attributes get split out to their own array
                if (strpos($att, 'data-') === 0) {
                    $this->_dataAttributes[] = [str_replace('data-', '', $att) => $vls];
                } else {
                    $this->_dataAttributes[] = $vls;
                }

            } else {

                // Only set valid attributes
                if ($this->isValidAttribute($att)) {
                    $this->_attributes[$att] = $vls;
                }

            }

        }

    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function setContent($content)
    {
        if (!$this->isSelfClosing) {
            $this->_content = $content;
        }
    }

    public function getContent()
    {
        return $this->_content;
    }

    private function mergeDataAttributes()
    {
        $dataAtts = array();

        foreach ($this->_dataAttributes as $key => $data) {
            foreach ($data as $type => $value) {
                $dataAtts['data-' . $type] = $value;
            }
        }

        $this->_attributes = array_merge($this->_attributes, $dataAtts);
    }

    private function buildNodeOpen()
    {
        $this->mergeDataAttributes();

        $nodeOpen = "<" . $this->_nodeType;

        $attributes = '';

        if ($this->isInput) {
            $attributes .= " type='" . $this->_inputType . "'";
        }

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