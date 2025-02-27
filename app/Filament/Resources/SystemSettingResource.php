<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?string $navigationGroup = '系統管理';
    
    protected static ?string $navigationLabel = '系統設定';

    protected static ?string $modelLabel = '系統設定';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('系統設定')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('設定鍵')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('type')
                            ->label('設定類型')
                            ->options([
                                'string' => '字串',
                                'integer' => '整數',
                                'float' => '浮點數',
                                'boolean' => '布林值',
                                'json' => 'JSON',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('value')
                            ->label('設定值')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('設定鍵')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('設定類型')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'string' => '字串',
                        'integer' => '整數',
                        'float' => '浮點數',
                        'boolean' => '布林值',
                        'json' => 'JSON',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'string',
                        'success' => 'integer',
                        'warning' => 'float',
                        'danger' => 'boolean',
                        'gray' => 'json',
                    ]),
                Tables\Columns\TextColumn::make('value')
                    ->label('設定值')
                    ->limit(50),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述')
                    ->limit(50)
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('type')
                    ->label('設定類型')
                    ->options([
                        'string' => '字串',
                        'integer' => '整數',
                        'float' => '浮點數',
                        'boolean' => '布林值',
                        'json' => 'JSON',
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
} 