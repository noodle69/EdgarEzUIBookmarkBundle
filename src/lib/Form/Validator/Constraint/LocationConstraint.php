<?php

namespace Edgar\EzUIBookmark\Form\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

class LocationConstraint extends Constraint
{
    public $message = 'No valid location id : %message%';

    public function validatedBy(): string
    {
        return LocationConstraintValidator::class;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
