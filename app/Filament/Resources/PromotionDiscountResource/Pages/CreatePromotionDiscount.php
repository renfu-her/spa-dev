<?php

namespace App\Filament\Resources\PromotionDiscountResource\Pages;

use App\Filament\Resources\PromotionDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePromotionDiscount extends CreateRecord
{
    protected static string $resource = PromotionDiscountResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 