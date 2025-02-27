<?php

namespace App\Filament\Resources\StaffPerformanceStatisticResource\Pages;

use App\Filament\Resources\StaffPerformanceStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStaffPerformanceStatistic extends ViewRecord
{
    protected static string $resource = StaffPerformanceStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
} 