<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatmentItemResource\Pages;
use App\Models\TreatmentItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TreatmentItemResource extends Resource
{
    protected static ?string $model = TreatmentItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = '療程項目';
    
    protected static ?string $modelLabel = '療程項目';
    
    protected static ?string $pluralModelLabel = '療程項目';
    
    protected static ?string $navigationGroup = '療程管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資料')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('療程分類')
                            ->relationship('category', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('分類名稱')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->label('分類類型')
                                    ->required()
                                    ->options([
                                        'spa' => 'SPA課程',
                                        'product' => '商品',
                                        'treatment' => '療程',
                                        'experience' => '療程體驗',
                                    ]),
                                Forms\Components\Textarea::make('description')
                                    ->label('分類描述')
                                    ->maxLength(65535),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label('項目名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('項目描述')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('價格設定')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('牌價')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('member_price')
                            ->label('會員價')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('operation_commission_rate')
                            ->label('操作獎金比率')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                    ]),
                Forms\Components\Section::make('其他設定')
                    ->schema([
                        Forms\Components\Toggle::make('is_experience')
                            ->label('是否為體驗項目')
                            ->default(false),
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('療程時長(分鐘)')
                            ->numeric()
                            ->default(60),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('項目名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分類')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('牌價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('member_price')
                    ->label('會員價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_experience')
                    ->label('體驗項目')
                    ->boolean(),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('時長(分鐘)')
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('療程分類')
                    ->relationship('category', 'name'),
                Tables\Filters\Filter::make('is_experience')
                    ->label('僅顯示體驗項目')
                    ->query(fn (Builder $query): Builder => $query->where('is_experience', true)),
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
            'index' => Pages\ListTreatmentItems::route('/'),
            'create' => Pages\CreateTreatmentItem::route('/create'),
            'edit' => Pages\EditTreatmentItem::route('/{record}/edit'),
        ];
    }
} 