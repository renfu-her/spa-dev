<?php

namespace App\Filament\Resources\StaffPerformanceStatisticResource\Pages;

use App\Filament\Resources\StaffPerformanceStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffPerformanceStatistic extends CreateRecord
{
    protected static string $resource = StaffPerformanceStatisticResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 