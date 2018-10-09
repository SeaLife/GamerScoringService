<?php

use App\Entity\Role;
use Globals\DB;
use Output\Http\Form;
use Output\Http\Validator\EmailValidator;
use Output\Http\Validator\EqualsValidator;
use Output\Http\Validator\HasDatabaseEntryValidator;
use Output\Http\Validator\HasNoDatabaseEntryValidator;
use Output\Http\Validator\PasswordValidator;
use Output\Http\Validator\StringLengthLimitValidator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {

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
     *          [0, "Da"]
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

    public function testEqualsValidator () {
        $validator = new EqualsValidator('123456');
        $form      = $this->getFormFor('test', '123456');

        $this->assertTrue($validator->isValid($form, 'test'));
    }

    /**
     * @expectedException \Globals\Exception\ValidationException
     */
    public function testNotEqualsValidator () {
        $validator = new EqualsValidator('1234567');
        $form      = $this->getFormFor('test', '123456');

        $validator->isValid($form, 'test');
    }

    public function testHasNoDatabaseEntry () {
        $validator = new HasNoDatabaseEntryValidator(Role::class, 'name');
        $form      = $this->getFormFor('test', 'role-not-found1234');

        $this->assertTrue($validator->isValid($form, 'test'));
    }

    /**
     * @expectedException \Globals\Exception\ValidationException
     */
    public function testHasNoDatabaseEntryFailed () {
        $role = new Role();
        $role->setName('admin');

        DB::getInstance()->getEntityManager()->persist($role);
        DB::getInstance()->getEntityManager()->flush();

        $validator = new HasNoDatabaseEntryValidator(Role::class, 'name');
        $form      = $this->getFormFor('test', 'admin');

        $validator->isValid($form, 'test');
    }

    /**
     * @depends testHasNoDatabaseEntryFailed
     */
    public function testHasDatabaseEntry () {
        $validator = new HasDatabaseEntryValidator(Role::class, 'name');
        $form      = $this->getFormFor('test', 'admin');

        $this->assertTrue($validator->isValid($form, 'test'));
    }

    /**
     * @expectedException \Globals\Exception\ValidationException
     * @depends testHasDatabaseEntry
     */
    public function testHasDatabaseEntryFailed () {
        $validator = new HasDatabaseEntryValidator(Role::class, 'name');
        $form      = $this->getFormFor('test', 'role-not-found1234');

        $validator->isValid($form, 'test');
    }

    /**
     * @expectedException \Globals\Exception\ValidationException
     */
    public function testStringLimiterValidatorFailed () {
        $validator = new StringLengthLimitValidator(2);
        $form      = $this->getFormFor('test', 'exceed');

        $validator->isValid($form, 'test');
    }

    /**
     */
    public function testStringLimiterValidator () {
        $validator = new StringLengthLimitValidator(2);
        $form      = $this->getFormFor('test', 'ex');

        $this->assertTrue($validator->isValid($form, 'test'));
    }
}