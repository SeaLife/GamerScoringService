<?php

namespace Output\Http\Validator;

use Globals\Exception\ValidationException;
use Output\Http\Form;
use Output\Http\FormFieldValidator;
use ReCaptcha\ReCaptcha;

/**
 * @codeCoverageIgnore
 */
class CaptchaValidator implements FormFieldValidator {

    /** @var $captcha ReCaptcha */
    private $captcha;

    public function __construct ($cpt) {
        $this->captcha = $cpt;
    }

    public function isValid (Form $form, $field) {
        $resp = $this->captcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$resp->isSuccess()) {
            throw new ValidationException("Captcha is missing");
        }
    }
}