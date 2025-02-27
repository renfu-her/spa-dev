<?php

namespace App\Filament\Resources\TreatmentUsageRecordResource\Pages;

use App\Filament\Resources\TreatmentUsageRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTreatmentUsageRecord extends CreateRecord
{
    protected static string $resource = TreatmentUsageRecordResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 