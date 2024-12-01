<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueProduct extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Le produit "{{ name }}, {{ brand }}, {{ year }}" existe dejà dans la base de données.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
