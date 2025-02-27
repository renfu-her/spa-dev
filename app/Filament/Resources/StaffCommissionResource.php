<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffCommissionResource\Pages;
use App\Models\StaffCommission;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffCommissionResource extends Resource
{
    protected static ?string $model = StaffCommission::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static ?string $navigationGroup = '獎金管理';
    
    protected static ?string $navigationLabel = '員工獎金';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('獎金資訊')
                    ->schema([
                        Forms\Components\Select::make('staff_id')
                            ->label('員工')
                            ->relationship('staff', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('金額')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\DatePicker::make('commission_date')
                            ->label('獎金日期')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('commission_type')
                            ->label('獎金類型')
                            ->options([
                                'sales' => '銷售獎金',
                                'operation' => '操作獎金',
                                'bonus' => '額外獎金',
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('關聯資訊')
                    ->schema([
                        Forms\Components\Select::make('commissionable_type')
                            ->label('關聯類型')
                            ->options([
                                'App\\Models\\SalesOrder' => '銷售訂單',
                                'App\\Models\\TreatmentUsageRecord' => '療程操作記錄',
                                'App\\Models\\CommissionSetting' => '獎金設定',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\Select::make('commissionable_id')
                            ->label('關聯項目')
                            ->options(function (Forms\Get $get) {
                                $type = $get('commissionable_type');
                                if (!$type) return [];
                                
                                $model = new $type;
                                $query = $model::query();
                                
                                if ($type === 'App\\Models\\SalesOrder') {
                                    return $query->pluck('order_number', 'id')->toArray();
                                } elseif ($type === 'App\\Models\\TreatmentUsageRecord') {
                                    return $query->pluck('id', 'id')->toArray();
                                } elseif ($type === 'App\\Models\\CommissionSetting') {
                                    return $query->pluck('name', 'id')->toArray();
                                }
                                
                                return [];
                            })
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('支付資訊')
                    ->schema([
                        Forms\Components\Toggle::make('is_paid')
                            ->label('已支付')
                            ->default(false),
                        Forms\Components\DatePicker::make('paid_date')
                            ->label('支付日期')
                            ->visible(fn (Forms\Get $get) => $get('is_paid')),
                    ]),
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_date')
                    ->label('獎金日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('commission_type')
                    ->label('獎金類型')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'sales' => '銷售獎金',
                        'operation' => '操作獎金',
                        'bonus' => '額外獎金',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'sales',
                        'primary' => 'operation',
                        'warning' => 'bonus',
                    ]),
                Tables\Columns\TextColumn::make('commissionable_type')
                    ->label('關聯類型')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'App\\Models\\SalesOrder' => '銷售訂單',
                        'App\\Models\\TreatmentUsageRecord' => '療程操作記錄',
                        'App\\Models\\CommissionSetting' => '獎金設定',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('已支付')
                    ->boolean(),
                Tables\Columns\TextColumn::make('paid_date')
                    ->label('支付日期')
                    ->date()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('commission_type')
                    ->label('獎金類型')
                    ->options([
                        'sales' => '銷售獎金',
                        'operation' => '操作獎金',
                        'bonus' => '額外獎金',
                    ]),
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('支付狀態'),
                Tables\Filters\Filter::make('commission_date')
                    ->form([
                        Forms\Components\DatePicker::make('commission_date_from')
                            ->label('獎金日期從'),
                        Forms\Components\DatePicker::make('commission_date_until')
                            ->label('獎金日期至'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['commission_date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('commission_date', '>=', $date),
                            )
                            ->when(
                                $data['commission_date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('commission_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('標記為已支付')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (StaffCommission $record) => !$record->is_paid)
                    ->action(function (StaffCommission $record) {
                        $record->update([
                            'is_paid' => true,
                            'paid_date' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_as_paid')
                        ->label('標記為已支付')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Builder $query) {
                            $query->update([
                                'is_paid' => true,
                                'paid_date' => now(),
                            ]);
                        }),
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
            'index' => Pages\ListStaffCommissions::route('/'),
            'create' => Pages\CreateStaffCommission::route('/create'),
            'edit' => Pages\EditStaffCommission::route('/{record}/edit'),
        ];
    }
} 