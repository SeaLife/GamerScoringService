<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class StringLengthLimitValidator implements FormFieldValidator {

    private $length;
    private $message;

    public function __construct ($length = 3, $message = "Must be lower :length: characters long.") {
        $this->length  = $length;
        $this->message = $message;
    }

    public function isValid (Form $form, $field) {
        $strLength = strlen($form->get($field));

        if ($strLength > $this->length) {
            $msg = str_replace(":length:", $this->length, $this->message);

            throw new ValidationException(
                $msg
            );
        }
    }
}