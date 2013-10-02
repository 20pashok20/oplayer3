<?php

namespace Project\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailUniqueValidator extends ConstraintValidator {
  
  public function validate( $value, Constraint $constraint ) {
    $users = \Model\UserQuery::create()
      ->filterByEmail($value)
      ->find();

    if ( count($users) && $value != $constraint->email ) {
      $this->context->addViolation(
      	$constraint->message, array('%string%' => $value)
    	);
    }
  }
  
}