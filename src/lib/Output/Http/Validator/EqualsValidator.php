<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class EqualsValidator implements FormFieldValidator {

    private $toMatch;
    private $message;

    public function __construct ($match, $message = 'Items are not equal') {
        $this->toMatch = $match;
        $this->message = $message;
    }

    public function isValid (Form $form, $field) {
        if ($form->get($field) != $this->toMatch) throw new ValidationException($this->message);
        return TRUE;
    }
}