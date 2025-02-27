<?php

namespace App\Filament\Resources\TreatmentItemResource\Pages;

use App\Filament\Resources\TreatmentItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTreatmentItem extends EditRecord
{
    protected static string $resource = TreatmentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('刪除療程項目'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 