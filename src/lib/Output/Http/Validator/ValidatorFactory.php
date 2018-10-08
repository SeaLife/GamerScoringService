<?php

namespace Output\Http\Validator;

use Output\Http\Form;
use Output\Http\FormFieldValidator;

class ValidatorFactory implements FormFieldValidator {

    private $chain;

    public static function create () {
        return new ValidatorFactory();
    }

    public function __construct () {
        $this->chain = new ChainValidator();
        $this->chain->runAllValidators();
    }

    public function isLongerThan ($int) {
        $this->chain->add(new StringLengthValidator($int));
        return $this;
    }

    public function isSmallerThan ($int) {
        $this->chain->add(new StringLengthLimitValidator($int));
        return $this;
    }

    public function isEmail () {
        $this->chain->add(new EmailValidator());
        return $this;
    }

    public function isNonExistentInDatabase ($entity, $column) {
        $this->chain->add(new HasNoDatabaseEntryValidator($entity, $column));
        return $this;
    }

    public function isPasswordSecure ($level = 3) {
        $this->chain->add(new PasswordValidator($level));
        return $this;
    }

    public function isNotContainingACharSequenceOf ($length = 2) {
        $this->chain->add(new StringSequenceValidator($length));
        return $this;
    }

    public function contains ($regex, $onFind = TRUE, $message = "Value is not valid") {
        $this->chain->add(new StringContainsValidator($regex, $onFind, $message));
        return $this;
    }

    public function isNotContainedWithinThePawnedPasswordDatabase ($limit = 0) {
        $this->chain->add(new PawnedPasswordValidator($limit));
        return $this;
    }

    public function isEqualTo ($str, $message = 'Items are not equal') {
        $this->chain->add(new EqualsValidator($str, $message));
        return $this;
    }

    /**
     * @param Form $form
     * @param      $field
     *
     * @return bool
     */
    public function isValid (Form $form, $field) {
        return $this->chain->isValid($form, $field);
    }
}