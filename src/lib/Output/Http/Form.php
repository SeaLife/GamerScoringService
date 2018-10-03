<?php

namespace Output\Http;

class Form {

    private $id;

    private $location;

    private $mode;

    private $formItems = array();

    public function __construct ($id, $location = "#", $mode = 'simple') {
        $this->id       = $id;
        $this->location = $location;
        $this->mode     = $mode;
    }

    public function add ($type, $name, $display) {
        array_push($this->formItems, array("type" => $type, "name" => $name, "display" => $display));
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

        return $content;
    }

    public function isSubmitted () {
        return isset($_POST[$this->id]) && !empty($_POST[$this->id]);
    }

    public function view () {
        $this->getOutput()->render();
    }
}