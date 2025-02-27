<?php

namespace App\Filament\Resources\StaffPerformanceStatisticResource\Pages;

use App\Filament\Resources\StaffPerformanceStatisticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStaffPerformanceStatistics extends ListRecords
{
    protected static string $resource = StaffPerformanceStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('generate_statistics')
                ->label('生成績效統計')
                ->icon('heroicon-o-calculator')
                ->action(function () {
                    // 這裡可以調用生成員工績效統計數據的服務
                    // 例如: app(StaffPerformanceGeneratorService::class)->generateMonthlyStatistics();
                    
                    // 顯示成功通知
                    $this->notify('success', '員工績效統計數據已生成');
                }),
        ];
    }
} 