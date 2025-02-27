<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopSupplyResource\Pages;
use App\Models\ShopSupply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShopSupplyResource extends Resource
{
    protected static ?string $model = ShopSupply::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    
    protected static ?string $navigationLabel = '沙貨管理';
    
    protected static ?string $modelLabel = '沙貨';
    
    protected static ?string $pluralModelLabel = '沙貨';
    
    protected static ?string $navigationGroup = '庫存管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('沙貨資料')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('產品')
                            ->relationship('product', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('batch_number')
                            ->label('批號')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('total_quantity')
                            ->label('總數量')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->reactive(),
                        Forms\Components\TextInput::make('opened_quantity')
                            ->label('已開瓶數量')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $total = $get('total_quantity');
                                $opened = $get('opened_quantity');
                                $set('unopened_quantity', max(0, $total - $opened));
                            }),
                        Forms\Components\TextInput::make('unopened_quantity')
                            ->label('未開瓶數量')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $total = $get('total_quantity');
                                $unopened = $get('unopened_quantity');
                                $set('opened_quantity', max(0, $total - $unopened));
                            }),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('有效期限')
                            ->format('Y-m-d'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('產品名稱')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch_number')
                    ->label('批號')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('總數量')
                    ->sortable(),
                Tables\Columns\TextColumn::make('opened_quantity')
                    ->label('已開瓶數量')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unopened_quantity')
                    ->label('未開瓶數量')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('有效期限')
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
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('產品')
                    ->relationship('product', 'name'),
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('即將到期')
                    ->query(fn (Builder $query): Builder => $query->whereDate('expiry_date', '<=', now()->addDays(30))),
            ])
            ->actions([
                Tables\Actions\Action::make('open_bottle')
                    ->label('開瓶')
                    ->icon('heroicon-o-beaker')
                    ->color('warning')
                    ->form([
                        Forms\Components\DateTimePicker::make('opened_at')
                            ->label('開瓶時間')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('notes')
                            ->label('備註'),
                    ])
                    ->action(function (ShopSupply $record, array $data): void {
                        if ($record->unopened_quantity > 0) {
                            $record->openingRecords()->create([
                                'opened_by' => auth()->id(),
                                'opened_at' => $data['opened_at'],
                                'notes' => $data['notes'],
                            ]);
                            
                            $record->update([
                                'opened_quantity' => $record->opened_quantity + 1,
                                'unopened_quantity' => $record->unopened_quantity - 1,
                            ]);
                        }
                    })
                    ->visible(fn (ShopSupply $record): bool => $record->unopened_quantity > 0),
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
            'index' => Pages\ListShopSupplies::route('/'),
            'create' => Pages\CreateShopSupply::route('/create'),
            'edit' => Pages\EditShopSupply::route('/{record}/edit'),
        ];
    }
} 