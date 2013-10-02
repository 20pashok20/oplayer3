<?php
namespace Project;

class Cache {
  public static function get( $key, $expired, $getFunction, $recache = false ) {
    $cache = \Model\CacheQuery::create('cache')
      ->where('cache.key = ?', $key)
      ->where('cache.expiredAt > NOW() - INTERVAL ? SECOND', $expired)
      ->orderById('DESC')
      ->findOne();

    if ( !$cache || $recache ) {
      $value = $getFunction();

      $cache = new \Model\Cache;
      if ( $value ) {
        $cache->setKey( $key );
        $cache->setValue( serialize($value) );
        $cache->setExpiredAt( new \DateTime(date('Y-m-d H:i:s', time() + $expired)) );
        $cache->save();
      }
    }

    $value = $cache->getValue();
    if ( $value ) {
      return unserialize($value);
    }

    return null;
  } 
}