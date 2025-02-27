<?php

namespace App\Filament\Resources\SalesStatisticResource\Pages;

use App\Filament\Resources\SalesStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesStatistics extends ListRecords
{
    protected static string $resource = SalesStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('generate_statistics')
                ->label('生成統計數據')
                ->icon('heroicon-o-calculator')
                ->action(function () {
                    // 這裡可以調用生成統計數據的服務
                    // 例如: app(StatisticsGeneratorService::class)->generateDailyStatistics();
                    
                    // 顯示成功通知
                    $this->notify('success', '統計數據已生成');
                }),
        ];
    }
} 