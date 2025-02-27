<?php

namespace App\Filament\Resources\CustomerTreatmentResource\Pages;

use App\Filament\Resources\CustomerTreatmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerTreatment extends EditRecord
{
    protected static string $resource = CustomerTreatmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('刪除客戶療程'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 