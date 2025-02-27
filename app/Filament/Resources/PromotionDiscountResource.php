<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromotionDiscountResource\Pages;
use App\Models\PromotionDiscount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromotionDiscountResource extends Resource
{
    protected static ?string $model = PromotionDiscount::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = '促銷管理';

    protected static ?string $navigationLabel = '促銷折扣';

    protected static ?string $modelLabel = '促銷折扣';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('促銷名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('discount_type')
                            ->label('折扣類型')
                            ->options([
                                'treatment' => '療程折扣',
                                'product' => '產品折扣',
                                'package' => '套裝折扣',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('threshold_amount')
                            ->label('門檻金額')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('max_discount_amount')
                            ->label('最大折扣金額')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                    ])->columns(2),

                Forms\Components\Section::make('超額折抵設定')
                    ->schema([
                        Forms\Components\Toggle::make('can_exceed_limit')
                            ->label('允許超額折抵')
                            ->default(false),
                        Forms\Components\TextInput::make('exceed_amount')
                            ->label('超額折抵金額')
                            ->numeric()
                            ->prefix('NT$')
                            ->visible(fn(Forms\Get $get) => $get('can_exceed_limit')),
                    ]),

                Forms\Components\Section::make('有效期限')
                    ->schema([
                        Forms\Components\DatePicker::make('valid_from')
                            ->label('開始日期')
                            ->required(),
                        Forms\Components\DatePicker::make('valid_until')
                            ->label('結束日期')
                            ->required()
                            ->afterOrEqual('valid_from'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('啟用')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('促銷名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('折扣類型')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'treatment' => '療程折扣',
                        'product' => '產品折扣',
                        'package' => '套裝折扣',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'treatment',
                        'success' => 'product',
                        'warning' => 'package',
                    ]),
                Tables\Columns\TextColumn::make('threshold_amount')
                    ->label('門檻金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_discount_amount')
                    ->label('最大折扣金額')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('can_exceed_limit')
                    ->label('允許超額折抵')
                    ->boolean(),
                Tables\Columns\TextColumn::make('valid_from')
                    ->label('開始日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->label('結束日期')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('啟用')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('折扣類型')
                    ->options([
                        'treatment' => '療程折扣',
                        'product' => '產品折扣',
                        'package' => '套裝折扣',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('啟用狀態'),
                Tables\Filters\TernaryFilter::make('can_exceed_limit')
                    ->label('允許超額折抵'),
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
            'index' => Pages\ListPromotionDiscounts::route('/'),
            'create' => Pages\CreatePromotionDiscount::route('/create'),
            'edit' => Pages\EditPromotionDiscount::route('/{record}/edit'),
        ];
    }
}
