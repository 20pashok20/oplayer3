<?php
$data = file_get_contents(
  __DIR__ . '/build.properties'
);
$conf = array();
foreach ( explode("\n", $data) as $line ) {
  if ( trim($line) ) {
    $c = explode('=', $line, 2);
    
    $key = $c[0];
    $conf[trim($key)] = isset($c[1])
      ? trim($c[1])
      : null;
  }
}

return array(
  'datasources' => array(
    $conf['propel.project'] => array(
      'adapter' => $conf['propel.database'],
      'connection' => array(
        'dsn' => $conf['propel.database.url'],
        'user' => $conf['propel.database.user'],
        'password' => isset($conf['propel.database.password'])
          ? $conf['propel.database.password']
          : null,
        'settings' => array(
          'charset' => $conf['propel.database.encoding'],
        )
      )
    )
  )
);