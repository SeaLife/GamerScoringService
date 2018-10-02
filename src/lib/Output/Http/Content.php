<?php

namespace Output\Http;

class Content {

    private $file    = '';
    private $context = array();

    public function __construct ($file, $context = array()) {
        $this->file    = $file;
        $this->context = $context;
    }

    public function assign ($key, $value) {
        $this->context[$key] = $value;
        return $this;
    }


    public function getFile () {
        return $this->file;
    }

    public function getContext () {
        return $this->context;
    }
}