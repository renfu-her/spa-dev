<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatmentCategoryResource\Pages;
use App\Models\TreatmentCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TreatmentCategoryResource extends Resource
{
    protected static ?string $model = TreatmentCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationLabel = '療程分類';
    
    protected static ?string $modelLabel = '療程分類';
    
    protected static ?string $pluralModelLabel = '療程分類';
    
    protected static ?string $navigationGroup = '療程管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('分類名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('分類類型')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'spa' => 'SPA課程',
                        'product' => '商品',
                        'treatment' => '療程',
                        'experience' => '療程體驗',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'spa',
                        'success' => 'product',
                        'warning' => 'treatment',
                        'danger' => 'experience',
                    ]),
                Tables\Columns\TextColumn::make('description')
                    ->label('分類描述')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('treatmentItems_count')
                    ->label('療程項目數量')
                    ->counts('treatmentItems'),
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
                Tables\Filters\SelectFilter::make('type')
                    ->label('分類類型')
                    ->options([
                        'spa' => 'SPA課程',
                        'product' => '商品',
                        'treatment' => '療程',
                        'experience' => '療程體驗',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTreatmentCategories::route('/'),
            'create' => Pages\CreateTreatmentCategory::route('/create'),
            'edit' => Pages\EditTreatmentCategory::route('/{record}/edit'),
        ];
    }
} 