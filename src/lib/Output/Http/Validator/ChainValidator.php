<?php

namespace Output\Http\Validator;

use Output\Http\Form;
use Output\Http\FormFieldValidator;

class ChainValidator implements FormFieldValidator {
    /** @var $validators FormFieldValidator[] */
    protected $validators = array();

    /**
     * ChainValidator constructor.
     *
     * @param FormFieldValidator... $validators
     */
    public function __construct () {
        $args = func_get_args();

        foreach ($args as $arg) {
            $this->add($arg);
        }
    }

    public function add (FormFieldValidator $validator) {
        array_push($this->validators, $validator);
    }

    public function isValid (Form $form, $field) {
        foreach ($this->validators as $validator) {
            $var = $validator->isValid($form, $field);

            if (is_bool($var) && !$var) {
                return FALSE;
            }
        }
        return TRUE;
    }
}