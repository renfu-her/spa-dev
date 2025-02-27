<?php

namespace App\Filament\Resources\PromotionDiscountResource\Pages;

use App\Filament\Resources\PromotionDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPromotionDiscounts extends ListRecords
{
    protected static string $resource = PromotionDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
} 