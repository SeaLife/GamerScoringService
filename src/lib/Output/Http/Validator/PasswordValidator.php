<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class PasswordValidator implements FormFieldValidator {

    private $factory = NULL;
    private $level   = 0;

    public function __construct ($level = 0) {

        $this->factory = ValidatorFactory::create();
        $this->level   = $level;

        $this->factory->isSmallerThan(255); # just to make sure the password can fit the database? :D

        if ($level >= 0) {
            $this->factory
                ->contains("[a-z]", FALSE, 'Must at least contain 1 lower character')
                ->contains("[A-Z]", FALSE, 'Must at least contain 1 upper character')
                ->isLongerThan($this->level <= 4 ? 4 : 8);
        }

        if ($level >= 1) {
            $this->factory->contains('[0-9]', FALSE, 'Must at least contain 1 digit');
        }

        if ($level >= 2) {
            $this->factory->contains("[\'^£$%&*()}{@#~?!§><>\/\\\,|=_+¬-]", FALSE, 'Must at least contain 1 special character');
        }

        if ($level >= 5) {
            $this->factory->isNotContainedWithinThePawnedPasswordDatabase($this->level <= 7 ? 2 : 0);
        }

        if ($level >= 8) {
            $this->factory->isNotContainingACharSequenceOf(3);
        }
    }

    public function isValid (Form $form, $field) {
        return $this->factory->isValid($form, $field);
    }
}