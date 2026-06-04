<?php

namespace App\StainTypes\Contracts;

interface StainTypeDefinition
{
    public function label(): string;

    /** Validation rules for the type_data payload sent from the form. */
    public function rules(): array;

    /** Blank type_data structure used to initialise the Livewire form. */
    public function defaultData(): array;

    /** True if the form supports adding multiple block/slide rows. */
    public function supportsMultipleBlocks(): bool;

    /** Blade partial path rendered inside the stain-type form section. */
    public function formPartial(): string;
}
