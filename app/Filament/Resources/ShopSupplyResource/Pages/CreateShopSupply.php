<?php

namespace App\Filament\Resources\ShopSupplyResource\Pages;

use App\Filament\Resources\ShopSupplyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShopSupply extends CreateRecord
{
    protected static string $resource = ShopSupplyResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 