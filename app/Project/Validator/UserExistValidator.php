<?php

namespace Project\Validator;
 
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
 
class UserExistValidator extends ConstraintValidator {
    
  public function validate($value, Constraint $constraint) {
    $user = \Model\UserQuery::create()
      ->filterByEmail($value)
      ->findOne();

    if ( !$user || $user->getPassword() != \Project\User::getPass( $constraint->password, $user->getSalt() ) ) {
      $this->context->addViolation( $constraint->message, array() );
    }
  }
    
}