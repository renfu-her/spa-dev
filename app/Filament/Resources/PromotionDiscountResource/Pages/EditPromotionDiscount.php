<?php

namespace App\Filament\Resources\PromotionDiscountResource\Pages;

use App\Filament\Resources\PromotionDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromotionDiscount extends EditRecord
{
    protected static string $resource = PromotionDiscountResource::class;

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