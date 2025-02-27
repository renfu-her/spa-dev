<?php

namespace App\Filament\Resources\SalesStatisticResource\Pages;

use App\Filament\Resources\SalesStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesStatistic extends EditRecord
{
    protected static string $resource = SalesStatisticResource::class;

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