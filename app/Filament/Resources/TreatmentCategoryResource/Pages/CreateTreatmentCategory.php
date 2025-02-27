<?php

namespace App\Filament\Resources\TreatmentCategoryResource\Pages;

use App\Filament\Resources\TreatmentCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreatmentCategory extends CreateRecord
{
    protected static string $resource = TreatmentCategoryResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 