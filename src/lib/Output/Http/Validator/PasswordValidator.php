<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;

class PasswordValidator extends ChainValidator {
    public function __construct ($level = 0) {
        parent::__construct();

        if ($level > 0) {
            $this->add(new StringContainsValidator("[a-z]", FALSE, "Password must contain lower case characters."));
        }

        if ($level > 1) {
            $this->add(new StringContainsValidator("[A-Z]", FALSE, "Password must contain upper case characters."));
        }

        if ($level > 2) {
            $this->add(new StringContainsValidator("[0-9]", FALSE, "Password must contain at least 1 numeric"));
        }

        if ($level > 3 && $level <= 6) {
            $this->add(new StringSequenceValidator(3, TRUE));
            $this->add(new PwnedPasswordValidator(2));
        }
        if ($level > 6) {
            $this->add(new StringSequenceValidator(2, TRUE));
            $this->add(new PwnedPasswordValidator(0));
        }

        if ($level > 4) {
            $this->add(new StringContainsValidator("[\'^£$%&*()}{@#~?!§><>\/\\\,|=_+¬-]", FALSE, "Password must contain at least 1 special character"));
        }

        if ($level >= 4 && $level < 9) {
            $this->add(new StringLengthValidator(4, "Password must be at least :length: characters long."));
        }

        if ($level >= 9) {
            $this->add(new StringLengthValidator(8, "Password must be at least :length: characters long."));
        }
    }

    public function isValid (Form $form, $field) {
        $messages = array();

        foreach ($this->validators as $validator) {
            try {
                $val = $validator->isValid($form, $field);

                if (!$val && is_bool($val)) return $val;
            } catch (ValidationException $e) {
                array_push($messages, $e->getMessage());
            }
        }

        if (!empty($messages)) {
            throw new ValidationException(join("<br>", $messages));
        }
    }
}