<?php

namespace App\Filament\Resources\TreatmentCategoryResource\Pages;

use App\Filament\Resources\TreatmentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTreatmentCategory extends EditRecord
{
    protected static string $resource = TreatmentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('刪除分類'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 