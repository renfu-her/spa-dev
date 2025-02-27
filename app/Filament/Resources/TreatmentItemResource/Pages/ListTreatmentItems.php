<?php

namespace App\Filament\Resources\TreatmentItemResource\Pages;

use App\Filament\Resources\TreatmentItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreatmentItems extends ListRecords
{
    protected static string $resource = TreatmentItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('新增療程項目'),
        ];
    }
} 