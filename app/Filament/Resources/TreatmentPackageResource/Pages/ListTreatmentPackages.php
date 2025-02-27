<?php

namespace App\Filament\Resources\TreatmentPackageResource\Pages;

use App\Filament\Resources\TreatmentPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTreatmentPackages extends ListRecords
{
    protected static string $resource = TreatmentPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('新增療程套餐'),
        ];
    }
} 