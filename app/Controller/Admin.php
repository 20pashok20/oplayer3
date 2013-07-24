<?php
namespace Controller;

class Admin implements \Silex\ControllerProviderInterface {

  public function connect(\Silex\Application $app) {
    $admin = $app['controllers_factory'];
    $admin->before(function() {
      echo 'asd';
    });


    $admin->get('/', function() use ($app) { 
      return 'Hello '; 
    }); 

    return $admin;
  }

}