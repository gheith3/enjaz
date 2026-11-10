<?php

namespace App\Filament\Resources\AwardFeatures\Pages;

use App\Filament\Resources\AwardFeatures\AwardFeatureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAwardFeature extends CreateRecord
{
    protected static string $resource = AwardFeatureResource::class;

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
