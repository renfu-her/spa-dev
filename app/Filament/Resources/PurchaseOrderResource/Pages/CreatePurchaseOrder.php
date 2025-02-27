<?php

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseOrder extends CreateRecord
{
    protected static string $resource = PurchaseOrderResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 計算總金額
        $totalAmount = 0;
        foreach ($data['items'] ?? [] as $item) {
            $totalAmount += $item['subtotal'];
        }
        
        $data['total_amount'] = $totalAmount;
        $data['final_amount'] = $totalAmount + ($data['tax_amount'] ?? 0) - ($data['discount_amount'] ?? 0);
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 