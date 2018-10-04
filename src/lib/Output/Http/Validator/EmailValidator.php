<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class EmailValidator implements FormFieldValidator {
    public function isValid (Form $form, $field) {
        $email = $form->get($field);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Does not contain a valid email address");
        }
    }
}