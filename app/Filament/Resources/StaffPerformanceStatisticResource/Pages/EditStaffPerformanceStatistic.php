<?php

namespace App\Filament\Resources\StaffPerformanceStatisticResource\Pages;

use App\Filament\Resources\StaffPerformanceStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffPerformanceStatistic extends EditRecord
{
    protected static string $resource = StaffPerformanceStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 