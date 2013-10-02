<?php
namespace Project;

class OpenPlayer {
  // public static $app = null;
  public $apps = null;
  public $access_token = null;

  private $api = "http://www.appsmail.ru/platform/api";

  public function __construct( $apps ) {
    $this->apps = $apps[
      array_rand($apps)
    ];
  }

  public function auth() {
    $cookie = __DIR__ . '/../../cache/cookie' . sha1($this->apps);
    $app = explode(':', $this->apps);

    list($login, $domain) = explode('@', $app[0]);

    $params = array(
      'Login' => $login,
      'Domain' => $domain,
      'Password' => $app[1],
      'saveauth' => 1,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://auth.mail.ru/cgi-bin/auth");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $resp = curl_exec($ch);
    curl_close($ch);
  }

  public function getToken($reget = false) {
    if ( !$this->access_token ) {
      $cookie = __DIR__ . '/../../cache/cookie' . sha1($this->apps);
      $app = explode(':', $this->apps);

      $params = array(
        'client_id' => $app[2],
        'response_type' => 'token',
        'display' => 'mobile',
      );
      $httpQuery = http_build_query($params);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://connect.mail.ru/oauth/authorize?{$httpQuery}");
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_TIMEOUT, 20);

      $resp = curl_exec($ch);
      curl_close($ch);

      if ( !$reget && false === strpos($resp, 'access_token') ) {
        $this->auth();
        return $this->getToken(true);
      }
      preg_match_all("/access_token=([\d\w]*)/", $resp, $matches);

      if ( !isset($matches[1][0]) ) {
        echo $resp;die;
      }

      $this->access_token = $matches[1][0];
    }
    
    return $this->access_token;
  }

  public function audioLink( $mid ) {
    $app = explode(':', $this->apps);

    $params = array(
      'method' => 'audio.link',
      'app_id' => $app[2],
      'secure' => '1',
      'mid' => $mid,
      'session_key' => $this->getToken()
    );

    ksort($params);
    $psig = '';
    foreach ( $params as $key => $value ) {
      $psig .= "{$key}={$value}";
    }
    $params['sig'] = md5($psig . $app[3]);

    $httpQuery = http_build_query($params);

    $result = $this->file_get_contents_curl("{$this->api}?{$httpQuery}");
    $result = json_decode($result);

    return $result;
  }

  public function audioGetById( $mid, $reget = false ) {
    $app = explode(':', $this->apps);

    $params = array(
      'method' => 'audio.get',
      'app_id' => $app[2],
      'secure' => '1',
      'mids' => $mid,
      'session_key' => $this->getToken()
    );

    ksort($params);
    $psig = '';
    foreach ( $params as $key => $value ) {
      $psig .= "{$key}={$value}";
    }
    $params['sig'] = md5($psig . $app[3]);

    $httpQuery = http_build_query($params);

    $result = $this->file_get_contents_curl("{$this->api}?{$httpQuery}");
    $result = json_decode($result);

    if ( !$result && !$reget && $this->audioLink($mid) ) {
      return $this->audioGetById($mid);
    } elseif ( !$result && $reget ) {
      return null;
    }

    return reset($result);
  }

  public function remoteFilesize( $url ) {
    $head = get_headers( $url, 1 );

    return isset( $head['Content-Length'] )
      ? $head['Content-Length']
      : "unknown";
  }

  public function search( $request ) {
    $app = explode(':', $this->apps);

    $count = isset($request['count'])
      ? $request['count']
      : 200;

    $p = isset($request['p'])
      ? $request['p']
      : 0;

    $params = array(
      'method' => 'audio.search',
      'app_id' => $app[2],
      'secure' => '1',
      'query' => isset($request['q']) ? $request['q'] : '',
      'limit' => $count,
      'offset' => $p * $count,
      'session_key' => $this->getToken()
    );

    ksort($params);
    $psig = '';
    foreach ( $params as $key => $value ) {
      $psig .= "{$key}={$value}";
    }
    $params['sig'] = md5($psig . $app[3]);

    $httpQuery = http_build_query($params);

    $result = $this->file_get_contents_curl("{$this->api}?{$httpQuery}");
    $result = json_decode($result);

    return array(
      'count' => $result->total,
      'tracks' => $result->result
    );
  }

  public function file_get_contents_curl($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);   

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
  }

}