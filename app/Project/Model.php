<?php
namespace Project;

class Model extends \ActiveRecord\Model {
    public function __call($method, $args) {
        echo $method;die;
        parent::__call($method, $args);
    }
    
    public function __get($name) {
        parent::__get($name);
    }
}