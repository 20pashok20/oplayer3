<?php

namespace Project;

class User {
  private static $storage = null;
  private static $perms = array();

  public static function get( $field = null ) {
    $user = self::$storage;

    if ( $user ) {
      if ( $field ) {
        $field = strtolower($field);
        $data = $user->toArray();
        $newdata = array();
        foreach ( $data as $key => $value ) {
          $newdata[strtolower($key)] = $value;
        }
        return isset( $newdata[$field] ) ? $newdata[$field] : null ;
      }

      return $user;
    }

    return null;
  }

  public static function genKey() {
    return sha1( self::get('id') . uniqid() );
  }

  public static function set( $user ) {
    self::$storage = $user;

    $_SESSION['user'] = $user;
    // $_SESSION['user'] = self::$storage->toJSON();

    // session
    $sess = \Model\UserSessionQuery::create()
      ->filterByUserId( $user->getId() )
      ->findOne();
    if ( !$sess ) {
      $sess = new \Model\UserSession;
      $sess->setUser( $user );
      $sess->setSesskey( self::genKey() );
    }

    $expire = time() + 60 * 60 * 24 * 30;
    $sess->setExpiredAt( date("Y-m-d H:i:s", $expire) );
    $sess->save();

    setcookie("sesskey", $sess->getSesskey(), $expire, '/');
  }

  public function __construct() {
    if ( isset( $_SESSION['user'] ) ) {
      self::$storage = $_SESSION['user'];
    } else {
      if ( isset( $_COOKIE['sesskey'] ) ) {
        $sess = \Model\UserSessionQuery::create()
          ->filterBySesskey( $_COOKIE['sesskey'] )
          ->findOne();

        if ( $sess ) {
          self::set( $sess->getUser() );
        }
      }
    }

    if ( $user = self::get() ) {
      $user->setLastVisit(new \DateTime);
      $user->save();
    }
  }

  public static function logout() {
    self::$storage = null;
    unset( $_SESSION['user'] );

    $_COOKIE = null;
    setcookie("sesskey", null, 0, '/');
  }
  
  public static function getPass( $pass, $salt ) {
    return md5( $pass . $salt );
  }

}