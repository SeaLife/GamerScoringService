<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class StringContainsValidator implements FormFieldValidator {

    private $match        = NULL;
    private $onFind       = TRUE;
    private $errorMessage = NULL;

    public function __construct ($match, $onFind = TRUE, $message = "Value is not valid") {
        $this->onFind       = $onFind;
        $this->match        = $match;
        $this->errorMessage = $message;
    }

    public function isValid (Form $form, $field) {
        $matches = preg_match("/{$this->match}/", $form->get($field));

        if (empty($matches) && !$this->onFind) {
            throw new ValidationException($this->errorMessage);
        }

        if ($matches && $this->onFind) {
            throw new ValidationException($this->errorMessage);
        }
    }
}