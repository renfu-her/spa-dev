<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffPerformanceStatisticResource\Pages;
use App\Models\StaffPerformanceStatistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffPerformanceStatisticResource extends Resource
{
    protected static ?string $model = StaffPerformanceStatistic::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = '統計分析';
    
    protected static ?string $navigationLabel = '員工績效統計';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('統計資訊')
                    ->schema([
                        Forms\Components\Select::make('staff_id')
                            ->label('員工')
                            ->relationship('staff', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('statistic_date')
                            ->label('統計日期')
                            ->required(),
                        Forms\Components\Select::make('period_type')
                            ->label('統計週期')
                            ->options([
                                'daily' => '每日',
                                'monthly' => '每月',
                                'yearly' => '每年',
                            ])
                            ->required(),
                    ])->columns(3),
                
                Forms\Components\Section::make('銷售績效')
                    ->schema([
                        Forms\Components\TextInput::make('total_sales')
                            ->label('銷售總額')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                        Forms\Components\TextInput::make('total_product_sales')
                            ->label('產品銷售額')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                        Forms\Components\TextInput::make('total_treatment_sales')
                            ->label('療程銷售額')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                        Forms\Components\TextInput::make('total_package_sales')
                            ->label('套裝銷售額')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('操作與獎金')
                    ->schema([
                        Forms\Components\TextInput::make('total_operations')
                            ->label('操作總數')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('total_commission')
                            ->label('獎金總額')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                        Forms\Components\TextInput::make('sales_commission')
                            ->label('銷售獎金')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                        Forms\Components\TextInput::make('operation_commission')
                            ->label('操作獎金')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('熱銷商品')
                    ->schema([
                        Forms\Components\JsonEditor::make('top_products')
                            ->label('熱銷產品')
                            ->columnSpan(1),
                        Forms\Components\JsonEditor::make('top_treatments')
                            ->label('熱銷療程')
                            ->columnSpan(1),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('員工')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statistic_date')
                    ->label('統計日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_type')
                    ->label('統計週期')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'daily' => '每日',
                        'monthly' => '每月',
                        'yearly' => '每年',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'daily',
                        'success' => 'monthly',
                        'warning' => 'yearly',
                    ]),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('銷售總額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_operations')
                    ->label('操作總數')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_commission')
                    ->label('獎金總額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_product_sales')
                    ->label('產品銷售額')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_treatment_sales')
                    ->label('療程銷售額')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_package_sales')
                    ->label('套裝銷售額')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sales_commission')
                    ->label('銷售獎金')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('operation_commission')
                    ->label('操作獎金')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('staff_id')
                    ->label('員工')
                    ->relationship('staff', 'name'),
                Tables\Filters\SelectFilter::make('period_type')
                    ->label('統計週期')
                    ->options([
                        'daily' => '每日',
                        'monthly' => '每月',
                        'yearly' => '每年',
                    ]),
                Tables\Filters\Filter::make('statistic_date')
                    ->form([
                        Forms\Components\DatePicker::make('statistic_date_from')
                            ->label('統計日期從'),
                        Forms\Components\DatePicker::make('statistic_date_until')
                            ->label('統計日期至'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['statistic_date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('statistic_date', '>=', $date),
                            )
                            ->when(
                                $data['statistic_date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('statistic_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffPerformanceStatistics::route('/'),
            'create' => Pages\CreateStaffPerformanceStatistic::route('/create'),
            'view' => Pages\ViewStaffPerformanceStatistic::route('/{record}'),
            'edit' => Pages\EditStaffPerformanceStatistic::route('/{record}/edit'),
        ];
    }
} 