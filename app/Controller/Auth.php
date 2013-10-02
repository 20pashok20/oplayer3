<?php
namespace Controller;
use \Silex\Application,
  \Symfony\Component\HttpFoundation\Request;

class Auth implements \Silex\ControllerProviderInterface {
  public function connect( Application $app) {
    $auth = $app['controllers_factory'];

    $auth->match('/register', function( Application $app, Request $request ) {
      if ( $app['user']::get() ) {
        return $app->redirect(
          $app['url_generator']->generate('index')
        );
      }

      $user = new \Model\User;
      $form = $app['form.factory']->createBuilder('form', $user)
        ->add('email', 'text', array(
          'constraints' => new \Project\Validator\EmailUnique,
          'label' => 'Email: '
        ))
        ->add('name', 'text', array(
          'label' => 'Имя: '
        ))
        ->add('password', 'password', array(
          'label' => 'Пароль: '
        ))
        ->getForm();

      if ( 'POST' == $request->getMethod() ) {
        $form->bind($request);

        if ( $form->isValid() ) {
          $confirmationtoken = sha1(
            $user->getEmail() . uniqid()
          );

          $user->setConfirmationtoken($confirmationtoken);
          $salt = uniqid();
          $user->setSalt($salt);
          $user->setPassword(
            $app['user']::getPass( $user->getPassword(), $salt )
          );
          $user->save();

          $app['user']::set($user);

          $host = $request->server->get('HTTP_HOST');
          $html = $app['twig']->render('mail/confirm.twig', array(
            'confirmationtoken' => $confirmationtoken,
            'host' => $host
          ));
          if ( $app['debug'] ) {
            $app['monolog']->addInfo( 'Registration mail: ' . $html );
          } else {
            // $message = \Swift_Message::newInstance()
            //   ->setSubject($app['translator']->trans('Confirmation')." Email на {$host}")
            //   ->setFrom($app['conf']['app']['backmail'])
            //   ->setTo($user->email)
            //   ->setBody($html);
            // $app['mailer']->send($message);
          }

          return $app->redirect(
            $app['url_generator']->generate('index')
          );
        }
      }

      return $app['twig']->render('auth/register.twig', array(
        'form' => $form->createView()
      ));
    })->bind('auth.register');

    $auth->match('/login', function( Application $app, Request $request ) {
      if ( $app['user']::get() ) {
        return $app->redirect(
          $app['url_generator']->generate('index')
        );
      }
      
      $user = new \Model\User;
      $user->setName('Guest');
      $requestForm = $request->get('form');
      $password = null;

      if ( isset($requestForm['password']) ) {
        $password = $requestForm['password'];
      }

      $form = $app['form.factory']->createBuilder('form', $user)
        ->add('email', 'text', array(
          'constraints' => new \Project\Validator\UserExist(array(
            'password' => $password
          )),
          'attr' => array('placeholder' => 'Эл.почта'),
          'label' => "Эл.почта"
        ))
        ->add('password', 'password', array(
          'attr' => array('placeholder' => 'Пароль'),
          'label' => "Пароль"
        ))
        ->getForm();

      if ( 'POST' == $request->getMethod() ) {
        $form->bind($request);

        if ( $form->isValid() ) {
          $luser = \Model\UserQuery::create()
            ->filterByEmail( $user->getEmail() )
            ->findOne();

          $app['user']::set( $luser );

          return $app->redirect(
            $app['url_generator']->generate('index')
          );
        }
      }

      return $app['twig']->render('auth/login.twig', array(
        'form' => $form->createView(),
      ));
    })->bind('auth.login');

    $auth->get('/logout', function( Application $app ) {
      $app['user']::logout();

      return $app->redirect(
        $app['url_generator']->generate('index')
      );
    })->bind('auth.logout');

    $auth->get('/confirm/{token}', function( Application $app, Request $request, $token ) {
      $user = \Model\UserQuery::create()
        ->filterByConfirmationtoken( $token )
        ->findOne();
      $user->setConfirmed(1);
      $confirmationtoken = sha1( $user->getEmail() . uniqid() );
      $user->setConfirmationtoken($confirmationtoken);
      $user->save();

      $app['user']::set( $user );

      return $app->redirect(
        $app['url_generator']->generate('index')
      );
    })->bind('auth.confirm');

    return $auth;
  }

}