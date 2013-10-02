<?php

namespace Project\Validator;

use Symfony\Component\Validator\Constraint;

class UserExist extends Constraint {

    public $message = 'User не найден.';
    public $password;

    public function getDefaultOption() {
        return 'password';
    }

    public function getRequiredOptions() {
        return array('password');
    }

}
