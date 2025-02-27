<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesStatisticResource\Pages;
use App\Models\SalesStatistic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesStatisticResource extends Resource
{
    protected static ?string $model = SalesStatistic::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationGroup = '統計分析';
    
    protected static ?string $navigationLabel = '銷售統計';

    protected static ?string $modelLabel = '銷售統計';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('統計資訊')
                    ->schema([
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
                        Forms\Components\TextInput::make('total_orders')
                            ->label('訂單總數')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('total_sales')
                            ->label('銷售總額')
                            ->numeric()
                            ->prefix('NT$')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('銷售明細')
                    ->schema([
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
                        Forms\Components\TextInput::make('total_products_sold')
                            ->label('產品銷售數量')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('total_treatments_sold')
                            ->label('療程銷售數量')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('total_packages_sold')
                            ->label('套裝銷售數量')
                            ->numeric()
                            ->required(),
                    ])->columns(3),
                
                Forms\Components\Section::make('付款方式明細')
                    ->schema([
                        Forms\Components\JsonEditor::make('payment_methods_breakdown')
                            ->label('付款方式明細')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('熱銷商品')
                    ->schema([
                        Forms\Components\JsonEditor::make('top_products')
                            ->label('熱銷產品')
                            ->columnSpan(1),
                        Forms\Components\JsonEditor::make('top_treatments')
                            ->label('熱銷療程')
                            ->columnSpan(1),
                        Forms\Components\JsonEditor::make('top_packages')
                            ->label('熱銷套裝')
                            ->columnSpan(1),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                Tables\Columns\TextColumn::make('total_orders')
                    ->label('訂單總數')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('銷售總額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_product_sales')
                    ->label('產品銷售額')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_treatment_sales')
                    ->label('療程銷售額')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_package_sales')
                    ->label('套裝銷售額')
                    ->money('TWD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_products_sold')
                    ->label('產品銷售數量')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_treatments_sold')
                    ->label('療程銷售數量')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_packages_sold')
                    ->label('套裝銷售數量')
                    ->numeric()
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
            'index' => Pages\ListSalesStatistics::route('/'),
            'create' => Pages\CreateSalesStatistic::route('/create'),
            'view' => Pages\ViewSalesStatistic::route('/{record}'),
            'edit' => Pages\EditSalesStatistic::route('/{record}/edit'),
        ];
    }
} 