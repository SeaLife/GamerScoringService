<?php

namespace Output\Http\Validator;

use Globals\DB;
use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;

class HasNoDatabaseEntryValidator implements FormFieldValidator {
    private $entity;
    private $column;
    private $error;

    public function __construct ($entity, $column, $error = ":column: already exist.") {
        $this->entity = $entity;
        $this->column = $column;
        $this->error  = $error;
    }

    public function isValid (Form $form, $field) {
        $exists = DB::getInstance()->getEntityManager()->getRepository($this->entity)->findOneBy(array($this->column => $form->get($field)));

        if ($exists != NULL) {
            $msg = str_replace(":column:", ucfirst($this->column), $this->error);
            throw new ValidationException($msg);
        }

        return TRUE;
    }
}