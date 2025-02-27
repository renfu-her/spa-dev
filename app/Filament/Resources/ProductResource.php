<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = '產品管理';
    
    protected static ?string $modelLabel = '產品';
    
    protected static ?string $pluralModelLabel = '產品';
    
    protected static ?string $navigationGroup = '產品管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資料')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('產品分類')
                            ->relationship('category', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('分類名稱')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('分類描述')
                                    ->maxLength(65535),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label('產品名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('產品代碼')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('barcode')
                            ->label('條碼')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('產品描述')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image')
                            ->label('產品圖片')
                            ->image()
                            ->directory('products')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('價格與庫存')
                    ->schema([
                        Forms\Components\TextInput::make('regular_price')
                            ->label('牌價')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('member_price')
                            ->label('會員價')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('庫存數量')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('safety_stock')
                            ->label('安全庫存')
                            ->numeric()
                            ->default(0),
                    ]),
                Forms\Components\Section::make('獎金設定')
                    ->schema([
                        Forms\Components\TextInput::make('sales_commission_rate')
                            ->label('銷售獎金比率')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('operation_commission_rate')
                            ->label('操作獎金比率')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                    ]),
                Forms\Components\Section::make('有效期限')
                    ->schema([
                        Forms\Components\DatePicker::make('valid_from')
                            ->label('有效期間開始')
                            ->format('Y-m-d'),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('有效期間結束')
                            ->format('Y-m-d'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('是否啟用')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('圖片')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('產品名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分類')
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('產品代碼')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regular_price')
                    ->label('牌價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('member_price')
                    ->label('會員價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('庫存數量')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('啟用狀態')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('產品分類')
                    ->relationship('category', 'name'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('庫存不足')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('stock_quantity', '<', 'safety_stock')),
                Tables\Filters\Filter::make('is_active')
                    ->label('僅顯示啟用產品')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
} 