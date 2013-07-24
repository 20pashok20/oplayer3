<?php
namespace Project;

class LastFM {
  private $root = "http://ws.audioscrobbler.com/2.0/";

  public function __construct( $lastfmapikey, $lastfmsecret ) {
    $this->lastfmapikey = $lastfmapikey;
    $this->lastfmsecret = $lastfmsecret;
  }

  public function request($method, $params) {
    $qparams = http_build_query($params);
    $q = $this->root . "?method={$method}&format=json&api_key={$this->lastfmapikey}&{$qparams}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_URL, $q);
    $resp = curl_exec($ch);
    curl_close($ch);

    if ( !$resp ) {
      return null;
    }

    $data = json_decode($resp);
    return $data;
  }
    
}