<?php
namespace Project;

class PfAdapter implements \Pagerfanta\Adapter\AdapterInterface {
  private $classname = null;
  private $params = null;

  public function __construct( $classname, $params = array() ) {
    $this->classname = $classname;
    $this->params = $params;
  }

  public function getNbResults() {
    $params = array(
      'select' => 'COUNT(*) as cnt',
      'group' => null,
    );

    if ( $this->params ) {
      $params = array_merge( $this->params, $params );
    }

    $cnt = call_user_func_array(
      array($this->classname, "find"), 
      array($params)
    );

    if ( !$cnt ) {
      return 0;
    }

    return $cnt->cnt;
  }


  public function getSlice($offset, $length) {
    $params = array(
      'limit' => $length,
      'offset' => $offset
    );

    if ( $this->params ) {
      $params = array_merge($params, $this->params);
    }

    return call_user_func_array(
      array($this->classname, "all"), 
      array($params)
    );
  }
}