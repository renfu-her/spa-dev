<?php

namespace App\Filament\Resources\TreatmentItemResource\Pages;

use App\Filament\Resources\TreatmentItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreatmentItem extends CreateRecord
{
    protected static string $resource = TreatmentItemResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 