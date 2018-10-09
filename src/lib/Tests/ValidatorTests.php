<?php

namespace Tests;

use Output\Http\Form;
use Output\Http\Validator\EmailValidator;
use PHPUnit\Framework\TestCase;

class ValidatorTests extends TestCase {
    public function testValidateEmail () {
        $validator = new EmailValidator();
        $form      = new Form('form_submit');
        $form->add('email', 'email', 'email');
        $_POST['email']       = 'test@localhost.de';
        $_POST['form_submit'] = '1';

        $this->assertTrue($validator->isValid($form, 'email'));
    }
}