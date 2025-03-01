<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatmentPackageResource\Pages;
use App\Models\TreatmentPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TreatmentPackageResource extends Resource
{
    protected static ?string $model = TreatmentPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = '療程套餐';

    protected static ?string $modelLabel = '療程套餐';

    protected static ?string $pluralModelLabel = '療程套餐';

    protected static ?string $navigationGroup = '療程管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資料')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('套餐名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('套餐描述')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('價格與有效期')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('套餐價格')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('validity_days')
                            ->label('有效天數')
                            ->required()
                            ->numeric()
                            ->default(365),
                        Forms\Components\Toggle::make('members_only')
                            ->label('僅限會員購買')
                            ->default(false),
                        Forms\Components\DatePicker::make('valid_from')
                            ->label('銷售開始日期')
                            ->format('Y-m-d'),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('銷售結束日期')
                            ->format('Y-m-d'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('是否啟用')
                            ->default(true),
                    ]),
                Forms\Components\Section::make('工本費設定')
                    ->schema([
                        Forms\Components\TextInput::make('material_fee')
                            ->label('工本費')
                            ->numeric()
                            ->default(0)
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('material_quantity')
                            ->label('工本數量')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('deduction_times')
                            ->label('扣抵次數')
                            ->numeric()
                            ->default(0),
                    ]),
                Forms\Components\Section::make('療程項目')
                    ->schema([
                        Forms\Components\Repeater::make('treatmentItems')
                            ->label('療程項目')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('treatment_item_id')
                                    ->label('療程項目')
                                    ->relationship('treatmentItems', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('數量')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1),
                            ])
                            ->columns(2)
                            ->defaultItems(0),
                    ]),
                Forms\Components\Section::make('產品項目')
                    ->schema([
                        Forms\Components\Repeater::make('products')
                            ->label('產品項目')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('產品')
                                    ->relationship('products', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('數量')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1),
                            ])
                            ->columns(2)
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('套餐名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('價格')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity_days')
                    ->label('有效天數')
                    ->sortable(),
                Tables\Columns\IconColumn::make('members_only')
                    ->label('僅限會員')
                    ->boolean(),
                Tables\Columns\TextColumn::make('treatmentItems_count')
                    ->label('療程項目數')
                    ->counts('treatmentItems'),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('產品數')
                    ->counts('products'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('啟用狀態')
                    ->boolean(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label('銷售開始日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('銷售結束日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立日期')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新日期')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->label('僅顯示啟用套餐')
                    ->query(fn(Builder $query): Builder => $query->where('is_active', true)),
                Tables\Filters\Filter::make('members_only')
                    ->label('僅顯示會員專屬套餐')
                    ->query(fn(Builder $query): Builder => $query->where('members_only', true)),
            ])
            ->actions([
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
            'index' => Pages\ListTreatmentPackages::route('/'),
            'create' => Pages\CreateTreatmentPackage::route('/create'),
            'edit' => Pages\EditTreatmentPackage::route('/{record}/edit'),
        ];
    }
}
