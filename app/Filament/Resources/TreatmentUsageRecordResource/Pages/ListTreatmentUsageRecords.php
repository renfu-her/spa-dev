<?php

namespace App\Filament\Resources\TreatmentUsageRecordResource\Pages;

use App\Filament\Resources\TreatmentUsageRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreatmentUsageRecords extends ListRecords
{
    protected static string $resource = TreatmentUsageRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('新增使用記錄'),
        ];
    }
} 