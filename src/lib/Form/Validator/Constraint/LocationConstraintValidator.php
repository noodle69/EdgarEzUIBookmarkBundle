<?php

namespace Edgar\EzUIBookmark\Form\Validator\Constraint;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LocationConstraintValidator extends ConstraintValidator
{
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function validate($value, Constraint $constraint)
    {
        try {
            $this->locationService->loadLocation($value);
        } catch (UnauthorizedException | NotFoundException $e) {
            $this->context->addViolation(
                $constraint->message,
                ['%message%' => $e->getMessage()]
            );
        }
    }
}
