<?php

namespace App\Filament\Resources\SalesStatisticResource\Pages;

use App\Filament\Resources\SalesStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesStatistic extends CreateRecord
{
    protected static string $resource = SalesStatisticResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 