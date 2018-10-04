<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Nette\Utils\Strings;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class StringSequenceValidator implements FormFieldValidator {

    private $maxRepeats = 2;
    private $ignoreCase = FALSE;

    public function __construct ($maxRepeats, $ignoreCase = FALSE) {
        $this->maxRepeats = $maxRepeats;
        $this->ignoreCase = $ignoreCase;
    }

    public function isValid (Form $form, $field) {
        $val = $form->get($field);

        if ($this->ignoreCase) $val = strtolower($val);

        $data = str_split($val);

        $lastOcc = NULL;
        $i       = 0;

        foreach ($data as $char) {
            if ($lastOcc == $char) $i++;
            else $i = 0;

            $lastOcc = $char;

            if ($i >= $this->maxRepeats) {
                throw new ValidationException("To high char sequence detected, security impact (found $i or more $lastOcc in a row)");
            }
        }

        if (Strings::compare($val, "abcdefghijklmnopqrstuvwxyz", $this->maxRepeats)) {
            throw new ValidationException("To high char sequence detected of the arabic alphabet, used {$this->maxRepeats} of the alphabet in a row.");
        }
    }
}