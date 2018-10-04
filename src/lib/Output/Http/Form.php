<?php

namespace Output\Http;

use Globals\Exception\ValidationException;
use Output\Http\Validator\CaptchaValidator;
use ReCaptcha\ReCaptcha;

class Form {

    private $id;
    private $location;
    private $mode;
    private $formItems      = array();
    private $lastError      = NULL;
    private $lastErrorField = NULL;

    public function __construct ($id, $location = "#", $mode = 'simple') {
        $this->id       = $id;
        $this->location = $location;
        $this->mode     = $mode;
    }

    public function add ($type, $name, $display, $validator = NULL, $val = NULL) {
        array_push($this->formItems, array(
            "type"      => $type,
            "name"      => $name,
            "display"   => $display,
            "validator" => $validator,
            "value"     => $val != NULL ? $val : isset($_POST[$name]) ? $_POST[$name] : ""
        ));
    }

    public function enableCaptcha () {
        $captcha = new ReCaptcha(envvar("RECAPTCHA_SECRET"));
        $this->add("captcha", "captcha", NULL, new CaptchaValidator($captcha));
    }

    public function isValid () {
        if ($this->isSubmitted()) {
            foreach ($this->formItems as $item) {
                if ($item["validator"] != NULL) {
                    /** @var $validator FormFieldValidator */
                    $validator = $item["validator"];

                    try {
                        $valid = $validator->isValid($this, $item["name"]);

                        if (!$valid && !is_null($valid)) {
                            $this->lastError      = $item["name"];
                            $this->lastErrorField = $item["name"];

                            return FALSE;
                        }
                    } catch (ValidationException $e) {
                        $this->lastError      = $e->getMessage();
                        $this->lastErrorField = $item["name"];

                        return FALSE;
                    }
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    public function get ($name) {
        if ($this->isSubmitted()) {
            return $_POST[$name];
        }
        return NULL;
    }

    public function getFormItems () {
        return $this->formItems;
    }

    public function getOutput () {
        $content = new Content("base/form-{$this->mode}.html.twig");

        $content->assign("items", $this->getFormItems());
        $content->assign("id", $this->id);
        $content->assign("submit_button", "Submit");
        $content->assign("location", $this->location);
        $content->assign("error_message", $this->lastError);
        $content->assign("error_field", $this->lastErrorField);
        $content->assign("captcha_client", envvar("RECAPTCHA_CLIENT_SECRET"));

        return $content;
    }

    public function isSubmitted () {
        return isset($_POST[$this->id]) && !empty($_POST[$this->id]);
    }

    public function view () {
        $this->getOutput()->render();
    }
}