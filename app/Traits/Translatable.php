<?php

namespace App\Traits;

trait Translatable
{
    public function getTranslatableFields()
    {
        return $this->translationFields;
    }
}
