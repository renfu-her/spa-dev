<?php

namespace App\Filament\Resources\StaffCommissionResource\Pages;

use App\Filament\Resources\StaffCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffCommission extends EditRecord
{
    protected static string $resource = StaffCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 