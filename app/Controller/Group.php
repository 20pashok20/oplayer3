<?php
namespace Controller;
use \Silex\Application,
  \Symfony\Component\HttpFoundation\Request;

class Group implements \Silex\ControllerProviderInterface {
  public function connect( Application $app) {
    $group = $app['controllers_factory'];

    $group->match('/', function( Application $app, Request $request ) {
      return $app['twig']->render('group/index.twig', array(
        'asdasda' => 'asdasdasd'
      ));
    })->bind('group.root');

    return $group;
  }

}