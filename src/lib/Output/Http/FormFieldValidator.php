<?php

namespace Output\Http;

interface FormFieldValidator {
    /**
     * @param Form $form
     * @param      $field
     * @return bool
     */
    public function isValid (Form $form, $field);
}