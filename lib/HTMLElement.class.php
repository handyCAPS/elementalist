<?php

/**
 * Class HTMLElement
 *
 */

namespace lib;

/**
 * Builds a valid html element with appropiate attributes. Returns a string containg a valid html node
 *
 * @author Tim Doppenberg
 */
class HTMLElement
{
    /**
     * The fully formed string representing a valid html node
     *
     * @var string
     */
    private $_node;

    /**
     * Name of the element (required)
     *
     * @var string
     */
    private $_nodeType;

    /**
     * Opening tag for the element
     *
     * @var string
     */
    private $_nodeOpen;

    /**
     * Content between the tags
     *
     * @var string
     */
    private $_content;

    /**
     * Closing tag
     *
     * @var string
     */
    private $_nodeClose;

    /**
     * Input type
     *
     * @var string
     */
    private $_inputType;

    /**
     * Node attributes
     *
     * @var array
     */
    private $_attributes = array();

    /**
     * Node data attributes
     *
     * @var array
     */
    private $_dataAttributes = array();

    /**
     * Errors
     *
     * @var array
     */
    private $errors = array();

    /**
     * Is it an input element ?
     *
     * @var boolean
     */
    private $isInput = false;

    /**
     * Does it need content and a closing tag ?
     *
     * @var boolean
     */
    private $isSelfClosing = false;

    /**
     * All valid html elements
     *
     * @var array
     */
    private $validElements;

    /**
     * Valid html attributes for the seperate elements
     * [element => array(valid attributes)]
     *
     * @var array
     */
    private $validAttributes;

    /**
     * Attributes valid for all elements
     *
     * @var array
     */
    private $globalAttributes;

    /**
     * Valid input types
     * @var array
     */
    private $inputTypes;

    /**
     * Elements that don't need content or closing tags
     *
     * @var array
     */
    private $selfClosingTags;


    /**
     * Constructor
     *
     * @param string $nodeType Name of the element to create (optional)
     */
    public function __construct($nodeType = null)
    {

        $this->setUp();

        if (!is_null($nodeType)) {
            $this->setNodeType($nodeType);
            $this->isValidElement();
        }

    }

    /**
     * Add error to error array
     *
     * @param string $error Error message
     */
    private function setError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * Get error array
     *
     * @return array Array of error messages
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Are there any errors ?
     *
     * @api
     *
     * @return boolean Are there any errors ?
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Set valid elements and attributes in memory
     *
     */
    private function setUp()
    {
        $htmlelements = json_decode(file_get_contents(__DIR__ . '/../htmlelements.json'), true);

        $this->validElements    = array_keys($htmlelements['elements']);

        $this->validAttributes  = $htmlelements['elements'];

        $this->globalAttributes = $htmlelements['globalattributes'];

        $this->inputTypes       = $htmlelements['inputtypes'];

        $this->selfClosingTags  = $htmlelements['selfclosingtags'];
    }

    /**
     * Checks required elements of the node
     *
     * @return boolean Are there any errors ?
     */
    private function finalCheck()
    {

        if (empty($this->_nodeType)) {
            $this->setError('No nodetype has been defined');
        }

        if (!$this->isSelfClosing) {
            if (empty($this->_content)) {
                $this->setError('No content has been set.');
            }
        }

        return !$this->hasErrors();
    }

    /**
     * Checks if attribute is data type
     *
     * @param  string $attribute attribute to check
     * @return boolean Is it a data attribute ?
     */
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

    /**
     * Set element name
     *
     * @api
     *
     * @param string $type Name of the element
     */
    public function setNodeType($type)
    {
        $this->_nodeType = $type;

        if ($this->isValidElement()) {

            if ($type == 'input') {
                $this->isInput = true;
            }

            if (in_array($type, $this->selfClosingTags)) {
                $this->isSelfClosing = true;
            } else {
                $this->setNodeClose();
            }

        }

    }

    /**
     * Build the closing node
     *
     */
    private function setNodeClose()
    {
        $this->_nodeClose = "</" . $this->_nodeType . ">";
    }

    /**
     * Set the type for an input element
     *
     * @api
     * @param string $type Input type
     *
     */
    public function setInputType($type)
    {

        if ($this->isValidInputType($type)) {
            $this->_inputType = $type;
        } else {
            $this->setError('Invalid inputtype ' . $type . '.');
        }

    }

    /**
     * Check if input type is valid
     *
     * @param  string  $type Input type
     * @return boolean       Is it a valid input type
     */
    private function isValidInputType($type)
    {
        return in_array($type, array_keys($this->inputTypes));
    }

    /**
     * Check if the element is valid
     * @return boolean Is it a valid element ?
     */
    private function isValidElement()
    {
        $result = in_array($this->_nodeType, $this->validElements);

        if ($result === false) {
            $this->setError('Invalid element type: ' . $this->_nodeType);
        }

        return $result;
    }

    /**
     * Check if the attribute is valid for the set input type
     *
     * @param  string  $attribute Attribute tot check
     * @return boolean            Is the attribute valid for this input type ?
     */
    private function isValidInputAttribute($attribute)
    {

        $res = false;

        if (in_array($attribute, $this->inputTypes[$this->_inputType]['attributes'])) {
            $res = true;
        }

        if (in_array($attribute, $this->globalAttributes)) {
            $res = true;
        }

        return $res;

    }

    /**
     * Check if attribute is valid for element
     *
     * @param  string  $attribute Attribute to check
     * @return boolean            Is it a valid attribute for this element ?
     */
    private function isValidAttribute($attribute)
    {
        $res = false;

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

        return $res;
    }

    /**
     * Set or add attribute
     *
     * @api
     * @param string|array $attribute attribute to add
     * @param array|string $values    Array of values to set for the attribute
     */
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

    /**
     * Add content to the node
     *
     * @api
     * @param string $content Content for the node
     */
    public function setContent($content)
    {
        if (!$this->isSelfClosing) {
            $this->_content = $content;
        }
    }

    /**
     * Get the content
     *
     * @api
     * @return string Content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Merge data attributes with the other attributes
     *
     * @return void
     */
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

    /**
     * Get a string of formatted attributes and values
     *
     * @return string Formatted attribute string
     */
    private function getAttributeString(){
        $attributes = '';

        if ($this->isInput) {
            $attributes .= " type='" . $this->_inputType . "'";
        }

        foreach ($this->_attributes as $attribute => $values) {
            $vals = is_array($values) ? implode(' ', $values) : $values;
            $attributes .= ' ' . strtolower($attribute) . "='" . $vals . "'";
        }

        return $attributes;
    }

    /**
     * Build the opening tag
     *
     * @return void
     */
    private function buildOpenTag()
    {
        $this->mergeDataAttributes();

        $nodeOpen = "<" . $this->_nodeType;

        $nodeOpen .= $this->getAttributeString();

        $nodeOpen .= ">";

        $this->_nodeOpen = $nodeOpen;
    }

    /**
     * Build the complete node and save to memory
     *
     * @return void
     */
    private function buildNode()
    {
        $this->_node = $this->_nodeOpen . $this->_content . $this->_nodeClose;
    }

    /**
     * Get the fully formed valid html node
     *
     * @api
     * @return string Fully formed valid html node as string
     */
    public function getNode()
    {

        if ($this->finalCheck()) {

            $this->buildOpenTag();

            $this->buildNode();

            return $this->_node;

        } else {
            throw new InvalidElementException($this->getErrors());
        }
    }

}