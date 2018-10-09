<?php

use Output\Http\Content;
use Output\Http\Form;
use Output\Http\Validator\EmailValidator;
use Output\Http\Validator\StringLengthValidator;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase {
    public function testFormBasics () {
        $form = new Form('id');
        $this->assertNotEmpty($form);
    }

    public function testEnableCaptcha () {
        setenv("RECAPTCHA_SECRET", "1122334455");
        $form = new Form('id');
        $form->enableCaptcha();
        $this->assertNotEmpty($form);
    }

    public function testIsNotSubmitted () {
        $form = new Form('id');
        $this->assertFalse($form->isSubmitted());
    }

    public function testIsSubmitted () {
        $form        = new Form('id');
        $_POST['id'] = '1';
        $this->assertTrue($form->isSubmitted());
    }

    public function testIsValidNoValidators () {
        $form = new Form('id');
        $form->add('text', 'name', 'name');
        $_POST['id']   = '1';
        $_POST['name'] = 'Hansolo';

        $this->assertTrue($form->isValid());
    }

    public function testIsValidSimpleStringValidation () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $_POST['id']   = '1';
        $_POST['name'] = 'Hansolo';

        $this->assertTrue($form->isValid());
    }

    public function testIsNotValidSimpleStringValidation () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $_POST['id']   = '1';
        $_POST['name'] = 'Ha';

        $this->assertFalse($form->isValid());
    }

    public function testIsValidMoreValidators () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $form->add('text', 'email', 'email', new EmailValidator());
        $_POST['id']    = '1';
        $_POST['name']  = 'Hansolo';
        $_POST['email'] = 'sealife@github.com';

        $this->assertTrue($form->isValid());
    }

    public function testIsNotValidMoreValidatorsOnFailure () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $form->add('text', 'email', 'email', new EmailValidator());
        $_POST['id']    = '1';
        $_POST['name']  = 'Hansolo';
        $_POST['email'] = 'sealife';

        $this->assertFalse($form->isValid());
    }

    public function testFormItemsExist () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $form->add('text', 'email', 'email', new EmailValidator());

        $this->assertEquals(2, count($form->getFormItems()));
    }

    public function testGetHttpOutput () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $form->add('text', 'email', 'email', new EmailValidator());

        $this->assertEquals(Content::class, get_class($form->getOutput()));
    }

    public function testView () {
        $form = new Form('id');
        $form->add('text', 'name', 'name', new StringLengthValidator(3));
        $form->add('text', 'email', 'email', new EmailValidator());

        ob_start();
        $form->view();
        $data = ob_get_contents();
        ob_end_clean();

        $this->assertNotEmpty($data);
    }
}