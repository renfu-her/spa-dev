<?php

namespace App\Filament\Resources\CustomerTreatmentResource\Pages;

use App\Filament\Resources\CustomerTreatmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomerTreatment extends CreateRecord
{
    protected static string $resource = CustomerTreatmentResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 