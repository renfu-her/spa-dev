<?php

namespace App\Filament\Resources\StaffCommissionResource\Pages;

use App\Filament\Resources\StaffCommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStaffCommission extends CreateRecord
{
    protected static string $resource = StaffCommissionResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 