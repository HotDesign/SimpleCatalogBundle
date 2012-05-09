<?php

namespace HotDesign\SimpleCatalogBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use ConfigClasses\MyConfig;

class ImageAttachmentLimitConstraintValidator extends ConstraintValidator
{
    public function isValid($value, Constraint $constraint)
    {
        if ($value >= MyConfig::$max_image_per_item) {
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }
}