<?php
/**
 * Invalid Element Exception
 *
 */

namespace lib;

/**
 * Custom exception thrown when attempting to get an invalid html element
 *
 * @author Tim Doppenberg
 */
class InvalidElementException extends \Exception
{
    /**
     * Throws Exception with custom error message
     * @param array $errors All collected errors
     */
    public function __construct($errors = array())
    {
        parent::__construct("Invalid HTMLElement. Errors:\n" . implode(",\n", $errors));
    }
}