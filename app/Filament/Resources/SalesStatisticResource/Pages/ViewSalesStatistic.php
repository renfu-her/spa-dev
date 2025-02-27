<?php

namespace App\Filament\Resources\SalesStatisticResource\Pages;

use App\Filament\Resources\SalesStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesStatistic extends ViewRecord
{
    protected static string $resource = SalesStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
} 