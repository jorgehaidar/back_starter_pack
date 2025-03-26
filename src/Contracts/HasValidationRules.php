<?php

namespace Mbox\BackCore\Contracts;

interface HasValidationRules
{
    /**
     * Define validation rules based on a given scenario.
     *
     * @param string $scenario The validation scenario ('create', 'update', etc.)
     * @return array The validation rules for the specified scenario.
     */
    public function rules(string $scenario = 'create'): array;
}
