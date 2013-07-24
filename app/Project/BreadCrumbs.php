<?php
namespace Project;

class BreadCrumbs {
  private $bc = array();

  public function add( $path, $name ) {
    $this->bc[$path] = $name;
  }

  public function getbc() {
    return $this->bc;
  }
}