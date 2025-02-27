<?php

namespace App\Filament\Resources\ShopSupplyResource\Pages;

use App\Filament\Resources\ShopSupplyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShopSupply extends EditRecord
{
    protected static string $resource = ShopSupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('刪除沙貨'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 