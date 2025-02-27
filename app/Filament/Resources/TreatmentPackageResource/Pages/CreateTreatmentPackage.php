<?php

namespace App\Filament\Resources\TreatmentPackageResource\Pages;

use App\Filament\Resources\TreatmentPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreatmentPackage extends CreateRecord
{
    protected static string $resource = TreatmentPackageResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 