<?php
namespace Project;

class Register {
  private $reg = array();

  public function set( $key, $value ) {
    $this->reg[$key] = $value;
  }

  public function get( $key, $default = null ) {
    if ( isset($this->reg[$key]) && $this->reg[$key] ) {
      return $this->reg[$key];
    }

    return $default;
  }
}