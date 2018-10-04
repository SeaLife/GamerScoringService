<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class PwnedPasswordValidator implements FormFieldValidator {

    private $limitUsage = 0;

    public function __construct ($limitUsage = 0) {
        $this->limitUsage = $limitUsage;
    }

    public function isValid (Form $form, $field) {
        $pw   = $form->get($field);
        $sha1 = strtoupper(sha1($pw));

        $key   = substr($sha1, 0, 5);
        $check = substr($sha1, 5);

        $data = file_get_contents("https://api.pwnedpasswords.com/range/" . $key);

        $dataSet = explode("\n", $data);

        foreach ($dataSet as $row) {
            $rowData = explode(":", $row);

            $hash = $rowData[0];

            if ($hash == $check && $rowData[1] * 1 >= $this->limitUsage) {
                throw new ValidationException("Password exist in the Pwned Passwords DB and is not allowed to be used here (was used {$rowData[1]} times on other breached websites).");
            }
        }
    }
}