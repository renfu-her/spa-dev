<?php

namespace App\Filament\Resources\CustomerTreatmentResource\Pages;

use App\Filament\Resources\CustomerTreatmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomerTreatments extends ListRecords
{
    protected static string $resource = CustomerTreatmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('新增客戶療程'),
        ];
    }
} 