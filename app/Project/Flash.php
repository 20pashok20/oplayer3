<?php
namespace Project;

class Flash {
  public function get() {
    if ( isset( $_SESSION['flash'] ) ) {
      $flashes = $_SESSION['flash'];
      unset( $_SESSION['flash'] );

      return $flashes;
    }

    return array();
  }

  public function set( $level, $msg ) {
    if ( !isset($_SESSION['flash']) ) {
      $_SESSION['flash'] = array();
    }

    $msg = array(
      'level' => $level,
      'msg' => $msg
    );

    $_SESSION['flash'][] = $msg;
  }
    
}