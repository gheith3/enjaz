<?php

namespace App\Filament\Resources\AwardEditions\Pages;

use App\Filament\Resources\AwardEditions\AwardEditionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAwardEdition extends CreateRecord
{
    protected static string $resource = AwardEditionResource::class;

    /** Opened from a parent page with ?award= / ?edition=: start with it selected. */
    protected function fillForm(): void
    {
        parent::fillForm();
        $this->form->fill(array_filter([
            'award_id' => request()->integer('award') ?: null,
            'award_edition_id' => request()->integer('edition') ?: null,
        ]) + $this->form->getRawState());
    }
}
