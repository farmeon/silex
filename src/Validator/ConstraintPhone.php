<?php

namespace Validator;

use Symfony\Component\Validator\Constraint;

class ConstraintPhone extends Constraint
{
    public $message = 'Your phone "{{ string }}" is not validate';
}