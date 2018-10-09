<?php

use Output\Http\Form;
use Output\Http\Validator\EmailValidator;
use Output\Http\Validator\PasswordValidator;
use PHPUnit\Framework\TestCase;

class ValidatorTests extends TestCase {

    private function getFormFor ($field, $value) {
        $form = new Form('test_case');
        $form->add('text', $field, $field);

        $_POST[$field]      = $value;
        $_POST['test_case'] = '1';
        return $form;
    }

    public function testValidateEmail () {
        $validator = new EmailValidator();
        $form      = $this->getFormFor('email', 'test@localhost.de');

        $this->assertTrue($validator->isValid($form, 'email'));
    }

    /**
     * @expectedException \Globals\Exception\ValidationException
     */
    public function testValidateFailedEmail () {
        $validator = new EmailValidator();
        $form      = $this->getFormFor('email', 'none');

        $validator->isValid($form, 'email');
    }

    /**
     * @testWith
     *          [0, "Demo"]
     *          [1, "Demo1"]
     *          [2, "Demo1!"]
     *          [3, "Demo1!"]
     *          [4, "Demo1!Hans"]
     *          [5, "Demo1!Hans"]
     *          [8, "Demo1!Hans"]
     *          [10, "Demo1!Hans"]
     *          [4, "abcdefABCD!13YP"]
     *          [1, "p..P§4?U%mb8NS6p"]
     *          [10, "p..P§4?U%mb8NS6p"]
     *
     * @param $level
     * @param $password
     */
    public function testPasswordLevelMatching ($level, $password) {
        $validator = new PasswordValidator($level);
        $form      = $this->getFormFor('password', $password);

        $this->assertTrue($validator->isValid($form, 'password'));
    }

    /**
     * @expectedException \Globals\Exception\ValidationException
     * @testWith
     *          [0, "demo"]
     *          [1, "Demo"]
     *          [2, "Demo1"]
     *          [5, "klopapier123"]
     *          [8, "aaaaaaa1!A"]
     *          [10, "abcdefABCD!13YP"]
     *
     * @param $level
     * @param $password
     */
    public function testPasswordLevelFailedMatching ($level, $password) {
        $validator = new PasswordValidator($level);
        $form      = $this->getFormFor('password', $password);

        $validator->isValid($form, 'password');
    }
}