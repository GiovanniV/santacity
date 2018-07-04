<?php

namespace Drupal\time_field\Plugin\Validation\Constraint;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimeConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
      try {
        \Drupal\time_field\Time::createFromTimestamp($value);
      } catch (\InvalidArgumentException $e) {
        $this->context->addViolation(TimeConstraint::$message, []);
      }
    }
}