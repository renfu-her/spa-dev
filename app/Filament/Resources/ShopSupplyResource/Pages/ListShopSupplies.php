<?php

namespace App\Filament\Resources\ShopSupplyResource\Pages;

use App\Filament\Resources\ShopSupplyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShopSupplies extends ListRecords
{
    protected static string $resource = ShopSupplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('新增沙貨'),
        ];
    }
} 