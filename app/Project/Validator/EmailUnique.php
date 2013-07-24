<?php

namespace Project\Validator;
use Symfony\Component\Validator\Constraint;

class EmailUnique extends Constraint {
  public $message = 'Email %string% уже зарегистрирован';
  
  public $email;

  public function getDefaultOption() {
    return 'email';
  }

}