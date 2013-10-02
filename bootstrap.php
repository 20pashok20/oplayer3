<?php
session_start();
date_default_timezone_set('Europe/Moscow');
$app = new Silex\Application;

// Must be setted to false in production mode.
$forceDebug = false;
if ( '127.0.0.1' == getenv('REMOTE_ADDR') || $forceDebug ) {
  $app['debug'] = true;
  // ini_set('display_errors', 'on');
  $app->register(new Whoops\Provider\Silex\WhoopsServiceProvider);
}

// Controllers
$app->mount('/', new Controller\Root);
$app->mount('/auth', new Controller\Auth);
$app->mount('/admin', new Controller\Admin);
$app->mount('/group', new Controller\Group);

// --- Services ---
// Propel
$app->register(new Propel\Silex\PropelServiceProvider, array(
  'propel.config_file' => __DIR__.'/app/Config/propel-conf.php',
  'propel.model_path'  => __DIR__.'/app/Model'
));

// Twig
$app->register(new Silex\Provider\TwigServiceProvider, array(
  'twig.path' => __DIR__.'/app/View',
  'twig.options' => array(
    'debug' => $app['debug'],
    'cache' => __DIR__ . '/cache',
    'auto_reload' => $app['debug'],
    'strict_variables' => $app['debug']
  )
));

// Form
$app->register(new Silex\Provider\FormServiceProvider);
$app->register(new Silex\Provider\ValidatorServiceProvider);

// Url generator
$app->register(new Silex\Provider\UrlGeneratorServiceProvider);

// Translation
$app->register(new Silex\Provider\TranslationServiceProvider, array(
  'locale_fallback' => 'en',
));

// Flash
$app['flash'] = $app->share(function () {
  return new Project\Flash;
});

// User
$app['user'] = $app->share(function () {
  return new Project\User;
});

// Config
$app['conf'] = $app->share(function () use ($app) {
  return parse_ini_file('app/Config/app.ini');
});

// LastFM
$app['lastfm'] = $app->share(function () use ($app) {
  return new Project\LastFM(
    $app['conf']['lastfmapikey'],
    $app['conf']['lastfmsecret']
  );
});

// OpenPlayer
$app['openplayer'] = $app->share(function () use ($app) {
  return new Project\OpenPlayer(
    $app['conf']['apps']
  );
});
// Project\OpenPlayer::$app = $app;

if ( $app['debug'] ) {
  // Monolog
  $app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/cache/dev.log',
  ));
}

//
function collectValues( $collection, $field, $keyfield = null ) {
  $items = array();
  foreach ( $collection as $k => $item ) {
    $key = $keyfield ? $item->{$keyfield} : $k;
    if ( false === $field ) {
      $items[$key] = $item;
    } else {
      $items[$key] = $field 
        ? $item->{$field}
        : $item->__toString();
    }
  }

  return $items;
}

// SendMail hack
function sendMail( $from, $to, $subject, $text ) {
  set_include_path(
    get_include_path() . PATH_SEPARATOR .
    __DIR__ . "/app/Project/Mail/"
  );
  require_once 'Mail.php';
  require_once 'Mail/mime.php' ;

  $crlf = "\n";
  $hdrs = array(
    'From' => $from,
    'Subject' => $subject,
    'Content-Type' => 'text/html; charset=UTF-8',
//    'Content-Transfer-Encoding' => '8bit',
  );

  $mime = new Mail_mime(array('eol' => $crlf));
  $mime->setTXTBody(strip_tags($text));
  $mime->setHTMLBody($text);
//  $mime->addAttachment($file, 'text/plain');
  $body = $mime->get(array(
    'text_encoding' => "8bit",
    'text_charset'  => "UTF-8",
    'html_charset'  => "UTF-8",
    'head_charset'  => "UTF-8",
  ));
//  $body = $mime->get();
  $hdrs = $mime->headers($hdrs);

  $mail = &Mail::factory('mail');//, "-f {$from}"
  $status = $mail->send($to, $hdrs, $body);

  return $status;
}

return $app;