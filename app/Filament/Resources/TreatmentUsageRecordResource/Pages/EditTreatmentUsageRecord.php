<?php

namespace App\Filament\Resources\TreatmentUsageRecordResource\Pages;

use App\Filament\Resources\TreatmentUsageRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTreatmentUsageRecord extends EditRecord
{
    protected static string $resource = TreatmentUsageRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('刪除使用記錄'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 