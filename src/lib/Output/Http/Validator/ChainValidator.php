<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

/**
 * @codeCoverageIgnore
 */
class ChainValidator implements FormFieldValidator {
    /** @var $validators FormFieldValidator[] */
    protected $validators = array();
    /** @var bool */
    private $runAll = FALSE;

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
        $exceptionMessage = '';

        foreach ($this->validators as $validator) {
            try {
                $validator->isValid($form, $field);
            } catch (ValidationException $e) {
                if (!$this->runAll) throw $e;
                $exceptionMessage .= $e->getMessage() . "\n";
            }
        }

        if (!empty($exceptionMessage)) {
            throw new ValidationException($exceptionMessage);
        }

        return TRUE;
    }

    public function runAllValidators () {
        $this->runAll = TRUE;
    }
}